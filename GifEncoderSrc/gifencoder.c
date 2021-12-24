#include <stdio.h>
#include <stdlib.h>

#include <math.h>
#include <time.h>

#include "globals.h"
#include "gifencoder.h"
#include "lzwCompressor.h"
#include "sorting.h"

#define GIF_HEADER "GIF89a"

//Weights for finding closest color in table
#define WEIGHT_H 0.6
#define WEIGHT_S 0.2
#define WEIGHT_V 0.2

//7 max -> means 2^(res + 1) as tablesize
#define colorResolution 7
#define pixelAspectRatio 0

#define allocateAtOnce 100

//informations from http://www.matthewflickinger.com/lab/whatsinagif/bits_and_bytes.asp

//private functions definitions
void littleEndianDump(char* writeInto, int* writeSize, int32_t data, int8_t bytes);
void bigEndianDump(char* writeInto, int* writeSize, int32_t data, int8_t bytes);
void gifencoder_createColorTable(GIF_STRUCTURE* gif);
int find_closest_matching(GIF_STRUCTURE* gif, int32_t color, float* minDiff);
void to_HSV(int32_t asRGB, float* h, float* s, float* v);
void gifencoder_writeColorTable(FILE* targetFile, GIF_STRUCTURE* gif);
void gifencoder_writeLoopExtension(FILE* targetFile, GIF_STRUCTURE* gif);
void gifencoder_writeImage(char** target, int* targetSize, int* maxTargetSize, int globColorTableSizeBit, GIF_STRUCTURE* gif, int number);
void gifencoder_writeDelayHeader(char** target, int* targetSize, int* maxTargetSize, GIF_STRUCTURE* gif, int transparentIdx, int isFirst);
void getChangedReagion(int32_t* imageData, int32_t* prevImageData, int width, int height, int* xs, int* xe, int* ys, int* ye);
void gifencoder_lzwCompressImage(char** target, int* targetSize, int* maxTargetSize, GIF_STRUCTURE* gif, int32_t* imageData,
int32_t* prevImageData, int transparentIdx, int globColorTableSizeBit, int xs, int xe, int ys, int ye);


//public functions
/**
 * Initializes a gif structure that can be filled afterwards
 */
GIF_STRUCTURE* gifencoder_create(unsigned width, unsigned height, unsigned delay, LodePNGColorType colType) {
    GIF_STRUCTURE* retval;
    retval = (GIF_STRUCTURE*) calloc(1, sizeof(GIF_STRUCTURE));

    retval->height = height;
    retval->width = width;
    retval->images = 0;
    retval->numImages = 0;
    retval->maxImages = 0;
    retval->delay = delay;
    retval->colType = colType;
    retval->colorHash = hashmap_new(allocateAtOnce);
    return retval;
}

/**
 * Adds a given imageData to the GIF (just add will not perform any calculations)
 */
void gifencoder_addImage(GIF_STRUCTURE* gif, unsigned char* imageData) {
    if(! gif->images) {
        gif->images = calloc(allocateAtOnce, sizeof(int32_t*));
        gif->maxImages = allocateAtOnce;
        gif->numImages = 0;
    } else if(gif->numImages == gif->maxImages) {
        //full -> realloc
        gif->maxImages += allocateAtOnce;
        gif->images = realloc(gif->images, sizeof(int32_t*) * (gif->maxImages));
        for(int inner = gif->maxImages - allocateAtOnce; inner < gif->maxImages; inner++) {
            gif->images[inner] = 0;
        }
    }

    int32_t color;
    int32_t* newImageData = calloc(gif->width * gif->height, sizeof(int32_t));
    int r, g, b;
    for(int i = 0; i < gif->width * gif->height; i++) {
        if(gif->colType == LCT_RGBA) {
            //downsample to 16 Bit color space
            r = imageData[i * 4] & 0xF8;
            g = imageData[i * 4 + 1] & 0xFC;
            b = imageData[i * 4 + 2] & 0xF8;
        } else {
            printf("Unknown color type %u\n", gif->colType);
            exit(1);
        }

        color = (r << 16) + (g << 8) + b;
        newImageData[i] = color;

        struct bucket* inMap = hashmap_get(gif->colorHash, color);
        if(inMap == NULL) {
            hashmap_set(gif->colorHash, color, 1);
        } else {
            inMap->value++;
        }
    }

    gif->images[gif->numImages] = newImageData;
    gif->numImages++;
}

/**
 * Creates and writes the gif
 * uses the data found in the GIF_STRUCTURE
 * @param targetFilePath the file we want to write into
 */
void gifencoder_encode(GIF_STRUCTURE* gif, char* targetFilePath) {
    #ifdef TIMING_DEBUG
    printf("MapSize %ld\n", hashmap_count(gif->colorHash));
    clock_t start = clock();
    #endif // TIMING_DEBUG
    FILE* targetFile = fopen(targetFilePath, "w");
    if(targetFile == NULL) {
        printf("unable to open output file");
        exit(1);
    }

    char* temp = calloc(100, sizeof(char));
    int tempSize = 0;

    fprintf(targetFile, GIF_HEADER);
    littleEndianDump(temp, &tempSize, gif->width, 2);
    littleEndianDump(temp, &tempSize, gif->height, 2);

    #ifdef TIMING_DEBUG
    clock_t preCol = clock();
    #endif // TIMING_DEBUG
    gifencoder_createColorTable(gif);
    #ifdef TIMING_DEBUG
    clock_t postCol = clock();
    printf("Timing precol: %ld u sec / globalColTbl: %ld u sec\n", (preCol - start), (postCol - preCol));
    #endif // TIMING_DEBUG

    int globColTblSizeBit = (int) ceil(log2f(gif->globalColorTableSize) - 1);
    gif->GIFglobalColorTableSize = 2 << globColTblSizeBit;


    //don't ask
    //global color table; max color Resolution; Thing is sorted
    temp[tempSize++] = 0b10001000 + globColTblSizeBit + (colorResolution << 4);
    //background color index
    temp[tempSize++] = 0;
    //pixel aspect ratio
    temp[tempSize++] = pixelAspectRatio;
    fwrite(temp, tempSize, 1, targetFile);

    gifencoder_writeColorTable(targetFile, gif);
    gifencoder_writeLoopExtension(targetFile, gif);

    char* encodedImage = malloc(1000 * sizeof(char));
    int encodedImageSize = 0;
    int maxEncodedImageSize = 1000;
    for(int i = 0; i < gif->numImages; i++) {
        printf("Write %d\n", i);
        fflush(stdout);
        #ifdef TIMING_DEBUG
        clock_t imageStart = clock();
        #endif // TIMING_DEBUG
        gifencoder_writeImage(&encodedImage, &encodedImageSize, &maxEncodedImageSize, globColTblSizeBit, gif, i);
        #ifdef TIMING_DEBUG
        clock_t imageEnd = clock();
        #endif // TIMING_DEBUG

        fwrite(encodedImage, encodedImageSize, 1, targetFile);
        encodedImageSize = 0;
        #ifdef TIMING_DEBUG
        clock_t imageWrite = clock();
        printf("Writing Image: prepare %ld u sec - write %ld u sec - ges %ld u sec\n", (imageEnd - imageStart), (imageWrite - imageEnd), (imageWrite - imageStart));
        #endif // TIMING_DEBUG
    }

    fprintf(targetFile, "%c", 0x3B);

    fclose(targetFile);
    targetFile = 0;
    free(encodedImage);
    free(temp);
    temp = 0;
}

void gifencoder_free(GIF_STRUCTURE* gif) {
    for(int i = 0; i < gif->numImages; i++) {
        if(gif->images[i]) {
            free(gif->images[i]);
        }
    }
    free(gif->images);
    hashmap_free(gif->colorHash);

    hashmap_free(gif->pictureColorMap);

    free(gif->globalColorTable);
    free(gif->globalHSVColorTableH);
    free(gif->globalHSVColorTableS);
    free(gif->globalHSVColorTableV);
    free(gif);
}

struct colorContainer {
    int32_t* colors;
    int32_t* colorAmount;
    int32_t cur;
};

bool mapToArray(struct bucket* item, void *udata) {
    struct colorContainer *container = udata;
    container->colors[container->cur] = item->hash;
    container->colorAmount[container->cur] = item->value;
    container->cur++;
    return true;
}

//private function code
void gifencoder_createColorTable(GIF_STRUCTURE* gif) {
    #ifdef TIMING_DEBUG
    clock_t start = clock();
    #endif // TIMING_DEBUG

    int colorsSize = hashmap_count(gif->colorHash);
    struct colorContainer container;
    container.colors = calloc(colorsSize, sizeof(int32_t));
    container.colorAmount = calloc(colorsSize, sizeof(int32_t));
    container.cur = 0;
    hashmap_scan(gif->colorHash, mapToArray, &container);
    int32_t* colors = container.colors;
    int32_t* colorAmount = container.colorAmount;

    sort_data(colorAmount, colors, colorsSize);

    #ifdef TIMING_DEBUG
    clock_t sort = clock();
    #endif // TIMING_DEBUG

    //write into gif element
    gif->pictureColorMap = hashmap_new(colorsSize + 1);

    gif->globalColorTable = calloc(256, sizeof(int32_t));
    gif->globalHSVColorTableH = calloc(256, sizeof(float));
    gif->globalHSVColorTableS = calloc(256, sizeof(float));
    gif->globalHSVColorTableV = calloc(256, sizeof(float));
    gif->globalColorTableSize = 0;

    int i = 0, ignCnt = 0;
    float h, s, v, minDiff;
    int32_t* ignColorCache = calloc(colorsSize, sizeof(int32_t));

    for(; i < colorsSize && gif->globalColorTableSize < 255; i++) {
        find_closest_matching(gif, colors[i], &minDiff);
        if(minDiff < 0.02) {
            //similar color is already in the table. Ignore for now
            ignColorCache[ignCnt] = colors[i];
            ignCnt++;
            continue;
        }

        to_HSV(colors[i], &h, &s, &v);
        gif->globalColorTable[gif->globalColorTableSize] = colors[i];
        gif->globalHSVColorTableH[gif->globalColorTableSize] = h;
        gif->globalHSVColorTableS[gif->globalColorTableSize] = s;
        gif->globalHSVColorTableV[gif->globalColorTableSize] = v;

        hashmap_set(gif->pictureColorMap, colors[i], gif->globalColorTableSize);
        gif->globalColorTableSize++;
    }
    gif->globalColorTableSize++; //transparency

    for(; i < colorsSize; i++) {
        hashmap_set(gif->pictureColorMap, colors[i], find_closest_matching(gif, colors[i], &minDiff));
    }

    while(ignCnt > 0 && gif->globalColorTableSize < 255) {
        minDiff = 10;
        int minAt = -1, curMin;
        float curMinDiff = 10;
        for(i = 0; i < ignCnt; i++) {
            curMin = find_closest_matching(gif, ignColorCache[i], &minDiff);
            if(minDiff < curMinDiff) {
                curMinDiff = minDiff;
                minAt = curMin;
            }
        }

        to_HSV(ignColorCache[minAt], &h, &s, &v);
        gif->globalColorTable[gif->globalColorTableSize] = ignColorCache[minAt];
        gif->globalHSVColorTableH[gif->globalColorTableSize] = h;
        gif->globalHSVColorTableS[gif->globalColorTableSize] = s;
        gif->globalHSVColorTableV[gif->globalColorTableSize] = v;

        hashmap_set(gif->pictureColorMap, ignColorCache[minAt], gif->globalColorTableSize);
        gif->globalColorTableSize++;

        ignCnt--;
        for(i = minAt; i < ignCnt; i++) {
            ignColorCache[i] = ignColorCache[i + 1];
        }
    }

    for(i = 0; i < ignCnt; i++) {
        hashmap_set(gif->pictureColorMap, ignColorCache[i], find_closest_matching(gif, ignColorCache[i], &minDiff));
    }

//    for(int i = 0; i < colorsSize; i++) {
//        printf("Color: %d / Num: %d / NumTbl: %d\n", colors[i], colorAmount[i], gif->pictureColorMapValues[i]);
//    }
    free(ignColorCache);
    free(colorAmount);
    free(colors);

    #ifdef TIMING_DEBUG
    clock_t end = clock();

    printf("Timing color: sort: %ld u sec / other: %ld u sec\n", (sort - start), (end - sort));
    #endif // TIMING_DEBUG
}

void gifencoder_writeColorTable(FILE* targetFile, GIF_STRUCTURE* gif) {
    char* temp = calloc(gif->GIFglobalColorTableSize * 3 + 2, sizeof(char));
    int tempSize = 0;

    for(int i = 0; i < gif->GIFglobalColorTableSize; i++) {
        if(i < gif->globalColorTableSize) {
            bigEndianDump(temp, &tempSize, gif->globalColorTable[i], 3);
        } else {
            //fill with 0
            bigEndianDump(temp, &tempSize, 0, 3);
        }
    }
    fwrite(temp, tempSize, 1, targetFile);
    free(temp);
}

void gifencoder_writeLoopExtension(FILE* targetFile, GIF_STRUCTURE* gif) {
    char* temp = calloc(25, sizeof(char));
    int tempSize = 0;

    temp[tempSize++] = 0x21;
    temp[tempSize++] = 0xFF;
    temp[tempSize++] = 0x0B; // lenght of NETSCAPE2.0

    temp[tempSize++] = 'N'; //N
    temp[tempSize++] = 'E'; //E
    temp[tempSize++] = 'T'; //T
    temp[tempSize++] = 'S'; //S
    temp[tempSize++] = 'C'; //C
    temp[tempSize++] = 'A'; //A
    temp[tempSize++] = 'P'; //P
    temp[tempSize++] = 'E'; //E
    temp[tempSize++] = '2'; //2
    temp[tempSize++] = '.'; //.
    temp[tempSize++] = '0'; //0

    temp[tempSize++] = 0x03;
    temp[tempSize++] = 0x01;
    littleEndianDump(temp, &tempSize, 0, 2); //amount of looping 0 = unlimited
    temp[tempSize++] = 0x00;

    fwrite(temp, tempSize, 1, targetFile);
    free(temp);
}

void gifencoder_writeImage(char** target, int* targetSize, int* maxTargetSize, int globColorTableSizeBit, GIF_STRUCTURE* gif, int number) {
    int32_t* imageData = gif->images[number];
    int32_t* prevImageData = 0;

    if(number > 0) {
        prevImageData = gif->images[number - 1];
    }

    //last one is transparency
    int transparentIdx = gif->globalColorTableSize - 1;

    gifencoder_writeDelayHeader(target, targetSize, maxTargetSize, gif, transparentIdx, number == 0);
    (*target)[(*targetSize)++] = 0x2C;

    int xs, xe, ys, ye;
    if(! prevImageData) {
        littleEndianDump((*target), targetSize, 0, 2); //left
        littleEndianDump((*target), targetSize, 0, 2); //top
        littleEndianDump((*target), targetSize, gif->width, 2); //width
        littleEndianDump((*target), targetSize, gif->height, 2); //height
        xs = 0; ys = 0; xe = gif->height - 1; ye = gif->width - 1;
    } else {
        getChangedReagion(imageData, prevImageData, gif->width, gif->height, &xs, &xe, &ys, &ye);
        littleEndianDump((*target), targetSize, ys, 2); //left
        littleEndianDump((*target), targetSize, xs, 2); //top
        littleEndianDump((*target), targetSize, ye - ys + 1, 2); //width
        littleEndianDump((*target), targetSize, xe - xs + 1, 2); //height
    }

    (*target)[(*targetSize)++] = 0x00;
    gifencoder_lzwCompressImage(target, targetSize, maxTargetSize, gif, imageData, prevImageData, transparentIdx, globColorTableSizeBit, xs, xe, ys, ye);
}

void gifencoder_writeDelayHeader(char** target, int* targetSize, int* maxTargetSize, GIF_STRUCTURE* gif, int transparentIdx, int isFirst) {
    (*target)[(*targetSize)++] = 0x21;
    (*target)[(*targetSize)++] = 0xF9;
    (*target)[(*targetSize)++] = 0x04;

    if(isFirst) {
        (*target)[(*targetSize)++] = 0x06; //draw on bg / wait for user
    } else {
        (*target)[(*targetSize)++] = 0x05; //use transparency / show on top
    }

    littleEndianDump((*target), targetSize, gif->delay, 2);
    (*target)[(*targetSize)++] = transparentIdx & 0xFF;
    (*target)[(*targetSize)++] = 0x00;
}

void getChangedReagion(int32_t* imageData, int32_t* prevImageData, int width, int height, int* xs, int* xe, int* ys, int* ye) {
    *xs = 0;
    *ys = 0;
    *xe = height - 1;
    *ye = width - 1;

    //xs
    for(int i = *xs; i <= *xe; i++) {
        for(int j = *ys; j <= *ye; j++) {
            if(imageData[i * width + j] != prevImageData[i * width + j]) {
                *xs = i;
                break;
            }
        }
        if(*xs > 0) break;
    }

    //ys
    for(int j = *ys; j <= *ye; j++) {
        for(int i = *xs; i <= *xe; i++) {
            if(imageData[i * width + j] != prevImageData[i * width + j]) {
                *ys = j;
                break;
            }
        }
        if(*ys > 0) break;
    }

    //xe
    for(int i = *xe; i > *xs; i--) {
        for(int j = *ys; j <= *ye; j++) {
            if(imageData[i * width + j] != prevImageData[i * width + j]) {
                *xe = i;
                break;
            }
        }
        if(*xe < height - 1) break;
    }

    //ye
    for(int j = *ye; j > *ys; j--) {
        for(int i = *xs; i <= *xe; i++) {
            if(imageData[i * width + j] != prevImageData[i * width + j]) {
                *ye = j;
                break;
            }
        }
        if(*ye < width - 1) break;
    }
}

void gifencoder_lzwCompressImage(char** target, int* targetSize, int* maxTargetSize, GIF_STRUCTURE* gif, int32_t* imageData,
    int32_t* prevImageData, int transparentIdx, int globColorTableSizeBit, int xs, int xe, int ys, int ye) {
    LZW_STRUCTURE* compressor = lzwCompressor_create(globColorTableSizeBit);

    if(prevImageData) {
        (*target)[(*targetSize)++] = globColorTableSizeBit + 1;
        for(int i = xs; i <= xe; i++) {
            for(int j = ys; j <= ye; j++) {
                if(imageData[i * gif->width + j] != prevImageData[i * gif->width + j]) {
                    int32_t color = imageData[i * gif->width + j];
                    struct bucket* colorID = hashmap_get(gif->pictureColorMap, color);
                    if(colorID == NULL) {
                        printf("Unable to find color!!! %u", color);
                        exit(0);
                    }
                    lzwCompressor_append(compressor, colorID->value);
                } else {
                    lzwCompressor_append(compressor, transparentIdx);
                }
            }
        }
    } else {
        (*target)[(*targetSize)++] = globColorTableSizeBit + 1;
        for(int i = xs; i <= xe; i++) {
            for(int j = ys; j <= ye; j++) {
                int32_t color = imageData[i * gif->width + j];
                struct bucket* colorID = hashmap_get(gif->pictureColorMap, color);
                if(colorID == NULL) {
                    printf("Unable to find color!!! %u", color);
                    exit(0);
                }
                lzwCompressor_append(compressor, colorID->value);
            }
        }
    }
    lzwCompressor_finish(compressor);

    char* data = 0;
    int dataSize = 0;
    lzwCompressor_getCompressed(compressor, &data, &dataSize);

    int curPos = 0;
    while(dataSize - curPos > 255) {
        if(*maxTargetSize < *targetSize + 256) {
            *maxTargetSize += 1000;
            *target = realloc(*target, *maxTargetSize * sizeof(char));
        }

        (*target)[(*targetSize)++] = 0xFF;
        memcpy((*target) + *targetSize, data + curPos, 255);
        *targetSize += 255;
        curPos += 255;
    }
    if(*maxTargetSize < *targetSize + 256) {
        *maxTargetSize += 1000;
        *target = realloc(*target, *maxTargetSize * sizeof(char));
    }

    (*target)[(*targetSize)++] = (dataSize - curPos) & 0xFF;
    memcpy((*target) + *targetSize, data + curPos, (dataSize - curPos) & 0xFF);
    *targetSize += (dataSize - curPos) & 0xFF;

    (*target)[(*targetSize)++] = 0x00;

    lzwCompressor_free(compressor);
}

int find_closest_matching(GIF_STRUCTURE* gif, int32_t color, float* minDiff) {
    float h, s,v;
    to_HSV(color, &h, &s, &v);

    float diff;
    *minDiff = 10;
    int closest = 0;

    for(int i = 0; i < gif->globalColorTableSize; i++) {
        diff = fabsf( gif->globalHSVColorTableH[i] - h ) * WEIGHT_H;
        diff += fabsf( gif->globalHSVColorTableS[i] - s ) * WEIGHT_S;
        diff += fabsf( gif->globalHSVColorTableV[i] - v ) * WEIGHT_V;

        if(diff < *minDiff) {
            *minDiff = diff;
            closest = i;
        }
    }

    return closest;
}

/**
 * rgb to HSV
 * HSV output from 0 to 1
 */
void to_HSV(int32_t asRGB, float* h, float* s, float* v) {
    float rFloat = ((float) ((asRGB >> 16) & 0xFF)) / 255;
    float gFloat = ((float) ((asRGB >> 8) & 0xFF)) / 255;
    float bFloat = ((float) (asRGB & 0xFF)) / 255;

    float maxFloat = rFloat;
    if(gFloat > maxFloat) maxFloat = gFloat;
    if(bFloat > maxFloat) maxFloat = bFloat;

    float minFloat = rFloat;
    if(gFloat < minFloat) minFloat = gFloat;
    if(bFloat < minFloat) minFloat = bFloat;
    float maxDiff = maxFloat - minFloat;

    *v = maxFloat;
    if(maxDiff == 0) {
        *h = 0;
        *s = 0;
    } else {
        *s = maxDiff / maxFloat;

        float diffR = (((maxFloat - rFloat) / 6) + (maxDiff / 2)) / maxDiff;
        float diffG = (((maxFloat - gFloat) / 6) + (maxDiff / 2)) / maxDiff;
        float diffB = (((maxFloat - bFloat) / 6) + (maxDiff / 2)) / maxDiff;

        if      (rFloat == maxFloat) *h = diffB - diffG;
        else if (gFloat == maxFloat) *h = (1.0/3.0) + diffR - diffB;
        else if (bFloat == maxFloat) *h = (2.0/3.0) + diffG - diffR;

        if(*h < 0) *h = *h + 1;
        if(*h > 1) *h = *h - 1;
    }
}

void littleEndianDump(char* writeInto, int* writeSize, int32_t data, int8_t bytes) {
    for(int i = *writeSize; i < bytes + *writeSize; i++) {
        writeInto[i] = data & 0xFF;
        data = data >> 8;
    }
    *writeSize += bytes;
}

void bigEndianDump(char* writeInto, int* writeSize, int32_t data, int8_t bytes) {
    for(int i = (*writeSize) + bytes - 1; i >= (*writeSize); i--) {
        writeInto[i] = data & 0xFF;
        data = data >> 8;
    }
    *writeSize += bytes;
}

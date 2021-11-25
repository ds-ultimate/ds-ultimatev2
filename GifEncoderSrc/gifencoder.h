
#ifndef GIFENCODER_H
#define GIFENCODER_H

#include "lodepng.h"
#include "hashmap.h"

typedef struct {
    int width;
    int height;
    int delay;
    LodePNGColorType colType;
    int32_t** images;
    int numImages;
    int maxImages;

    struct hashmap* pictureColorMap;

    int32_t* globalColorTable;
    float* globalHSVColorTableH;
    float* globalHSVColorTableS;
    float* globalHSVColorTableV;
    int globalColorTableSize;
    int GIFglobalColorTableSize;

    struct hashmap* colorHash;
} GIF_STRUCTURE;

/**
 * Initializes a gif structure that can be filled afterwards
 */
GIF_STRUCTURE* gifencoder_create(unsigned width, unsigned height, unsigned delay, LodePNGColorType colType);

/**
 * Adds a given imageData to the GIF (just add will not perform any calculations)
 */
void gifencoder_addImage(GIF_STRUCTURE* gif, unsigned char* imageData);

/**
 * Creates and writes the gif
 * uses the data found in the GIF_STRUCTURE
 * @param targetFile the file we want to write into
 */
void gifencoder_encode(GIF_STRUCTURE* gif, char* targetFile);

/**
 * Frees the Structure
 */
void gifencoder_free(GIF_STRUCTURE* gif);

#endif

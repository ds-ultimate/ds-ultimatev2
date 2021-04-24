#include <stdio.h>
#include <stdlib.h>

#include <math.h>
#include "lzwCompressor.h"
#include "globals.h"

#define MAX_CODE_SIZE 4000
#define compressed_allocate_at_once 2000
#define bitstreamSize 200

void lzwCompressor_reInitTable(LZW_STRUCTURE* lzw);
void lzwCompressor_sizeCalc(LZW_STRUCTURE* lzw);
void lzwCompressor_bitstreamAppend(LZW_STRUCTURE* lzw, int data);
/*
Flow:
-> bulk allocate MAX_CODE_SIZE + 1 elements

Add:
Speichern von aktuellem element
schauen ob sub elm für gegebenes vorhanden
wenn = 0 -> nicht gefunden
wenn != 0 -< gefunden -> neues aktuelles elm

Clear:
sub elm verlinkung = 0
*/

/**
 * Initializes a compressor structure that can be filled afterwards
 */
LZW_STRUCTURE* lzwCompressor_create(int globColorTableSizeBit) {
    LZW_STRUCTURE* retval = calloc(1, sizeof(LZW_STRUCTURE));

    retval->compressed = calloc(compressed_allocate_at_once, sizeof(char));
    retval->compressedSize = 0;
    retval->maxCompressedSize = compressed_allocate_at_once;

    retval->initialTableSize = 2 << globColorTableSizeBit;
    if(retval->initialTableSize > MAX_INITIAL_TABLE_SIZE) {
        printf("Initial Table to big?? %u / %u", globColorTableSizeBit, retval->initialTableSize);
        exit(4);
    }

    //allocate MAX_CODE_SIZE + 1 elements -> no need for further allocates
    retval->rootNode = calloc(MAX_CODE_SIZE, sizeof(LZW_TREE_ELEMENT));
    retval->rootNode->value = -1;
    for(int i = 0; i < retval->initialTableSize; i++) {
        retval->rootNode->subElements[i] = retval->rootNode + i + 1;
        retval->rootNode->subElements[i]->value = i;
    }

    retval->asBitstream = malloc(bitstreamSize * sizeof(char));
    retval->asBitstreamStart = 0;
    retval->asBitstreamEnd = 0;
    retval->codeSize = globColorTableSizeBit + 2;
    lzwCompressor_reInitTable(retval);

    return retval;
}

/**
 * Appends a given symbol to the compressor
 */
void lzwCompressor_append(LZW_STRUCTURE* lzw, int data) {
    //new nodes will always have index lzw->curTableSize - 1
    //because +1 since 0 is root / -2 since we have Clear and end of image
    if(lzw->lastNode->subElements[data]) {
        //found -> new last node is found node
        lzw->lastNode = lzw->lastNode->subElements[data];
    } else {
        //not found
        lzwCompressor_bitstreamAppend(lzw, lzw->lastNode->value);
        lzw->lastNode->subElements[data] = lzw->rootNode + lzw->curTableSize - 1;
        lzw->lastNode->subElements[data]->value = lzw->curTableSize;
        for(int j = 0; j < lzw->initialTableSize; j++) {
            lzw->lastNode->subElements[data]->subElements[j] = 0;
        }
        lzw->curTableSize++;

        if(lzw->curTableSize == MAX_CODE_SIZE) {
            lzwCompressor_reInitTable(lzw);
        }
        if(lzw->codeSizeLimit <= lzw->curTableSize) {
            lzwCompressor_sizeCalc(lzw);
        }
        lzw->lastNode = lzw->rootNode->subElements[data];
    }
}

/**
 * finishes the writing
 * - adds end marker
 * - empties bit stream
 */
void lzwCompressor_finish(LZW_STRUCTURE* lzw) {
    //put end of image code
    lzwCompressor_bitstreamAppend(lzw, lzw->lastNode->value);
    lzwCompressor_bitstreamAppend(lzw, lzw->initialTableSize + 1);

    int bitstreamTempEnd = lzw->asBitstreamEnd;
    if(lzw->asBitstreamEnd < lzw->asBitstreamStart) {
        //wrap around
        bitstreamTempEnd += bitstreamSize;
    }

    int data = 0, i = 0;
    while(lzw->asBitstreamStart != lzw->asBitstreamEnd) {
        if(lzw->asBitstream[lzw->asBitstreamStart++]) {
            data |= 1 << i;
        }

        if(lzw->asBitstreamStart == bitstreamSize) {
            lzw->asBitstreamStart = 0;
            bitstreamTempEnd -= bitstreamSize;
        }
        i++;
    }

    lzw->compressed[lzw->compressedSize++] = data & 0xFF;
}

/**
 * "returns" the compressed data with size
 */
void lzwCompressor_getCompressed(LZW_STRUCTURE* lzw, char** data, int* dataSize) {
    *data = lzw->compressed;
    *dataSize = lzw->compressedSize;
}

/**
 * Frees the Structure
 */
void lzwCompressor_free(LZW_STRUCTURE* lzw) {
    free(lzw->compressed);
    free(lzw->rootNode);
    free(lzw->asBitstream);
    free(lzw);
}

void lzwCompressor_reInitTable(LZW_STRUCTURE* lzw) {
    lzwCompressor_bitstreamAppend(lzw, lzw->initialTableSize);

    //+2 for clear code and end of image code
    lzw->curTableSize = lzw->initialTableSize + 2;

    for(int i = 0; i < lzw->initialTableSize; i++) {
        for(int j = 0; j < lzw->initialTableSize; j++) {
            lzw->rootNode->subElements[i]->subElements[j] = 0;
        }
    }
    lzw->lastNode = lzw->rootNode;
    lzwCompressor_sizeCalc(lzw);
}

void lzwCompressor_sizeCalc(LZW_STRUCTURE* lzw) {
    lzw->codeSize = (int) ceil(log2f(lzw->curTableSize));
    lzw->codeSizeLimit = 1 + (2 << (lzw->codeSize - 1));
}

void lzwCompressor_bitstreamAppend(LZW_STRUCTURE* lzw, int data) {
    for(int i = 0; i < lzw->codeSize; i++) {
        lzw->asBitstream[lzw->asBitstreamEnd++] = data & 0x01;
        data = data >> 1;

        if(lzw->asBitstreamEnd == bitstreamSize) {
            lzw->asBitstreamEnd = 0;
        }
    }

    int bitstreamTempEnd = lzw->asBitstreamEnd;
    if(lzw->asBitstreamEnd < lzw->asBitstreamStart) {
        //wrap around
        bitstreamTempEnd += bitstreamSize;
    }

    int writeData, i;
    while(bitstreamTempEnd - lzw->asBitstreamStart > 7) {
        writeData = 0;
        for(i = 0; i < 8; i++) {
            if(lzw->asBitstream[lzw->asBitstreamStart++]) {
                writeData |= 1 << i;
            }

            if(lzw->asBitstreamStart == bitstreamSize) {
                lzw->asBitstreamStart = 0;
                bitstreamTempEnd -= bitstreamSize;
            }
        }

        lzw->compressed[lzw->compressedSize++] = writeData & 0xFF;
        if(lzw->compressedSize == lzw->maxCompressedSize) {
            lzw->maxCompressedSize += compressed_allocate_at_once;
            lzw->compressed = realloc(lzw->compressed, lzw->maxCompressedSize * sizeof(char));
        }
    }

    if(lzw->curTableSize >= lzw->codeSizeLimit) {
        lzwCompressor_sizeCalc(lzw);
    }
}

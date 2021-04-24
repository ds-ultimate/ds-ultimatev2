
#ifndef LZW_COMPRESSOR_H
#define LZW_COMPRESSOR_H

#define MAX_INITIAL_TABLE_SIZE 256

typedef struct LZW_TREE_ELEMENT LZW_TREE_ELEMENT;

struct LZW_TREE_ELEMENT {
    int16_t value;
    LZW_TREE_ELEMENT* subElements[MAX_INITIAL_TABLE_SIZE];

};

typedef struct {
    char* compressed;
    int compressedSize;
    int maxCompressedSize;

    int initialTableSize;
    LZW_TREE_ELEMENT* rootNode;
    int curTableSize;

    LZW_TREE_ELEMENT* lastNode;
    //ring buffer for bitstream
    char* asBitstream;
    int asBitstreamStart;
    int asBitstreamEnd;

    int codeSize;
    int codeSizeLimit;
} LZW_STRUCTURE;


/**
 * Initializes a compressor structure that can be filled afterwards
 */
LZW_STRUCTURE* lzwCompressor_create(int globColorTableSizeBit);

/**
 * Appends a given symbol to the compressor
 */
void lzwCompressor_append(LZW_STRUCTURE* lzw, int data);

/**
 * finishes the writing
 * - adds end marker
 * - empties bit stream
 */
void lzwCompressor_finish(LZW_STRUCTURE* lzw);

/**
 * "returns" the compressed data with size
 */
void lzwCompressor_getCompressed(LZW_STRUCTURE* lzw, char** data, int* dataSize);

/**
 * Frees the Structure
 */
void lzwCompressor_free(LZW_STRUCTURE* lzw);

#endif

#include <stdio.h>
#include <stdlib.h>

#include <time.h>

#include "globals.h"
#include "lodepng.h"
#include "gifencoder.h"

#include "sorting.h"

int main(int argc, char **argv)
{
    if(argc != 5) {
        //print the usage
        printf("usage: GifEncoder [src] [dst] [num] [delay]\n");
        printf(" src -> source name - use %%d or simmilar for number\n");
        printf(" dst -> name of the exported gif\n");
        printf(" num -> amount of frames to do\n");
        printf(" delay -> delay between two frames in ms\n");
        exit(0);
    }

    char* src = argv[1];
    char* dst = argv[2];
    int num = atoi(argv[3]);
    int delay = atoi(argv[4]);


    unsigned error;
    unsigned char* png = 0;
    size_t pngsize;
    unsigned char* image = 0;
    LodePNGState state;
    unsigned width, height;

    int widthCheck, heightCheck;
    LodePNGColorType typeCheck;
    GIF_STRUCTURE* gif = 0;

    #define tempSize 100
    char* temp = calloc(tempSize, sizeof(char));

    for(int i = 0; i < num; i++) {
        #ifdef TIMING_DEBUG
        clock_t start = clock();
        #endif // TIMING_DEBUG
        printf("Add %d\n", i);
        fflush(stdout);

        int written = snprintf(temp, tempSize - 10, src, i);
        if(written > tempSize - 10) {
            printf("Resulting filename is too long for buffer max len: %d\n", tempSize - 10);
            exit(0);
        }

        lodepng_state_init(&state);

        error = lodepng_load_file(&png, &pngsize, temp);
        if(!error) error = lodepng_decode(&image, &width, &height, &state, png, pngsize);
        if(error) {
            printf("error %u: %s\n", error, lodepng_error_text(error));
            printf("at %d", i);
            exit(-1);
        }
        free(png);

        if(i == 0) {
            typeCheck = state.info_png.color.colortype;
            widthCheck = width;
            heightCheck = height;

            gif = gifencoder_create(widthCheck, heightCheck, delay / 10, typeCheck);
        }

        if(state.info_png.color.colortype != typeCheck) {
            printf("type != typeCheck %u / %u\n", state.info_png.color.colortype, typeCheck);
            exit(-2);
        }

        if(width != widthCheck) {
            printf("width != widthCheck %u / %u\n", width, widthCheck);
            exit(-3);
        }

        if(height != heightCheck) {
            printf("height != heightCheck %u / %u\n", height, heightCheck);
            exit(-4);
        }

        #ifdef TIMING_DEBUG
        clock_t load = clock();
        #endif // TIMING_DEBUG

        gifencoder_addImage(gif, image);
        #ifdef TIMING_DEBUG
        clock_t end = clock();
        printf("Timing: Load %ld u sec / Append %ld u sec\n", (load - start), (end - load));
        #endif // TIMING_DEBUG

        lodepng_state_cleanup(&state);
        free(image);
    }

    gifencoder_encode(gif, dst);
    free(temp);
    temp = 0;
    if(gif) gifencoder_free(gif);
    gif = 0;

    return 0;
}

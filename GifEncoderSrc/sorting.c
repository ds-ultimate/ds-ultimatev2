#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#include "sorting.h"

void merge(int32_t* sortBy, int32_t* other, int32_t* tmpSortBy, int32_t* tmpOther, int l, int m, int r);
void mergeSort(int32_t* sortBy, int32_t* other, int32_t* tmpSortBy, int32_t* tmpOther, int l, int r);

void sort_data(int32_t* sortBy, int32_t* other, int array_size) {
    int32_t* tmpSortBy = malloc(array_size * sizeof(int32_t));
    int32_t* tmpOther = malloc(array_size * sizeof(int32_t));
    memcpy(tmpSortBy, sortBy, array_size * sizeof(int32_t));
    memcpy(tmpOther, other, array_size * sizeof(int32_t));

    mergeSort(sortBy, other, tmpSortBy, tmpOther, 0, array_size - 1);
    free(tmpSortBy);
    free(tmpOther);
}

// Merge Function
void merge(int32_t* sortBy, int32_t* other, int32_t* tmpSortBy, int32_t* tmpOther, int l, int m, int r) {
    int i, j, k;
    int n1 = m - l + 1;
    int n2 =  r - m;
    int32_t* LTmpSortBy = tmpSortBy + l;
    int32_t* LTmpOther = tmpOther + l;
    int32_t* RTmpSortBy = tmpSortBy + m + 1;
    int32_t* RTmpOther = tmpOther + m + 1;

    //Data is inside tmp move into orig
    i = 0;
    j = 0;
    k = l;
    while (i < n1 && j < n2) {
        if (LTmpSortBy[i] > RTmpSortBy[j]) {
            sortBy[k] = LTmpSortBy[i];
            other[k] = LTmpOther[i];
            i++;
        } else {
            sortBy[k] = RTmpSortBy[j];
            other[k] = RTmpOther[j];
            j++;
        }
        k++;
    }
    while (i < n1) {
        sortBy[k] = LTmpSortBy[i];
        other[k] = LTmpOther[i];
        i++;
        k++;
    }
    while (j < n2) {
        sortBy[k] = RTmpSortBy[j];
        other[k] = RTmpOther[j];
        j++;
        k++;
    }
}

// Merge Sort Function in C
void mergeSort(int32_t* sortBy, int32_t* other, int32_t* tmpSortBy, int32_t* tmpOther, int l, int r) {
    if (l < r) {
        int m = l+(r-l)/2;
        mergeSort(tmpSortBy, tmpOther, sortBy, other, l, m);
        mergeSort(tmpSortBy, tmpOther, sortBy, other, m+1, r);
        merge(sortBy, other, tmpSortBy, tmpOther, l, m, r);
    }
}

void bubble_sort(int32_t* sortBy, int32_t* other, int array_size) {
    int i, j;
    int32_t temp;

    for (i = 0; i < (array_size - 1); ++i)
    {
        for (j = 0; j < array_size - 1 - i; ++j )
        {
            if (sortBy[j] < sortBy[j+1])
            {
                temp = sortBy[j+1];
                sortBy[j+1] = sortBy[j];
                sortBy[j] = temp;

                temp = other[j+1];
                other[j+1] = other[j];
                other[j] = temp;
            }
        }
    }
}

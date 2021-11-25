//Copied from https://github.com/tidwall/hashmap.c

// Copyright 2020 Joshua J Baker. All rights reserved.
// Use of this source code is governed by an MIT-style
// license that can be found in the LICENSE file.

#ifndef HASHMAP_H
#define HASHMAP_H

#include <stdbool.h>
#include <stddef.h>
#include <stdint.h>

struct hashmap;

struct bucket {
    int32_t hash;
    int16_t dib;
    int32_t value;
};


struct hashmap *hashmap_new(size_t cap);

void hashmap_free(struct hashmap *map);
void hashmap_clear(struct hashmap *map, bool update_cap);
size_t hashmap_count(struct hashmap *map);
bool hashmap_oom(struct hashmap *map);
struct bucket *hashmap_get(struct hashmap *map, int32_t key);
struct bucket *hashmap_set(struct hashmap *map, int32_t key, int32_t value);
struct bucket *hashmap_delete(struct hashmap *map, int32_t key);
void *hashmap_probe(struct hashmap *map, uint64_t position);
bool hashmap_scan(struct hashmap *map,
                  bool (*iter)(struct bucket* item, void *udata), void *udata);

uint64_t hashmap_sip(const void *data, size_t len);
uint64_t hashmap_murmur(const void *data, size_t len);

#endif

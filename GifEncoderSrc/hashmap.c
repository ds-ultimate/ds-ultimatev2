//Copied from https://github.com/tidwall/hashmap.c

// Copyright 2020 Joshua J Baker. All rights reserved.
// Use of this source code is governed by an MIT-style
// license that can be found in the LICENSE file.

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <stdint.h>
#include <stddef.h>
#include "hashmap.h"

#define panic(_msg_) { \
    fprintf(stderr, "panic: %s (%s:%d)\n", (_msg_), __FILE__, __LINE__); \
    exit(1); \
}

#define bucketsz sizeof(struct bucket)


// hashmap is an open addressed hash map using robinhood hashing.
struct hashmap {
    bool oom;
    bool forceGrow;
    size_t cap;
    size_t nbuckets;
    size_t count;
    size_t mask;
    size_t growat;
    size_t shrinkat;
    struct bucket *buckets;
    struct bucket *spare;
    struct bucket *edata;
};

static struct bucket *bucket_at(struct hashmap *map, size_t index) {
    return map->buckets + index;
}

// hashmap_new returns a new hash map.
// Param `elsize` is the size of each element in the tree. Every element that
// is inserted, deleted, or retrieved will be this size.
// Param `cap` is the default lower capacity of the hashmap. Setting this to
// zero will default to 16.
// Param `hash` is a function that generates a hash value for an item. It's
// important that you provide a good hash function, otherwise it will perform
// poorly or be vulnerable to Denial-of-service attacks. This implementation
// comes with two helper functions `hashmap_sip()` and `hashmap_murmur()`.
// Param `compare` is a function that compares items in the tree. See the
// qsort stdlib function for an example of how this function works.
// The hashmap must be freed with hashmap_free().
// Param `elfree` is a function that frees a specific item. This should be NULL
// unless you're storing some kind of reference data in the hash.
struct hashmap *hashmap_new(size_t cap)
{
    int ncap = 16;
    if (cap < ncap) {
        cap = ncap;
    } else {
        while (ncap < cap) {
            ncap *= 2;
        }
        cap = ncap;
    }
    // hashmap + spare + edata
    size_t size = sizeof(struct hashmap)+bucketsz*2;
    struct hashmap *map = malloc(size);
    if (!map) {
        return NULL;
    }
    memset(map, 0, sizeof(struct hashmap));
    map->spare = (struct bucket*) (((char*)map)+sizeof(struct hashmap));
    map->edata = (struct bucket*) (((char*)map->spare)+bucketsz);
    map->cap = cap;
    map->nbuckets = cap;
    map->mask = map->nbuckets-1;
    map->buckets = calloc(map->nbuckets, bucketsz);
    if (!map->buckets) {
        free(map);
        return NULL;
    }
    map->growat = map->nbuckets * 0.75;
    map->shrinkat = map->nbuckets * 0.1;
    return map;
}


// hashmap_clear quickly clears the map.
// Every item is called with the element-freeing function given in hashmap_new,
// if present, to free any data referenced in the elements of the hashmap.
// When the update_cap is provided, the map's capacity will be updated to match
// the currently number of allocated buckets. This is an optimization to ensure
// that this operation does not perform any allocations.
void hashmap_clear(struct hashmap *map, bool update_cap) {
    map->count = 0;
    if (update_cap) {
        map->cap = map->nbuckets;
    } else if (map->nbuckets != map->cap) {
        void *new_buckets = malloc(bucketsz*map->cap);
        if (new_buckets) {
            free(map->buckets);
            map->buckets = new_buckets;
        }
        map->nbuckets = map->cap;
    }
    memset(map->buckets, 0, bucketsz*map->nbuckets);
    map->mask = map->nbuckets-1;
    map->growat = map->nbuckets*0.75;
    map->shrinkat = map->nbuckets*0.10;
}


static bool resize(struct hashmap *map, size_t new_cap) {
    struct hashmap *map2 = hashmap_new(new_cap);
    if (!map2) {
        return false;
    }
    for (size_t i = 0; i < map->nbuckets; i++) {
        struct bucket *entry = bucket_at(map, i);
        if (!entry->dib) {
            continue;
        }
        entry->dib = 1;
        size_t j = entry->hash & map2->mask;
        for (;;) {
            struct bucket *bucket = bucket_at(map2, j);
            if (bucket->dib == 0) {
                memcpy(bucket, entry, bucketsz);
                break;
            }
            if (bucket->dib < entry->dib) {
                memcpy(map2->spare, bucket, bucketsz);
                memcpy(bucket, entry, bucketsz);
                memcpy(entry, map2->spare, bucketsz);
            }
            j = (j + 1) & map2->mask;
            entry->dib += 1;
        }
	}
    free(map->buckets);
    map->buckets = map2->buckets;
    map->nbuckets = map2->nbuckets;
    map->mask = map2->mask;
    map->growat = map2->growat;
    map->shrinkat = map2->shrinkat;
    free(map2);
    return true;
}

// hashmap_set inserts or replaces an item in the hash map. If an item is
// replaced then it is returned otherwise NULL is returned. This operation
// may allocate memory. If the system is unable to allocate additional
// memory then NULL is returned and hashmap_oom() returns true.
struct bucket *hashmap_set(struct hashmap *map, int32_t key, int32_t value) {
    map->oom = false;
    if (map->count == map->growat || map->forceGrow) {
        map->forceGrow = false;
        if (!resize(map, map->nbuckets*2)) {
            map->oom = true;
            return NULL;
        }
    }

    struct bucket *entry = map->edata;
    entry->hash = key;
    entry->dib = 1;
    entry->value = value;

    size_t i = entry->hash & map->mask;
	for (;;) {
        struct bucket *bucket = bucket_at(map, i);
        if (bucket->dib == 0) {
            memcpy(bucket, entry, bucketsz);
            map->count++;
			return NULL;
		}
        if (entry->hash == bucket->hash)
        {
            memcpy(map->spare, bucket, bucketsz);
            memcpy(bucket, entry, bucketsz);
            return map->spare;
		}
        if (bucket->dib < entry->dib) {
            memcpy(map->spare, bucket, bucketsz);
            memcpy(bucket, entry, bucketsz);
            memcpy(entry, map->spare, bucketsz);
		}
		i = (i + 1) & map->mask;
        entry->dib += 1;
        if(entry->dib > 50) {
            map->forceGrow = true;
        }
	}
}

// hashmap_get returns the item based on the provided key. If the item is not
// found then NULL is returned.
struct bucket *hashmap_get(struct hashmap *map, int32_t key) {
	size_t i = key & map->mask;
	for (;;) {
        struct bucket *bucket = bucket_at(map, i);
		if (!bucket->dib) {
			return NULL;
		}
		if (bucket->hash == key)
        {
            return bucket;
		}
		i = (i + 1) & map->mask;
	}
}


// hashmap_delete removes an item from the hash map and returns it. If the
// item is not found then NULL is returned.
struct bucket *hashmap_delete(struct hashmap *map, int32_t key) {
    map->oom = false;
	size_t i = key & map->mask;
	for (;;) {
        struct bucket *bucket = bucket_at(map, i);
		if (!bucket->dib) {
			return NULL;
		}
		if (bucket->hash == key)
        {
            memcpy(map->spare, bucket, bucketsz);
            bucket->dib = 0;
            for (;;) {
                struct bucket *prev = bucket;
                i = (i + 1) & map->mask;
                bucket = bucket_at(map, i);
                if (bucket->dib <= 1) {
                    prev->dib = 0;
                    break;
                }
                memcpy(prev, bucket, bucketsz);
                prev->dib--;
            }
            map->count--;
            if (map->nbuckets > map->cap && map->count <= map->shrinkat) {
                // Ignore the return value. It's ok for the resize operation to
                // fail to allocate enough memory because a shrink operation
                // does not change the integrity of the data.
                resize(map, map->nbuckets/2);
            }
			return map->spare;
		}
		i = (i + 1) & map->mask;
	}
}

// hashmap_count returns the number of items in the hash map.
size_t hashmap_count(struct hashmap *map) {
    return map->count;
}

// hashmap_free frees the hash map
// Every item is called with the element-freeing function given in hashmap_new,
// if present, to free any data referenced in the elements of the hashmap.
void hashmap_free(struct hashmap *map) {
    if (!map) return;
    free(map->buckets);
    free(map);
}

// hashmap_oom returns true if the last hashmap_set() call failed due to the
// system being out of memory.
bool hashmap_oom(struct hashmap *map) {
    return map->oom;
}

// hashmap_scan iterates over all items in the hash map
// Param `iter` can return false to stop iteration early.
// Returns false if the iteration has been stopped early.
bool hashmap_scan(struct hashmap *map,
                  bool (*iter)(struct bucket* item, void *udata), void *udata)
{
    for (size_t i = 0; i < map->nbuckets; i++) {
        struct bucket *bucket = bucket_at(map, i);
        if (bucket->dib) {
            if (!iter(bucket, udata)) {
                return false;
            }
        }
    }
    return true;
}

<?php

return [
    'map' => [
        'cacheDir' => 'app/mapCache/',
    ],
    'signature' => [
        'cacheDir' => 'app/signatureCache/',
        'cacheDuration' => 24 * 60 * 60,
    ],
    'animHistMap' => [
        'renderDir' => 'app/animatedMaps/',
    ],
    'chart' => [
        'cacheDir' => 'app/chartCache/',
        'cacheDuration' => 24 * 60 * 60,
    ],
    'logs' => [
        'cacheRates' => 'customLog/cache/',
    ],
];

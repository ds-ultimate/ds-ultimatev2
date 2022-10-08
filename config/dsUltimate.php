<?php

return [
    'db_database_world' => env('DB_DATABASE_WORLD', 'dsUltimate_welt_{server}{world}'),

    'hash_ally' => env('HASH_ALLY', 29),
    'hash_player' => env('HASH_PLAYER', 59),
    'hash_village' => env('HASH_VILLAGE', 109),

    'db_save_day' => env('DB_SAVE_DAY', 30),
    'db_save_day_speed' => env('DB_SAVE_DAY_SPEED', 7),
    'db_update_every_hours' => env('DB_UPDATE_EVERY_HOURS', 2),
    'db_clean_every_hours' => env('DB_CLEAN_EVERY_HOURS', 24),

    'changelog_lang_key' => [
        'de',
        'en'
    ],
    
    'history_directory' => env('HISTORY_DIRECTORY', 'app/history/'),
    'attackPlannerSoundDirectory' => env('ATTACKPLANNER_SOUND_DIRECTORY', 'app/attackPlannerSounds/'),
];

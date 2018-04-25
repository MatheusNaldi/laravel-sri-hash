<?php

return [
    'base_path' => base_path('public'),

    'algorithm' => env('SRI_ALGORITHM', 'sha256'),

    'sri_hashes_file' => public_path('sri_hashes.json'),

    'sri_generate' => [
        'search_file_ext' => [
            'js',
            'css'
        ],

        'folders' => [
            base_path('public')
        ],
    ]
];

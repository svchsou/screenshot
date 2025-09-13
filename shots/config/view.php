<?php

return [

    // Directories where your Blade templates live
    'paths' => [
        resource_path('views'),
    ],

    // Where compiled Blade templates are cached
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
];


<?php

return [

    'paths' => [
        'base' => base_path(),
        'hidden' => [
            base_path('vendor/'),
            base_path('node_modules/')
        ],
        'pattern' => '/{,.}[!.,!..]*'
    ],

    // Laravel File Manager route configuration
    'route' => [
        'prefix' => '/file-manager',
        'domain' => '',
        'namespace' => 'dyutin\\FileManager\\Controllers',
        'as' => 'fileManager',
        'middleware' => ['web','auth']
    ],
];

<?php

return [
    'default' => 'toastr',

    'main_script' => '/vendor/flasher/flasher.min.js',

    'public_path' => '',

    'styles' => [
        '/vendor/flasher/flasher.min.css',
    ],

    'inject_assets' => true,

    'translate' => true,

    'excluded_paths' => [],

    'flash_bag' => [
        'success' => ['success'],
        'error' => ['error', 'danger'],
        'warning' => ['warning', 'alarm'],
        'info' => ['info', 'notice', 'alert'],
    ],

    'filter' => [
        'limit' => 5,
    ],

    'plugins' => [
        'toastr' => [
            'scripts' => [
                '/vendor/flasher/toastr.min.js',
                '/vendor/flasher/flasher-toastr.min.js',
            ],
            'options' => [
                'closeButton' => true,
                'progressBar' => true,
                'positionClass' => 'toast-top-right',
                'timeOut' => 3500,
            ],
        ],
    ],
];

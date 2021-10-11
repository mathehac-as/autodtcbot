<?php
return [
    'token' => '',
    'db' => [
        'DBHost' => 'localhost',
        'DBPort' => 3306,
        'DBName' => '',
        'DBUser' => '',
        'DBPassword' => ''
    ],
    'home' => require_once('home.conf'),
    'search' => [
        'obd_code_search' => [

        ],
        'marka' => [
            'Nissan' => [
                [['callback_data' => 'nissan', 'text' => 'nissan qashqai (2006-2013)']],
                [['callback_data' => 'mazda', 'text' => 'nissan qashqai (2013-2019)']],
            ],
        ]
    ],
];
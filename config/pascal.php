<?php
return [
    'rpc' => [
        'connect_timeout_ms' => 500,
        'timeout_ms' => 3000,
        'retry' => 1,
        'headers'    => [

        ],
        'response'   => [
            'code'    => 'code',
            'message' => 'message',
            'data'    => 'data',
        ],
    ],

    /**
     *  id: zsfucaiservice
     *  name: zsfucaiservice
     *  address: zsfucai.cn
     *  ssl: false
     *  port: 80
     */
    'service' => [
        'passportservice' => [
            'id' => 'passportservice',
            'name' => 'passportservice',
            'address' => env('PASSPORT_URL', 'passport.zsfucai.cn'),
            'ssl' => false,
            'port' => 80,
        ],
        'cpzsfucaiservice' => [
            'id' => 'cpzsfucaiservice',
            'name' => 'cpzsfucaiservice',
            'address' => 'cp.' . env('ZSFUCAI_URL', 'zsfucai.cn'),
            'ssl' => false,
            'port' => 80,
        ],
    ],


];

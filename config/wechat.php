<?php

/*
 * This file is part of the overtrue/laravel-wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
        ],
    ],

    /*
     * 路由配置
     */
    'route' => [
        /*
         * 开放平台第三方平台路由配置
         */
        // 'open_platform' => [
        //     'uri' => 'serve',
        //     'action' => Overtrue\LaravelWeChat\Controllers\OpenPlatformController::class,
        //     'attributes' => [
        //         'prefix' => 'open-platform',
        //         'middleware' => null,
        //     ],
        // ],
    ],

    /*
     * 公众号
     */
    'official_account' => [
        'default' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'your-app-id'),         // AppID
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', 'your-app-secret'),    // AppSecret
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'your-token'),           // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),                 // EncodingAESKey

            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            // 'oauth' => [
            //     'scopes'   => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
            //     'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            // ],
        ],
    ],

    /*
     * 开放平台第三方平台
     */
    // 'open_platform' => [
    //     'default' => [
    //         'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //         'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //         'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //         'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
    //     ],
    // ],

    /*
     * 小程序
     */
    // 'mini_program' => [
    //     'default' => [
    //         'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
    //         'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
    //         'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
    //         'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    //     ],
    // ],

    /*
     * 微信支付
     */
    // 'payment' => [
    //     'default' => [
    //         'sandbox'            => env('WECHAT_PAYMENT_SANDBOX', false),
    //         'app_id'             => env('WECHAT_PAYMENT_APPID', ''),
    //         'mch_id'             => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
    //         'key'                => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
    //         'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
    //         'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
    //         'notify_url'         => 'http://example.com/payments/wechat-notify',                           // 默认支付结果通知地址
    //     ],
    //     // ...
    // ],

    /*
     * 企业微信
     */
    /*
     * 企业微信
     */
    'work' => [
        //发送企业微信消息
        'default' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_CONTACTS_CORP_ID', ''), 
            'agent_id' => env('WECHAT_WORK_AGENT_CONTACTS_AGENT_ID', ''),
            'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
         ],
        //获取通讯录列表
        'member' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_CONTACTS_MOMBER_CORP_ID', ''), 
            'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_MOMBER_SECRET', ''),
         ],
        //发送企业微信消息(临采咨询工单)
        'workorder' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_CONTACTS_CORP_ID', ''), 
            'agent_id' => env('WECHAT_WORK_AGENT_WORKORDER_AGENT_ID', ''),
            'secret'   => env('WECHAT_WORK_AGENT_WORKORDER_SECRET', ''),
        ],
        // 发送企业微信消息(物流预警)
        'express' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_CONTACTS_CORP_ID', ''), 
            'agent_id' => env('WECHAT_WORK_AGENT_EXPRESS_AGENT_ID', ''),
            'secret'   => env('WECHAT_WORK_AGENT_EXPRESS_SECRET', ''),
        ],
        'price_notice' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_CONTACTS_CORP_ID', ''),
            'agent_id' => env('WECHAT_WORK_AGENT_PRICE_NOTICE_AGENT_ID', ''),
            'secret'   => env('WECHAT_WORK_AGENT_PRICE_NOTICE_SECRET', ''),
        ],
        'oa_approve' => [
            'corp_id'  => env('WECHAT_WORK_AGENT_OA_APPROVE_CORP_ID', ''),
            'agent_id' => env('WECHAT_WORK_AGENT_OA_APPROVE_AGENT_ID', ''),
            'secret'   => env('WECHAT_WORK_AGENT_OA_APPROVE_SECRET', ''),
        ],
    ],
    
    'enable_mock' => env('WECHAT_ENABLE_MOCK', false),
    'mock_user' => [
        'openid' => 'oLB961crEyLGJWmxK9r4rysTFYVM',
        // 以下字段为 scope 为 snsapi_userinfo 时需要
        'nickname' => '测试用户5',
        'sex' => '1',
        'province' => '北京',
        'city' => '北京',
        'country' => '中国',
        'unionid' => 'ojStS1ve0qLIIU9KalXDNyY0BsJ8',
        'headimgurl' => 'http://wx.qlogo.cn/mmopen/C2rEUskXQiblFYMUl9O0G05Q6pKibg7V1WpHX6CIQaic824apriabJw4r6EWxziaSt5BATrlbx1GVzwW2qjUCqtYpDvIJLjKgP1ug/0',
    ],
];

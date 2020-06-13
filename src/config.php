<?php

return [
    /**
     * 钉钉一键登录配置
     */
    'connect' => [
        'url' => env('DINGTALK_CONNECT_URL'),
        'appid' => env('DINGTALK_CONNECT_APPID'),
        'appsecret' => env('DINGTALK_CONNECT_APPSECRET'),
    ],
    /**
     * 钉钉应用配置
     */
    'app' => [
        'key' => env('DINGTALK_APP_KEY'),
        'secret' => env('DINGTALK_APP_SECRET'),
        # 缓存 AccessToken 的键名
        'cache_keyname' => env('DINGTALK_APP_CACHE_KEYNAME', 'dingtalk_access_token'),
    ],
    /**
     * 机器人的默认配置
     */
    'robot' => [
        'appsecret' => env('DINGTALK_ROBOT_APPSECRET'),
    ]
];

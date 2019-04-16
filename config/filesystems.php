<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'avatar' => [
            'driver' => 'local',
            'root' => base_path('../../attachment/avatar'),
            'url' => env('APP_URL').'/attachment/avatar',
            'visibility' => 'public',
        ],

        'image' => [
            'driver' => 'local',
            'root' => base_path('../../attachment/image'),
            'url' => env('APP_URL').'image',
            'visibility' => 'public',
        ],

        'cert' => [
            'driver' => 'local',
            'root' => storage_path('cert'),
        ],
        // 批量发货上传excel文件保存路径
        'recharge' => [
            'driver' => 'local',
            'root' => storage_path('app/public/recharge'),
        ],

        // 批量发货上传excel文件保存路径
        'orderexcel' => [
            'driver' => 'local',
            'root' => storage_path('app/public/orderexcel'),
        ],

        // 批量卡密上传excel文件保存路径
        'virtualcard' => [
            'driver' => 'local',
            'root' => storage_path('app/public/virtualcard'),
        ],

        // 网约车 批量上传excel文件保存路径
        'netcar' => [
            'driver' => 'local',
            'root' => storage_path('app/public/netcar'),
        ],

        // 易宝支付图片上传
        'yop' => [
            'driver' => 'local',
            'root' => storage_path('app/public/yop'),
            'url' => env('APP_URL').'/storage/public/yop',
        ],

        'upload' => [
            'driver' => 'local',
            'root' => storage_path('app/public/avatar'),
            'url' => env('APP_URL').'/storage/public/avatar',
            'visibility' => 'public',
        ],

        'banner' => [
            'driver' => 'local',
            'root' => storage_path('app/public/banner'),
            'url' => env('APP_URL').'/storage/public/banner',
            'visibility' => 'public',
        ],

        //淘宝CSV实例
        'taobaoCSV' => [
            'driver' => 'local',
            'root'=> base_path('plugins/goods-assistant/storage/examples'),
            'url' => env('APP_URL').'plugins/goods-assistant/storage/examples',
            'visibility' => 'public',
        ],

        //淘宝CSV上传
        'taobaoCSVupload' => [
            'driver' => 'local',
            'root'=> base_path('plugins/goods-assistant/storage/upload'),
            'url' => env('APP_URL').'plugins/goods-assistant/storage/upload',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
    ],
];

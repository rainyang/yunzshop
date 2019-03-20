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

        'syst' => [
            'driver' => 'local',
            'root' => base_path('static/upload/image/0/'.date('Y').'/'.date('m')),
            'url' => env('APP_URL').'/static/upload/image/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
        ],

        'video' => [
            'driver' => 'local',
            'root' => base_path('static/upload/video/0/'.date('Y').'/'.date('m')),
            'url' => env('APP_URL').'/static/upload/video/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
        ],

        'audio' => [
            'driver' => 'local',
            'root' => base_path('static/upload/audio/0/'.date('Y').'/'.date('m')),
            'url' => env('APP_URL').'/static/upload/audio/0/'.date('Y').'/'.date('m'),
            'visibility' => 'public',
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

        'oss' => [
            'driver'        => 'oss',
            'access_id'     => 'LTAI5VawtDOhA5OL',
            'access_key'    => 'zE4oQDzaXNLMT4YSkQMu3lR5TJ6q2D',
            'bucket'        => 'shop-yunshop-com',
            // 'bucket'        => 'test-yunshop-com',
            'region'        => 'oss-cn-hangzhou',  //OSS 地域
            'endpoint'      => 'oss-cn-hangzhou.aliyuncs.com', // OSS 外网节点或自定义外部域名
            'endpoint_internal' => 'oss-cn-hangzhou-internal.aliyuncs.com', //OSS内网节点 同地域的ECS可以通过内网访问OSS。 跨账户的ECS和OSS可以内网互连。 不同地域的ECS与OSS无法通过内网访问。   同一个Region的ECS和OSS之间内网互通，不同Region的ECS和OSS之间内网不互通。
            // v2.0.4 新增配置，为空，默认用endpoint 配置
//            'cdnDomain'   => '<CDN domain, cdn域名>', // 如果isCName为true, getUrl会判断cdnDomain是否设定来决定返回的url，如果cdnDomain未设置，则使用endpoint来生成url，否则使用cdn
            // true to use 'https://' and false to use 'http://'. default is false,
            // 'ssl'           => true,
            // 'isCName'       => false, // 是否使用自定义域名,true: 则Storage.url()会使用自定义的cdn或域名生成文件url， false: 则使用外部节点生成url
            'debug'         => false
        ],

        'cos' => [
            'driver'     => 'cos',
            'app_id'     => '1251768088',
            'secret_id'  => 'AKIDGPYa8gNUdZjmcGMYIPSRL8oMl2CDNua9',
            'secret_key' => 'Wq23sUu8E8pKchbJsKaeVAj6HF2d2ED3',
            'bucket'     => 'yunzmall',
            //bucket访问域名 或  自定义域名
            // 'region'        => 'https://yunzmall-1251768088.cos.ap-guangzhou.myqcloud.com',
            'region ' => 'ap-guangzhou',
            // 'region'     => '22',
            // 'timeout'    => '120'
        ]
    ],
];

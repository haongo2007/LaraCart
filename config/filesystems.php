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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

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

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'dd-wrt' => [
            'driver'   => 'ftp',
            'host'     => 'ftp.dd-wrt.com',
            'username' => 'anonymous',
            'passive'  => true,
            'timeout'  => 30,
        ],
        
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_URL'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'category' => [
            'driver' => 'local',
            'root' => storage_path('app/public/category'),
            'url' => env('APP_URL').'/storage'
        ],

        'product' => [
            'driver' => 'local',
            'root' => storage_path('app/public/product'),
            'url' => env('APP_URL').'/storage'
        ],

        'user' => [
            'driver' => 'local',
            'root' => storage_path('app/public/user'),
            'url' => env('APP_URL').'/storage',
        ],

        'store' => [
            'driver' => 'local',
            'root' => storage_path('app/public/store'),
            'url' => env('APP_URL').'/storage',
        ],

        'banner' => [
            'driver' => 'local',
            'root' => storage_path('app/public/banner'),
            'url' => env('APP_URL').'/storage',
        ],

        'brand' => [
            'driver' => 'local',
            'root' => storage_path('app/public/brand'),
            'url' => env('APP_URL').'/storage',
        ],
        
        'supplier' => [
            'driver' => 'local',
            'root' => storage_path('app/public/supplier'),
            'url' => env('APP_URL').'/storage',
        ],
        
        'blogs' => [
            'driver' => 'local',
            'root' => storage_path('app/public/blogs'),
            'url' => env('APP_URL').'/storage',
        ],

        'page' => [
            'driver' => 'local',
            'root' => storage_path('app/public/page'),
            'url' => env('APP_URL').'/storage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

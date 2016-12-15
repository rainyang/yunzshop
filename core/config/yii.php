<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2016/9/24
 * Time: 下午7:02
 */
global $_W;
$master_db_config = $_W['config']['db']['master'];
return array(
    'id' => 'myapp',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],//,'debug'
    'modules' => [
        //'debug' => 'yii\debug\Module',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.$master_db_config['host'].';dbname='.$master_db_config['database'].';port='.$master_db_config['port'],
            'username' => $master_db_config['username'],
            'password' => $master_db_config['password'],
            'charset' => $master_db_config['charset'],
            'tablePrefix'=>$master_db_config['tablepre']
        ],
    ],
    'params' => [],
);

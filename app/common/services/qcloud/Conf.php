<?php
namespace app\common\services\qcloud;

use app\platform\modules\system\models\SystemSetting;


class Conf {

    // Please refer to http://console.qcloud.com/cos to fetch your app_id, secret_id and secret_key.
    public static $APP_ID;
    public static $SECRET_ID;
    public static $SECRET_KEY;

    // Cos php sdk version number.
    const VERSION = 'v4.2.2';
    const API_COSAPI_END_POINT = 'http://region.file.myqcloud.com/files/v2/';

    public function __construct()
    {
        $remote = SystemSetting::settingLoad('remote', 'system_remote');
        self::$APP_ID     = $remote['cos']['appid'];
        self::$SECRET_ID  = $remote['cos']['secretid'];
        self::$SECRET_KEY = $remote['cos']['secretkey'];
    }

    /**
     * Get the User-Agent string to send to COS server.
     */
    public static function getUserAgent() {
        return 'cos-php-sdk-' . self::VERSION;
    }
}

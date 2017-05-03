<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 04/05/2017
 * Time: 00:32
 */

namespace app\common\services;

use app\common\facades\Setting;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Cache;

class Check
{
    public static function app()
    {
        if(app()->environment() !== 'production'){

        }
        if(Cache::has('app_auth')){
           if(Cache::get('app_auth')){
               exit(redirect(Url::absoluteWeb('setting.key.index'))->send());
           } else{
               return true;
           }
        }
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        Setting::get('auth.key');
        $update->setBasicAuth($key, $secret);

        if ($update->checkUpdate() === false) {
            Cache::put('app_auth',false,360);
            exit(redirect(Url::absoluteWeb('setting.key.index'))->send());
        }
        Cache::put('app_auth',true,360);
        return true;
    }

}
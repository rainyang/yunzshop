<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/4/5
 * Time: 上午10:00
 */

namespace app\common\models;

class AccountWechats extends BaseModel
{
    public $table = 'account_wechats';

    public static function getAccountByUniacid($uniacid)
    {
        return self::where('uniacid', $uniacid)->first();
    }

    /**
     * 设置公众号
     * @param $account
     */
    public static function setConfig($account)
    {
        if($account){
            \Config::set('wechat.app_id',$account->key);
            \Config::set('wechat.secret',$account->secret);
            \Config::set('wechat.token',$account->token);
            \Config::set('wechat.aes_key',$account->encodingaeskey);
        }
        return;
    }
}
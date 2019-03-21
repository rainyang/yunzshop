<?php

namespace app\frontend\modules\wechat\controllers;

use app\common\components\BaseController;
use app\common\models\AccountWechats;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends BaseController
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $uniacid = request('id');

        //设置uniacid
        \config::set('app.global', array_merge(\config::get('app.global'), ['uniacid' => $uniacid]));
        \Setting::$uniqueAccountId = $uniacid;
        //设置公众号信息
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid($uniacid));
    }

    public function index()
    {
        // 接入判断
        if ( isset( $_GET["signature"] ) && isset( $_GET["timestamp"] ) && isset( $_GET["nonce"] ) && isset( $_GET["echostr"] ) ) {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce     = $_GET["nonce"];
            $token = \Setting::get('plugin.wechat.token');
            $tmpArr    = [ $token, $timestamp, $nonce ];
            sort( $tmpArr, SORT_STRING );
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            if ( $tmpStr == $signature ) {
                \Log::debug('----------公众号接入成功---------',$_GET);
                //      echo $signature;
                //      return  $_GET["echostr"];
                //exit;
            }
        } else {// 不是接入，则触发事件，交给监听者处理.
            // 获取第三方库easyWechat的app对象
            $wechatApp = new \app\common\modules\wechat\WechatApplication();
            $server = $wechatApp->server;
            try {
                $msg = $server->getMessage();// 异常代码
                \Log::debug('----------微信公众号消息---------',$msg);
                event(new \app\common\events\WechatMessage($wechatApp,$msg));
            } catch (\Exception $exception) {
                \Log::debug('----------公众号异常---------',$exception->getMessage());
            }
        }
    }
}
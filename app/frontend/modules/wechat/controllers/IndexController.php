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
        }
        /*
        // 判断接入
        //查询数据库，该公众号是否开启，开启，则不校验，正常接收请求。如果关闭，则校验
        if (empty(\Setting::get('plugin.wechat.status'))) {
            if ($this->checkSignature()) {
                // 打开公众号
                return $_GET['echostr'];
            }
        }
        */
        // 获取第三方库easyWechat的app对象
        $wechatApp = new \app\common\modules\wechat\WechatApplication();
        $server = $wechatApp->server;
        try {
            $server->setMessageHandler(function ($message) use ($wechatApp) {
                // 判断微信消息类型
                switch ($message->MsgType) {
                    case 'event':
                        return '收到事件消息';
                        break;
                    case 'text':
                        if (!empty($message->Content)) {
                            // 查询关键字，交给对应的模块处理
                            $keyword = \app\common\modules\wechat\models\RuleKeyword::getRuleByKeywords($message->Content);
                            if ($keyword->module) {
                                event(new \app\common\events\WechatMessage($wechatApp,$keyword));
                            }
                        }
                        // 获取用户输入的关键字，查询关键字表，得到模块，然后将关键字交给该模块进行处理
                        break;
                    case 'image':
                        return '收到图片消息';
                        break;
                    case 'voice':
                        return '收到语音消息';
                        break;
                    case 'video':
                        return '收到视频消息';
                        break;
                    case 'location':
                        return '收到坐标消息';
                        break;
                    case 'link':
                        return '收到链接消息';
                        break;
                    // ... 其它消息
                    default:
                        return '收到其它消息';
                        break;
                }
            });
            $server->serve()->send();
        } catch (\Exception $exception) {

        }
    }
}
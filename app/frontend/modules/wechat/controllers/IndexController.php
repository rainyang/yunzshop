<?php

namespace app\frontend\modules\wechat\controllers;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\Slide;
use app\frontend\modules\goods\models\Brand;
use Illuminate\Support\Facades\DB;
use app\common\models\Adv;
use app\common\helpers\Cache;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends ApiController
{
    public function index()
    {
        \Log::debug('-------------wechat_start----------------------');
        \Log::debug($_GET);
        \Log::debug('-------------wechat_end------------------------');
        //引入laravel
        require_once __DIR__.'/bootstrap/autoload.php';
        $app = require_once __DIR__.'/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->handle(
            $request = \Illuminate\Http\Request::capture()
        );

        // 判断接入
        //查询数据库，该公众号是否开启，开启，则不校验，正常接收请求。如果关闭，则校验
        if (empty(\Setting::get('plugin.wechat.status'))) {
            if ($this->checkSignature()) {
                // 打开公众号
                \Setting::set('plugin.wechat.status',1);
                return $_GET['echostr'];
            }
        }
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

    // 验证接入数据
    public function checkSignature()
    {
        $token = \Setting::get('plugin.wechat.token');
        $array = array($token, $_GET['timestamp'], $_GET['nonce']);
        sort($array, SORT_STRING);
        $str = implode($array);
        $str = sha1($str);
        if ($_GET['signature'] === $str) {
            return true;
        } else {
            return false;
        }
    }

}
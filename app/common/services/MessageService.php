<?php

namespace app\common\services;

use app\common\events\message\SendMessageEvent;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\Jobs\MessageNoticeJob;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Foundation\Application;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MessageService
{

    /**
     * 消息推送，暂时使用，需要优化
     *
     * @param int $member_id
     * @param int $template_id
     * @param array $params
     * @param string $url
     * @return bool
     */
    public function push($member_id, $template_id, array $params, $url='', $uniacid='')
    {


        if (!$member_id || !$template_id) {
            return false;
        }


        //todo MessageTemp 用法有点乱，需要重构

        $params = MessageTemp::getSendMsg($template_id, $params);
        $template_id = MessageTemp::$template_id;


        if (!$template_id) {
            \Log::error("微信消息推送：MessageTemp::template_id参数不存在");
            return false;
        }


        $memberModel = $this->getMemberModel($member_id);

        $config = $this->getConfiguration($uniacid);

        $app = new Application($config);


        $app = $app->notice;
        $app = $app->uses($template_id);
        $app = $app->andData($params);
        $app = $app->andReceiver($memberModel->hasOneFans->openid);
        $app = $app->andUrl($url);

        $app->send();
        return true;
    }


    /**
     * 会员信息
     *
     * @param $member_id
     * @return bool
     */
    private function getMemberModel($member_id)
    {
        if (!$member_id) {
            \Log::error("微信消息推送：uid参数不存在");
            return false;
        }

        $memberModel = Member::whereUid($member_id)->first();

        if (!isset($memberModel)) {
            \Log::error("微信消息推送：未找到uid:{$member_id}的用户");
            return false;
        }
        if (!$memberModel->isFollow()) {
            \Log::error("微信消息推送：会员uid:{$member_id}未关注公众号");
            return false;
        }

        return $memberModel;
    }


    /**
     * 获取公众号配置信息
     *
     * @return array|bool
     */
    private function getConfiguration($uniacid)
    {
        if ($uniacid) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;
        } else{
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        }

        $accountWechat = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

        if (!isset($accountWechat)) {
            \Log::error("微信消息推送：未找到uniacid:{$uniacid}的配置信息");
            return false;
        }

        return ['app_id' => $accountWechat->key, 'secret' => $accountWechat->secret];
    }








    /*todo 一下代码需要重构，重新分化类功能 2018-03-23 yitian*/


    use DispatchesJobs;


    /**
     * 发送微信模板消息
     *
     * @param $templateId
     * @param $data
     * @param $uid
     * @param string $uniacid
     * @param string $url
     * @return bool
     */
    public static function notice($templateId, $data, $uid, $uniacid = '', $url = '')
    {
        //监听消息通知
        event(new SendMessageEvent([
            'data' => $data,
            'uid' => $uid,
            'url' => $url
        ]));

        $res = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        $app = new Application($options);
        $member = Member::whereUid($uid)->first();
        if (!isset($member)) {
            \Log::error("微信消息推送失败,未找到uid:{$uid}的用户");
            return false;
        }

        if (!$member->isFollow()) {
            return false;
        }
        (new MessageService())->noticeQueue($app->notice, $templateId, $data, $member->hasOneFans->openid, $url);
//        $notice = $app->notice;
//        $notice->uses($templateId)->andData($data)->andReceiver($openId)->send();
    }

    public function noticeQueue($notice, $templateId, $data, $openId, $url)
    {
        $this->dispatch((new MessageNoticeJob($notice, $templateId, $data, $openId, $url)));

    }

    public static function getWechatTemplates()
    {
        $app = app('wechat');
        $notice = $app->notice;
        return $notice->getPrivateTemplates();
    }

    /**
     * 验证"模板消息ID" 是否有效
     * @param $template_id
     * @return array
     */
    public static function verifyTemplateId($template_id)
    {
        $templates = self::getWechatTemplates()->get('template_list');
        if (!isset($templates)) {
            return [
                'status' => -1,
                'msg' => '任务处理通知模板id错误'
            ];
        }
        $template = collect($templates)->where('template_id', $template_id)->first();
        if (!isset($template)) {
            return [
                'status' => -1,
                'msg' => '任务处理通知模板id错误'
            ];
        }
        return [
            'status' => 1
        ];
    }

    /**
     * 发送微信"客服消息"
     * @param $openid
     * @param $data
     * 文本消息: $data = new Text(['content' => 'Hello']);
     * 图文消息:
     * $data = new News([
     * 'title' => 'your_title',
     * 'image' => 'your_image',
     * 'description' => 'your_description',
     * 'url' => 'your_url',
     * ]);
     */
    public static function sendCustomerServiceNotice($openid, $data)
    {
        $app = app('wechat');
        if (array_key_exists('content', $data)) {
            $data = new Text($data); //发送文本消息
        } else {
            $data = new News($data); //发送图文消息
        }
        $app->staff->message($data)->to($openid)->send();
    }
}
<?php

namespace app\common\services;

use app\common\events\message\SendMessageEvent;
use app\common\models\AccountWechats;
use app\common\models\Member;
use app\Jobs\MessageNoticeJob;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Foundation\Application;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MessageService
{
    use DispatchesJobs;

    /**
     * 发送微信模板消息
     * @param $templateId
     * @param $data
     * @param $openId
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
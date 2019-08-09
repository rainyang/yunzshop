<?php


namespace app\common\listeners\order;

use app\common\events\order\AfterOrderSentEvent;
use app\common\services\aliyun\AliyunSMS;
use Illuminate\Contracts\Events\Dispatcher;

class AfterOrderSentListener
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderSentEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderSentEvent $event)
    {
        $set = \Setting::get('shop.sms');
        if($set['type'] != 3 && $set['aly_templateSendMessageCode'] == null){
            return false;
        }
        //查询手机号
        $mobile = \app\common\models\Member::find($event->getOrderModel()->uid)->mobile;
        //todo 发送短信
        $aly_sms = new AliyunSMS(trim($set['aly_appkey']), trim($set['aly_secret']));
        $response = $aly_sms->sendSms(
                $set['aly_signname'], // 短信签名
                $set['aly_templateSendMessageCode'], // 发货提醒短信
                $mobile // 短信接收者
//                Array(  // 短信模板中字段的值
//                    "number" => $code
//                )
        );
        if ($response->Code == 'OK' && $response->Message == 'OK') {
             \Log::debug('阿里云短信发送成功');
        } else {
            \Log::debug($response->Message);
        }
        return true;
    }

}
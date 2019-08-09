<?php


namespace app\common\services;

use app\common\services\aliyun\AliyunSMS;
use app\common\models\UniAccount;
use app\backend\modules\member\models\Member;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;

class SmsBalance
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u ){
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $balanceSet = \Setting::get('finance.balance');
            if($balanceSet['sms_send'] == 0){
                continue;
            }
            $smsHour = explode(":", str_replace('：',':',$balanceSet['sms_hour']));
            if(count($smsHour) == 2){
                $time = $smsHour['1'].' '.$smsHour['0'].' * * * *';
            }else{
                $time = '0 '.$smsHour['0'].' * * * *';
            }

            if($balanceSet['sms_send'] ==1 and $balanceSet['sms_hour'] !== null){
                $events->listen('cron.collectJobs', function () use ($time) {
                    \Cron::add('smsMeaggeToMemberMobile', $time, function() {
                        $this->handle();
                    });
                });
            }
        }
    }

    /**
     * 定时发送短信
     * @return bool
     */
    public function handle()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $balanceSet = \Setting::get('finance.balance');
            //sms_send 是否开启
            if($balanceSet['sms_send'] == 0){
                continue;
            }
            $smsSet = \Setting::get('shop.sms');
            //sms_hour 时间
            //sms_hour_amount 金额
            if ($smsSet['type'] != 3 && $smsSet['aly_templateBalanceCode'] == null) {
                return false;
            }
            //查询余额,获取余额超过该值的用户，并把没有手机号的筛选掉
            $mobile = Member::uniacid()
                ->select('uid', 'mobile', 'credit2')
                ->whereNotNull('mobile')
                ->where('credit2', '>', $balanceSet['sms_hour_amount'])
                ->get();
            if ($mobile) {
                $mobile = $mobile->toArray();
            }
            foreach ($mobile as $key => $value) {
                //todo 发送短信
                $aly_sms = new AliyunSMS(trim($smsSet['aly_appkey']), trim($smsSet['aly_secret']));
                $response = $aly_sms->sendSms(
                    $smsSet['aly_signname'], // 短信签名
                    $smsSet['aly_templateBalanceCode'], // 发货提醒短信
                    $mobile, // 短信接收者
                    Array(  // 短信模板中字段的值
                        "blance" => $value['credit2'],
                        'name' => $u['name'],
                    )
                );
                if ($response->Code == 'OK' && $response->Message == 'OK') {
                    \Log::debug($value['mobile'].'阿里云短信发送成功');
                } else {
                    \Log::debug($value['mobile'].'阿里云短信发送失败'.$response->Message);
                }
                return true;
            }
        }
        return true;
    }
}
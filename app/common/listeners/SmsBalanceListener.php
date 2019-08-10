<?php


namespace app\common\listeners;

use app\common\models\UniAccount;
use app\backend\modules\member\models\Member;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;


class SmsBalanceListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
                $events->listen('cron.collectJobs', function ()  {
                    //sms_timing_setting 设置了永久缓存
                    // $time = '10 16 * * * *';
                    if(\Cache::has("sms_timing_setting")) {
                        $set = \Cache::get('sms_timing_setting');
                        if ($set['sms_send'] == 1 and $set['sms_hour'] != null) {
                            $set['time'] = explode(":", str_replace('：', ':', $set['sms_hour']));
                            if (count($set['time']) == 2) {
                                $set['times'] = $set['time']['1'] . ' ' . $set['time']['0'] . ' * * * *';
                            } else {
                                $set['times'] = '0 ' . set['time']['0'] . ' * * * ';
                            }
                            \Cron::add('smsMeaggeToMemberMobile', $set['times'], function () {
                                $this->handle();
                            });
                        }
                    }
                });
    }

    /**
     * 定时发送短信
     * @return bool
     */
    public function handle()
    {
        \Log::debug('定时短信发送');
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $balanceSet = \Setting::get('finance.balance');
            //sms_send 是否开启
            if($balanceSet['sms_send'] == 0){
                \Log::debug($u->uniacid.'未开启');
                continue;
            }
            $smsSet = \Setting::get('shop.sms');
            //sms_hour 时间
            //sms_hour_amount 金额
            if ($smsSet['type'] != 3 && $smsSet['aly_templateBalanceCode'] == null) {
                \Log::debug('短信功能设置'.$smsSet);
                continue;
            }
            //查询余额,获取余额超过该值的用户，并把没有手机号的筛选掉
            $mobile = Member::uniacid()
                ->select('uid', 'mobile', 'credit2')
                ->whereNotNull('mobile')
                ->where('credit2', '>', $balanceSet['sms_hour_amount'])
                ->get();
            if (empty($mobile)) {
                \Log::debug('未找到满足条件会员');
                continue;
            }else{
                $mobile = $mobile->toArray();
            }

            foreach ($mobile as $key => $value) {
                if(!$value['mobile']){
                      continue;
                }
                //todo 发送短信
                $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($smsSet['aly_appkey']), trim($smsSet['aly_secret']));
                $response = $aly_sms->sendSms(
                    $smsSet['aly_signname'], // 短信签名
                    $smsSet['aly_templateBalanceCode'], // 发货提醒短信
                    $value['mobile'], // 短信接收者
                    Array(  // 短信模板中字段的值
                        'preshop' => $u->name,
                        'amount' => $value['credit2'],
                        'endshop' =>$u->name,
                    )
                );
                if ($response->Code == 'OK' && $response->Message == 'OK') {
                    \Log::debug($value['mobile'].'阿里云短信发送成功');
                } else {
                    \Log::debug($value['mobile'].'阿里云短信发送失败'.$response->Message);
                }
            }
        }
        return true;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: b
 * Date: 16/10/12public function __construct()
{
parent::__construct();
}
 * Time: 下午7:52
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Recharge extends YZ
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $trigger = !empty($_REQUEST['trigger']) ? $_REQUEST['trigger'] : 'display';

        $openid    = m('user')->getOpenid();

        if ($trigger == 'display') {
            if ($openid) {
                $json = $this->callMobile('member/recharge');

                if ($json['json']['wechat']['success']) {
                    $btn[] = '微信支付';
                }

                if ($json['json']['yunpay']['success']) {
                    $btn[] = '云支付';
                }

                if ($json['json']['alipay']['success']) {
                    $btn[] = '支付宝支付';
                }

                $res = array('openid'=>$openid, 'money'=>$json['json']['credit'], 'btn'=>$btn, 'acts'=>$json['json']['acts'], 'logid'=>$json['json']['logid']);

                $this->returnSuccess($res);
            } else {
                $this->returnError("请重新登录!");
            }
        } else if ($trigger == 'post') {
            //api  /member/Recharge/index&trigger=post&openid=x&money=x&type=x&logid=x

            if ($openid) {
                $json = $this->callMobile('member/transfer/recharge');

                $this->returnSuccess($json);
            } else {
                $this->returnError("请重新登录!");
            }
        }
    }

    public function log()
    {
        $openid    = m('user')->getOpenid();

        if ($openid) {
            $json = $this->callMobile('member/log');
        } else {
            $this->returnError("请重新登录!");
        }
    }
}
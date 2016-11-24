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

    /**
     * 显示当前余额&余额充值
     *
     * @method post
     * @request /member/Recharge/index&trigger=post&openid=x&money=x&type=x&logid=x
     */
    public function index()
    {
        $trigger = !empty($_REQUEST['trigger']) ? $_REQUEST['trigger'] : 'display';

        $openid    = m('user')->getOpenid();

        if ($trigger == 'display') {
            if ($openid) {
                $json = $this->callMobile('member/recharge');

                if ($json['json']['wechat']['success']) {
                    $btn[] = array('name' => '微信支付', 'value' => 'weixin');
                }

                if ($json['json']['yunpay']['success']) {
                    $btn[] = array('name' => '云支付', 'value' => 'yunpay');
                }

                if ($json['json']['alipay']['success']) {
                    $btn[] = array('name' => '支付宝支付', 'value' => 'alipay');
                }

                $res = array('openid'=>$openid, 'money'=>$json['json']['credit'], 'btn'=>$btn, 'acts'=>$json['json']['acts'], 'logid'=>$json['json']['logid']);

                $this->returnSuccess($res);
            } else {
                $this->returnError("请重新登录!");
            }
        } else if ($trigger == 'post') {
            global $_W;
            if ($openid) {
                $_W['ispost'] = 1;
                $json = $this->callMobile('member/recharge/recharge');

                $this->returnSuccess($json);
            } else {
                $this->returnError("请重新登录!");
            }
        }
    }

    /**
     * 余额充值&提现记录
     *
     * @method get
     * @request member/Recharge/log&type=0,1&page=page     0-充值,1-提现
     */
    public function log()
    {
        $openid    = m('user')->getOpenid();

        if ($openid) {
            $json = $this->callMobile('member/log');

            foreach ($json['json']['list'] as $log) {
                switch ($log['type']) {
                    case 0:
                        $txt1 = '充值金额';
                        break;
                    case 1:
                        $txt1 = '提现金额';
                        break;
                    case 2:
                        $txt1 = '佣金打款';
                        break;
                    default:
                        $txt1 = '';

                }

                if ($log['type'] == 1) {
                    $pre_txt = '手续费:';
                    if ($log['poundage'] > 0) {
                        $txt3 = $pre_txt . $log['poundage'];
                    } else {
                        $txt3 = $pre_txt . '0元';
                    }
                }

                if ($log['status'] == 0) {
                     if ($log['type'] == 0) {
                         $txt2 = '未充值';
                     } else {
                         $txt2 = '申请中';
                     }
                } else if ($log['status'] == 1) {
                    switch ($log['type']) {
                        case 0:
                            $txt2 = '充值成功';
                            break;
                        case 1:
                            $txt2 = '提现成功';
                            break;
                        case 2:
                            $txt2 = '打款成功';
                            break;
                        default:
                            $txt2 = '';
                    }

                } else if ($log['status'] == -1) {
                    if ($log['type'] == 1) {
                        $txt2 = '提现失败';
                    }
                } else if ($log['status'] == -3) {
                    if ($log['type'] == 0) {
                        $txt2 = '充值退款';
                    }
                }

                $res[] = array(
                    'txt1' => $txt1,
                    'money' => $log['money'],
                    'time' => $log['createtime'],
                    'txt2' => $txt2,
                    'txt3' => $txt3,
                );
            }

        } else {
            $this->returnError("请重新登录!");
        }
    }
}
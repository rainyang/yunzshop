<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/11
 * Time: 下午6:27
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Balance extends YZ
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 余额转账
     *
     */
    public function transfer()
    {
        $trigger = !empty($_REQUEST['trigger']) ? $_REQUEST['trigger'] : 'display';

        $openid    = m('user')->getOpenid();
        $member = m('member')->getMember($openid);

        if ($trigger == 'display') {
            if ($member) {
                $res = array('credit2' => $member['credit2'], 'openid' => $openid);

                $this->returnSuccess($res);
            } else {
                $this->returnError("请重新登录!");
            }
        } else if ($trigger == 'post') {
            $parames = array('openid', 'money', 'assigns', 'yunbi'=>'0');

            $json_data = $this->callMobile('member/transfer/submit');

            print_r($json_data);exit;
        }

    }
}
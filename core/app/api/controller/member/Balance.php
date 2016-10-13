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
     * @method get
     * @request /member/Balance/transfer
     * @request /member/Balance/transfer&trigger=get&openid=x&money=x&assigins=x&yunbi=0  提交
     * @reqeust /member/Balance/transfe&trigger=list&type=1  转账记录
     *
     */
    public function transfer()
    {
        $trigger = !empty($_REQUEST['trigger']) ? $_REQUEST['trigger'] : 'display';

        $openid    = m('user')->getOpenid();
        $member = m('member')->getMember($openid);

        if ($trigger == 'display') {
            if ($openid) {
                $res = array('credit2' => $member['credit2'], 'openid' => $openid);

                $this->returnSuccess($res);
            } else {
                $this->returnError("请重新登录!");
            }
        } else if ($trigger == 'get') {
            if ($openid) {
                $json_data = $this->callMobile('member/transfer/submit');

                $this->returnSuccess($json_data);
            } else {
                $this->returnError("请重新登录!");
            }
        } else if ($trigger == 'list') {
            if ($openid) {
                $jsons = $this->callMobile('member/transfer_log');

                foreach ($jsons['json']['list'] as $list) {
                    if ($list['type'] == 1) {
                        $txt = '转让金额';
                        $status = '转让成功';
                    } else {
                        $txt = '受让金额';
                        $status = '受让成功';
                    }

                    $res[] = array('txt'=>$txt,'money'=>$list['money'],'time'=>$list['createtime'],'status'=>$status, 'name'=>$list['name']);
                }

                $this->returnSuccess($res);
            } else {
                $this->returnError("请重新登录!");
            }
        }
    }
}
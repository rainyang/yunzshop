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
    private $_openid;
    private $_withdrawmoney;

    public function __construct()
    {
        parent::__construct();

        $this->_openid = m('user')->getOpenid();
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

        $openid    = $this->_openid;
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

    /**
     * 余额提现
     *
     * @method get
     * @request member/Balance/withdraw
     * @method post
     * @request member/Balance/withdraw&trigger=post&money=money  提交
     */
    public function withdraw()
    {
        $trigger = !empty($_REQUEST['trigger']) ? $_REQUEST['trigger'] : 'display';

        if ($this->_openid) {
            if ($trigger == 'display') {
                $jsons = $this->callMobile('member/withdraw');

                $msg = '';
                if (!$jsons['json']['noinfo']) {
                    $msg ="请补充您的资料后才能申请提现!";
                }

                if ($jsons['json']['credit'] <= 0 ) {
                    $msg = "无余额,无法申请提现!";
                }

                $withdrawmoney = $this->_withdrawmoney = empty($set['withdrawmoney']) ? 0 : $set['withdrawmoney'];

                if ($withdrawmoney > 0 && $withdrawmoney > $jsons['json']['credit']) {
                    $msg = "余额不足!";
                }

                $jsons['json']['msg'] = $msg;
                $this->returnSuccess($jsons['json']);

            } else if ($trigger == 'post') {
                global $_W;
                
                $_W['ispost'] = 1;

                $msg = '';
                if (empty($_REQUEST['money'])) {
                    $msg = '请输入数字金额!';
                }

                if ($_REQUEST['money'] < $this->_withdrawmoney) {
                    $msg = '满 ' . $this->_withdrawmoney . ' 元才能申请提现!';
                }

                if (!empty($msg)) {
                    $this->returnError(array($msg));
                }

                $jsons = $this->callMobile('member/withdraw/submit');

                $this->returnSuccess($jsons);
            }
        } else {
            $this->returnError("请重新登录!");
        }



    }
}
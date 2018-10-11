<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/11
 * Time: 上午11:53
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;

class BalanceRechargeController extends BaseController
{
    private $memberModel;


    public function index()
    {
        $memberInfo =$this->getMemberInfo();
        if (!$this->_member_model) {
            return $this->message('未获取到会员信息', Url::absoluteWeb('finance.balance.member'), 'error');
        }
        if ($this->_member_model && \YunShop::request()->num) {
            $result = $this->rechargeStart();
            if ($result === true) {
                return $this->message('余额充值成功', Url::absoluteWeb('finance.balance.recharge',array('member_id' => $this->_member_model->uid)), 'success');
            }
            $this->error($result);
        }


        return view('finance.balance.recharge', [
            'rechargeMenu'  => $this->getRechargeMenu(),
            'memberInfo'    => $memberInfo,
        ])->render();
    }

    private function getMemberInfo()
    {
        $member_id = $this->getPostMemberId();
        
        $memberModel = Member::getMemberInfoById();
        return $this->memberModel = Member::getMemberInfoById(\YunShop::request()->member_id) ?: false;
    }

    private function getPostMemberId()
    {
        $member_id = \YunShop::request()->member_id;
        if (!$member_id) {
            throw new ShopException('请输入正确的参数');
        }
        return (int)$member_id;
    }

    /**
     * 余额充值菜单
     *
     * @return array
     * @Author yitian */
    private function getRechargeMenu()
    {
        return array(
            'title'     => '余额充值',
            'name'      => '粉丝',
            'profile'   => '会员信息',
            'old_value' => '当前余额',
            'charge_value' => '充值金额',
            'type'      => 'balance'
        );
    }

}

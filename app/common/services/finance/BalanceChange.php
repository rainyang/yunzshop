<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/24
 * Time: 下午5:08
 */

namespace app\common\services\finance;


use app\common\models\finance\Balance;
use app\common\models\Member;
use app\common\services\credit\Credit;

class BalanceChange extends Credit
{


    /**
     * 实现基类中的抽象方法
     * 通过基类 data 中的 member_id 获取会员信息
     * @return mixed
     */
    public function getMemberModel()
    {
        return $this->memberModel = Member::select('uid', 'avatar', 'nickname', 'realname', 'credit2')->where('uid', $this->data['member_id'])->first() ?: false;
    }

    public function recordSave()
    {}

    public function updateMemberCredit()
    {}


    public function validatorData()
    {
        if (!$this->relation()) {
            return '该订单已经提交过，不能重复使用';
        }

        //todo 增加其他验证

    }

    private function relation()
    {
        if ($this->data['relation']) {
            $result = Balance::ofOrderSn($this->data['relation'])->ofSource($this->source)->ofMemberId($this->data['member_id'])->first();
            //dd($result);
            if ($result) {
                return false;
            }
            return $this->data['relation'];
        }
        return $this->createOrderSN();
    }

    /**
     * 生成唯一单号
     * @return string
     */
    public function createOrderSN()
    {
        $ordersn = createNo('BC', true);
        while (1) {
            if (!Balance::ofOrderSn($ordersn)->first()) {
                break;
            }
            $ordersn = createNo('BC', true);
        }
        return $ordersn;
    }


}
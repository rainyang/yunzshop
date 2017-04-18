<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/13
 * Time: 上午11:54
 */

namespace app\common\services\finance;



use app\backend\modules\member\models\Member;
use app\common\exceptions\AppException;

class BalanceService
{
    protected $memberModel;

    protected $balanceModel;


    protected $data;

    protected $service_type;




    public function test()
    {

    }


    //获取会员余额 判断使用
     function getMemberBalance()
    {

    }

    //余额明细记录写入 protected
    protected function updateBalanceRecord()
    {

    }

    //修改会员余额
    protected function updateMemberBalance()
    {

    }

    private function getMemberInfo()
    {
        $this->memberModel = Member::getMemberInfoById(\YunShop::app()->getMemberId());
        if (!$this->memberModel) {
            throw new AppException('未获取到会员信息，请重试！');
        }
    }



    private function getNewMoney()
    {

    }

    private function getRecordData()
    {
        $new_money = getNewMoney();
        return array(
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => \YunShop::app()->getMemberId(),        // 会员ID
            'old_money'     => $this->memberModel->credit2 ?: 0,
            'change_money'  => $this->data['money'],     // 改变余额值 100 或 -100
            'new_money'     => $new_money,
            'type'          => $this->type,
            'service_type'  => $this->service_type,
            'serial_number' => $this->data['serial_number'] ?: '',    // 订单号或流水号，有订单号记录的直接写订单号，未做记录的可以为空
//todo operator 字段值需要如果是插件标示需要主动回去插件ID
            'operator'      => $this->data['operator'],         // 来源，-2会员，-1，订单，0 商城， 1++ 插件ID（没有ID值可以给插件标示）
            'operator_id'   => $this->data['operator_id'],      // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => $this->data['remark'],
        );
    }




    // todo 应该移到余额充值
    public static function attachedTypeName($model)
    {
        switch ($model->type)
        {
            case \app\common\services\finance\Balance::INCOME:
                return '收入';
                break;
            case \app\common\services\finance\Balance::EXPENDITURE:
                return '支出';
                break;
            default:
                return '';
        }

    }


}
<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/27
 * Time: 4:33 PM
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\modules\withdraw\models\Withdraw;
use app\common\exceptions\ShopException;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use Illuminate\Support\Facades\DB;
use app\common\models\Member;
use app\common\models\finance\BalanceRecharge;

class AuditRejectedController extends PreController
{

    /**
     * 提现记录 审核后驳回接口
     */
    public function index()
    {
        $result = $this->auditedRebut();
        if ($result == true) {
//            BalanceNoticeService::withdrawRejectNotice($this->withdrawModel);
            return $this->message('驳回成功', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('驳回失败，请刷新重试', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]), 'error');
    }

    public function validatorWithdrawModel($withdrawModel)
    {
        if ($withdrawModel->status != Withdraw::STATUS_INITIAL) {
            throw new ShopException('状态错误，不符合驳回规则！');
        }
    }

    /**
     * @return bool
     */
    private function auditedRebut()
    {
        DB::transaction(function () {
            $this->_auditedRebut();
        });
        return true;
    }

    /**
     * @throws ShopException
     */
    private function _auditedRebut()
    {
        $result = $this->updateWithdrawStatus();
        if (!$result) {
            throw new ShopException('驳回失败：更新状态失败');
        }
        $result = $this->updateBalance();
        if (!$result) {
            throw new ShopException('驳回失败：更新余额失败');
        }
        $result = $this->updateBalanceMessage();
        if (!$result) {
            throw new ShopException('驳回失败：更新余额明细失败');
        }
    }

    /**
     * @return bool
     */
    private function updateWithdrawStatus()
    {
        $this->withdrawModel->status = Withdraw::STATUS_REBUT;
        $this->withdrawModel->arrival_at = time();

        return $this->withdrawModel->save();
    }

    /**
     * @return bool
     */
    private function updateBalance()
    {
        $id = \YunShop::request()['id'];
        $amounts = $this->withdrawModel->amounts;
        $member_id = $this->withdrawModel->member_id;
        $memberModel = Member::where('uid',$member_id)->first()->toArray();
        //用户余额
        $balance = $memberModel['credit2'];
        $sum = $balance + $amounts;
        if($member_id){
            return Member::where('uid', $member_id)->update(['credit2' => $sum]);
        }
        return false;
    }

    private function updateBalanceMessage(){
        $memberModel = Member::where('uid',$member_id)->first()->toArray();
        //用户余额
        $balance = $memberModel['credit2'];
        $sum = $balance + $amounts;
        $data = array(
            'member_id'     => $member_id = $this->withdrawModel->member_id,
            'remark'        => '余额提现驳回' . $amounts = $this->withdrawModel->amounts . "元",
            'source'        => ConstService::SOURCE_REJECTED,
            'operator'      => ConstService::OPERATOR_SHOP,
//            'operator_id'   => ConstService::OPERATOR_SHOP,
            'uniacid'       => \YunShop::app()->uniacid,
            'old_money'     => $balance,
            'money'         => $this->withdrawModel->amounts,
            'new_money'     => $sum,
//            'type'          => BalanceRecharge::PAY_TYPE_SHOP,
            'ordersn'       => $this->withdrawModel->withdraw_sn,
//            'status'        => BalanceRecharge::PAY_STATUS_ERROR,
        );
        $result = (new BalanceChange())->rejected($data);
        if (!$result) {
            return false;
        }
    }
}

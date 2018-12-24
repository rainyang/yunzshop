<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/27
 * Time: 4:33 PM
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\modules\income\models\Income;
use app\backend\modules\withdraw\models\Withdraw;
use app\common\exceptions\ShopException;
use Illuminate\Support\Facades\DB;
use app\common\models\Member;
use app\backend\modules\finance\controllers\attachedMode;

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
        $amount = $this->withdrawModel->amount;
        $income_ids = explode(',', $this->withdrawModel->type_id);
        $memberModel = Member::where('uid',attachedMode::attachedMode())->first()->toArray();
        //用户余额
        $balance = $memberModel->credit2;
        if (count($income_ids) > 0) {
            return Member::whereIn('id', $income_ids)->update(['credit2' => $balance + $amount]);
        }
        return false;
    }
}

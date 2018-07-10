<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/12 下午4:23
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\modules\withdraw\models\Withdraw;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\services\withdraw\AuditService;

class AuditController extends BaseController
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct()
    {
        parent::__construct();

        $this->withdrawModel = $this->getWithdrawModel();
    }


    public function index()
    {
        list($audit_ids, $invalid_ids, $rebut_ids) = $this->auditResult();

        $this->withdrawModel->audit_ids = $audit_ids;
        $this->withdrawModel->rebut_ids = $rebut_ids;
        $this->withdrawModel->invalid_ids = $invalid_ids;

        $result = (new AuditService($this->withdrawModel))->withdrawAudit();

        if ($result == true) {
            return $this->message('审核成功', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('审核失败，请刷新重试', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]), 'error');
    }


    /**
     * @return array
     */
    private function auditResult()
    {
        $audit_data = $this->getPostAuditData();

        $audit_ids = [];
        $rebut_ids = [];
        $invalid_ids = [];
        foreach ($audit_data as $income_id => $status) {

            switch ($status) {
                case Withdraw::STATUS_AUDIT:
                    $audit_ids[] = $income_id;
                    break;
                case Withdraw::STATUS_INVALID:
                    $invalid_ids[] = $income_id;
                    break;
                case Withdraw::STATUS_REBUT:
                    $rebut_ids[] = $income_id;
                    break;
            }
        }

        return [$audit_ids, $invalid_ids, $rebut_ids];
    }


    /**
     * @return array
     * @throws ShopException
     */
    private function getPostAuditData()
    {
        $audit_data = \YunShop::request()->audit;
        if (!$audit_data) {
            throw new ShopException('数据参数错误');
        }
        return $audit_data;
    }


    /**
     * @param Withdraw $withdrawModel
     * @throws ShopException
     */
    private function validatorWithdrawModel(Withdraw $withdrawModel)
    {
        if (!$withdrawModel) {
            throw new ShopException('数据不存在或已被删除!');
        }
        if ($withdrawModel->status != Withdraw::STATUS_INITIAL && $withdrawModel->status != Withdraw::STATUS_INVALID) {
            throw new ShopException('状态错误，不符合审核规则！');
        }
    }


    /**
     * @return Withdraw
     * @throws ShopException
     */
    private function getWithdrawModel()
    {
        $withdraw_id = $this->getPostWithdrawId();

        $withdrawModel = Withdraw::find($withdraw_id);

        $this->validatorWithdrawModel($withdrawModel);

        return $withdrawModel;
    }


    /**
     * @return int
     * @throws ShopException
     */
    private function getPostWithdrawId()
    {
        $withdraw_id = \YunShop::request()->id;
        if (!$withdraw_id) {
            throw new ShopException('参数错误');
        }
        return $withdraw_id;
    }
}

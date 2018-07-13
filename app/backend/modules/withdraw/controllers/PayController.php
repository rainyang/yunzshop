<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/12 下午4:30
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\modules\withdraw\models\Withdraw;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\services\withdraw\PayedService;

class PayController extends BaseController
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
        $result = (new PayedService($this->withdrawModel))->withdrawPay();
        if ($result == true) {
            return $this->message('打款成功', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('打款成功，请刷新重试', yzWebUrl("finance.withdraw-detail.index", ['id' => $this->withdrawModel->id]), 'error');
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
        if ($withdrawModel->status != Withdraw::STATUS_AUDIT) {
            throw new ShopException('状态错误，不符合打款规则！');
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

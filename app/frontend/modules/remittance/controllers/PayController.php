<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/16
 * Time: 上午11:10
 */

namespace app\frontend\modules\remittance\controllers;


use app\common\components\BaseController;
use app\frontend\modules\process\controllers\Operate;

class PayController extends BaseController
{
    use Operate;

    public function index()
    {
        // todo 支付信息model 与付款流程的支付status相关联
        $this->tonNextState();
        return $this->successJson();
    }
}
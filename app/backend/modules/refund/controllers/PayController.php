<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\modules\refund\services\RefundService;
use app\backend\modules\refund\services\RefundMessageService;
use app\backend\modules\refund\models\RefundApply;


class PayController extends BaseController
{
    private $refundApply;   
    public $transactionActions = ['*'];

    public function preAction()
    {
        parent::preAction();
        $request = \Request::capture();
        $this->validate([
            'refund_id' => 'required',
        ]);
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('退款记录不存在');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $request = request()->input();
        
        $this->validate([
            'refund_id' => 'required'
        ]);

        /**
         * @var $this ->refundApply RefundApply
         */

        $result = (new RefundService)->pay($request['refund_id']);
        if (!$result) {
            throw new ShopException('操作失败');
        }
        if (is_string($result)) {
            redirect($result)->send();
        }
        RefundMessageService::passMessage($this->refundApply);//通知买家
        return $this->message('操作成功');

    }

}
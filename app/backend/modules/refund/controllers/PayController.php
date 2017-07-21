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


class PayController extends BaseController
{
    public $transactionActions = ['*'];

    /**
     * {@inheritdoc}
     */
    public function index(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required'
        ]);

        /**
         * @var $this ->refundApply RefundApply
         */

        $result = (new RefundService)->pay($request);
        if (!$result) {
            throw new ShopException('操作失败');
        }

        return $this->message('操作成功');

    }

}
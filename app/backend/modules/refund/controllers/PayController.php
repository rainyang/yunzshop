<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\common\components\BaseController;
use app\common\modules\refund\services\RefundService;


class PayController extends BaseController
{

    /**
     * 退款
     * @param \Request $request
     * @return mixed
     */
    public function index(\Request $request)
    {
        $this->validate($request, [
            'refund_id' => 'required'
        ]);

        /**
         * @var $this ->refundApply RefundApply
         */
        (new RefundService)->pay($request);
        return $this->message('操作成功');

    }

}
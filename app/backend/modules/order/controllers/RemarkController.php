<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 下午8:12
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\order\Remark;
use app\common\models\Order;

class RemarkController extends BaseController
{
    public function updateRemark()
    {
        if (\YunShop::app()->ispost) {
            $db_remark_model = Remark::where('order_id', \YunShop::request()->order_id)->first();
            if (!$db_remark_model) {
                Remark::create(
                    [
                        'order_id' => \YunShop::request()->order_id,
                        'remark' => \YunShop::request()->remark
                    ]
                );
                show_json(1);
            }
            $db_remark_model->remark = \YunShop::request()->remark;
            $this->updateInvoice( \YunShop::request()->order_id,\YunShop::request()->invoice );
            (new \app\common\services\operation\OrderLog($db_remark_model, 'special'));
            $db_remark_model->save();
            show_json(1);
        }
    }
    //保存图片
    public function updateInvoice($order_id,$invoice)
    {
        $db_invoice=Order::where('id',$order_id)->first();
        if (!$db_invoice){
            $this->errorJson("失败");
        }
        $db_invoice->invoice= $invoice ? $invoice : 0;
        $db_invoice->save();
        return;
    }
}
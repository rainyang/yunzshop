<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/6
 * Time: 下午8:12
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\order\Remark;

class RemarkController extends BaseController
{
    public function updateRemark()
    {
        if (\YunShop::app()->ispost) {
            //\YunShop::request()->id 是订单的id
            //\YunShop::request()->id = 1;
            //echo '<pre>';print_r(\YunShop::request()->order_id);exit;
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
            //dd($db_remark_model);
            $db_remark_model->remark = \YunShop::request()->remark;
            $db_remark_model->save();
            show_json(1);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/18
 * Time: 10:47
 */

namespace app\frontend\modules\order\controllers;


use app\common\models\order\Invoice;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Order;
class RiseController extends ApiController
{
   //获取发票图片
    public function getInvoice()
    {

        $db_remark_model = Order::select('invoice')->where('id', \YunShop::request()->order_id)->first();
        $invoice=yz_tomedia($db_remark_model->invoice);
        return $this->successJson('成功', ['invoice'=>$invoice]);

    }
    public function download()
    {
        $db_remark_model = Order::select('invoice')->where('id', \YunShop::request()->order_id)->first();
        $invoice=yz_tomedia($db_remark_model->invoice);

        $invoice=substr($invoice,0,-3).'pdf';
        return $this->successJson('成功', ['invoice'=>$invoice]);
    }
            //获取订单信息
    public function getData()
    {
        $db_remark_model = Order::select('call','order_sn','invoice_type','invoice')->where('id', \YunShop::request()->order_id)->first();
        if (!$db_remark_model){
            return $this->errorJson("失败");
        }
        $db_remark_model->invoice= 0==$db_remark_model->invoice ? 0 : 1;

        $date=[
            'call'=>$db_remark_model->call,
            'order_sn'=>$db_remark_model->order_sn,
            'invoice_type'=>$db_remark_model->invoice_type,
            'state'=>$db_remark_model->invoice
        ];
        return $this->successJson('ok', $date);
    }

    public function isState()
    {
        $db_remark_model = Order::select('invoice')->where('id', \YunShop::request()->order_id)->first();
        if ('0'===$db_remark_model->invoice){
            return $this->errorJson('未开启发票功能',['state'=>0]);
        }
        return $this->successJson('已开启发票功能',['name'=>'查看发票','state'=>1]);
    }

}
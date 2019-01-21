<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/18
 * Time: 10:47
 */

namespace app\frontend\modules\order\invoice;


use app\common\models\order\Invoice;
use app\common\components\ApiController;
use app\common\facades\Setting;
class Rise extends ApiController
{
    public static function Preservation()
    {
        $result = \YunShop::request()->all;
        if ($result->isEmpty()) {
            self::errorJson('参数错误', '');
        }

        $inv=Invoice::create(['uid'=>1,'uniacid'=>1,'order_id'=>2,'invoice_type'=>1,'rise_type'=>0,'call'=>'01林','company_number'=>1]);
        if (!$inv){
            self::errorJson('失败');
        }
      /*  $inv=new Invoice;
        $inv->uid=1;
        $inv->uniacid=1;
        $inv->invoice_type=1;
        $inv->rise_type=0;
        $inv->call='lin';
        $inv->company_number=123;
        $inv->save();**/
    }

    public function getInvoice(){
        /*$db_remark_model = Remark::select('invoice')->where('order_id', request('order_id'))->first();
        if (substr(yz_tomedia(($db_remark_model->toArray()['invoice'])),0,5) == "https"){
            $invoice=$db_remark_model ? "http".substr(yz_tomedia(($db_remark_model->toArray()['invoice'])),5) : [];
        }else{
            $invoice=yz_tomedia(($db_remark_model->toArray()['invoice']));
        }*/
        $db_remark_model = Invoice::select('invoice')->where('order_id', request('order_id'))->first();
        return yz_tomedia($db_remark_model->toArray()['invoice']);
    }

    public function getCall(){
        $db_remark_model = Invoice::select('call')->where('order_id', request('order_id'))->first();
        return $db_remark_model;
    }


}
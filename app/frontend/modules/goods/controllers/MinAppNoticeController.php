<?php
/**
 * Created 
 * Author: 芸众商城 www.yunzshop.com 
 * Date: 2018/1/24 
 * Time: 下午1:43 
 */

namespace app\frontend\modules\goods\controllers;


use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\models\Store;
use app\common\models\notice\MinAppTemplateMessage;
use app\common\models\MemberMiniAppModel;
use app\common\services\notice\SmallProgramNotice;
/**
 * 小程序发送模板消息
 */
class MinAppNoticeController extends ApiController
{
    /**
     * 支付成功后的 服务号通知消息的发送
     * form_id  表单提交场景下， 为 submit 事件带上的 formId；
     * 支付场景下，为本次支付的 prepay_id
     * $rawPost TODO 其参数解释请参考 sendTemplate()!!!
     */
    public function sendTemplatePaySuccess(){
             \YunShop::request()->storeid;
            $mini_app = MemberMiniAppModel::getFansById(\YunShop::request()->member_id);
            $openId =$mini_app->openid;        //接受人open_id
            $url = \YunShop::request()->url;    //跳转路径
            $form_id = \YunShop::request()->form_id;  //类型

            $min_small = new MinAppTemplateMessage;
            $temp_date = $min_small::getTemp(14);//获取数据表中的数据
            $rawPost = [
                'touser' => $openId ,
                'template_id' =>$temp_date->template_id,
                'page'=>$url,
                'form_id' => $form_id,
            ];
        $arr=explode(',',$temp_date->keyword_id);
        $i=1;
        foreach ($arr as $value){
            $keyword =  'keyword'.$i;
            $rawPost['data'][$keyword]['value'] = explode(":",$value)[1];
            $i++;
        }
        SmallProgramNotice::sendTemplate($rawPost,'sendTemplatePaySuccess');
    }
    //付款成功
    public function paymentSuccess(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '商品名称';
        $rawPost['data']['keyword2']['value'] = '订单编号';
        $rawPost['data']['keyword3']['value'] = '支付时间';
        $rawPost['data']['keyword4']['value'] = '付款金额';
        $rawPost['data']['keyword5']['value'] = '收货地址';
    }

    //待付款
    public function stayPayment(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '商品名称';
        $rawPost['data']['keyword2']['value'] = '单号';
        $rawPost['data']['keyword3']['value'] = '金额';
        $rawPost['data']['keyword4']['value'] = '下单时间';
        $rawPost['data']['keyword5']['value'] = '收货人';
        $rawPost['data']['keyword5']['value'] = '收货地址';
    }

    //交易提醒
    public function paymentFail (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '付款人';
        $rawPost['data']['keyword2']['value'] = '付款金额';
        $rawPost['data']['keyword3']['value'] = '付款时间';
        $rawPost['data']['keyword4']['value'] = '物品名称';
        $rawPost['data']['keyword5']['value'] = '订单编号';
        $rawPost['data']['keyword6']['value'] = '失败原因';
    }

    //订单取消
    public function OrderCancel(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '产品名称';
        $rawPost['data']['keyword2']['value'] = '订单编号';
        $rawPost['data']['keyword3']['value'] = '支付时间';
        $rawPost['data']['keyword4']['value'] = '付款金额';
        $rawPost['data']['keyword5']['value'] = '收货地址';
    }

    //订单发货
    public function OrderShipment(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '物品名称';
        $rawPost['data']['keyword2']['value'] = '发货时间';
        $rawPost['data']['keyword3']['value'] = '订单号';
        $rawPost['data']['keyword4']['value'] = '收货地址';
        $rawPost['data']['keyword5']['value'] = '收货人';
    }

    //购买成功
    public function purchaseSuccess(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '物品名称';
        $rawPost['data']['keyword2']['value'] = '交易单号';
        $rawPost['data']['keyword3']['value'] = '购买时间';
        $rawPost['data']['keyword4']['value'] = '交易金额';
        $rawPost['data']['keyword5']['value'] = '发货单号';
    }

    //购买失败
    public function purchaseFail(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '物品名称';
        $rawPost['data']['keyword2']['value'] = '购买时间';
        $rawPost['data']['keyword3']['value'] = '交易金额1';
        $rawPost['data']['keyword4']['value'] = '失败原因';
        $rawPost['data']['keyword5']['value'] = '交易单号';
    }

    //交易提醒
    public function transactionRemind (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '商户详情';
        $rawPost['data']['keyword2']['value'] = '商品信息';
        $rawPost['data']['keyword3']['value'] = '交易时间';
        $rawPost['data']['keyword4']['value'] = '交易类型';
        $rawPost['data']['keyword5']['value'] = '交易金额';
        $rawPost['data']['keyword6']['value'] = '订单编号';
        $rawPost['data']['keyword7']['value'] = '订单状态';
    }

    //交易失败
    public function transactionFail (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '交易商品';
        $rawPost['data']['keyword2']['value'] = '交易金额';
        $rawPost['data']['keyword3']['value'] = '交易类型';
        $rawPost['data']['keyword4']['value'] = '交易原因';
    }

   //订单支付成功
    public function OrderPayment (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '物品名称';
        $rawPost['data']['keyword2']['value'] = '支付金额';
        $rawPost['data']['keyword3']['value'] = '单号';
        $rawPost['data']['keyword4']['value'] = '下单时间';
        $rawPost['data']['keyword5']['value'] = '订单时间';
    }

    //订单支付成功
    public function OrderPaymentFail (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '物品名称';
        $rawPost['data']['keyword2']['value'] = '金额';
        $rawPost['data']['keyword3']['value'] = '下单时间';
        $rawPost['data']['keyword4']['value'] = '单号';
        $rawPost['data']['keyword5']['value'] = '问题描述';
    }

    //订单取消
    public function OrderPaymentCancel (){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '商品详情';
        $rawPost['data']['keyword2']['value'] = '订单金额';
        $rawPost['data']['keyword3']['value'] = '订单编号';
        $rawPost['data']['keyword4']['value'] = '取消原因';
    }

    //退款通知
    public function RefundNotice(){
        $mid = \YunShop::request()->member_id;
        $rawPost['data']['keyword1']['value'] = '商品名称';
        $rawPost['data']['keyword2']['value'] = '退款金额';
        $rawPost['data']['keyword3']['value'] = '退款时间';
        $rawPost['data']['keyword4']['value'] = '退款原因';
        $rawPost['data']['keyword4']['value'] = '退款原因';
    }



}

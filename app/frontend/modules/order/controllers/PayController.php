<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/9
 * Time: 上午9:38
 */

namespace app\frontend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Order;
use app\frontend\modules\order\services\VerifyPayService;

class PayController extends BaseController
{
    public function index()
    {
        //返回支付方式列表
    }
    public function wechat(){
        //获取微信支付 支付单 数据
    }
    public function alipay(){
        //获取支付宝 支付单 数据
    }
}
<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\services\password\PasswordService;
use app\frontend\modules\coin\deduction\models\Deduction;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public $transactionActions = [''];

    public function index()
    {
        dd(Deduction::whereEnable(1)->get());
        // todo 找到所有开启的抵扣
        $deductions = Deduction::whereEnable(1)->get();
        if($deductions->isEmpty()){
            return 0;
        }
        // 遍历抵扣集合, 从容器中找到对应的抵扣设置注入到抵扣类中
        // 遍历抵扣集合, 实例化订单抵扣类 ,向其传入订单模型和抵扣模型 返回订单抵扣集合
        $orderDeductions = $deductions->map(function($deduction){
            // todo
            $orderDeduction = new OrderDeduction();
            $orderDeduction->setDeduction($deduction);
            // todo
            $orderDeduction->setOrder($this->order);

            return $orderDeduction;
        });
        // 将订单抵扣集合绑定到订单的关联模型(展示,保存)
        // 求和订单抵扣集合中所有已选中的可用金额
        $amount = $orderDeductions->sum(function($orderDeduction){
            if($orderDeduction->isChecked()){
                return $orderDeduction->getUsablePoint();
            }
            return 0;
        });
        // 返回 这个金额
        return $amount;
        //(new MessageService(\app\frontend\models\Order::completed()->first()))->received();
    }

    public function index1()
    {
        // 最简单的单例
        $result = app()->share(function ($var) {
            return $var + 1;
        });
        dd($result(100));

        dd($result(3));
    }

}
<?php
namespace app\frontend\modules\order\controllers;

use app\common\helpers\Url;
use app\frontend\modules\goods\service\GoodsService;
use app\frontend\modules\member\service\MemberService;
use app\frontend\modules\order\services\OrderService;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController
{
    public function index()
    {
        echo __CLASS__;
        echo "<pre>";
        //$_GPC
        print_r(\YunShop::request()->route);
        echo "</pre>";
        echo "<pre>";
        //$_W;

        print_r(\YunShop::app()->config['db']);
        echo "</pre>";

        echo '<a href="' . Url::web('api.v1.test.index', ['id' => 1]) . '" >api</a>';

    }

    public function testGoodsModel()
    {
        $goods_model = GoodsService::getGoodsModel(2);
        var_dump($goods_model->price);
        exit;
    }
    public function testMemberModel(){
        $member_model = MemberService::getCurrentMemberModel();
        var_dump($member_model->uid);
        exit;
    }
    public function testGoodsModels()
    {
        $goods_models = GoodsService::getGoodsModels([1, 2]);
        var_dump($goods_models[0]->price);
        var_dump($goods_models[1]->price);
        exit;
    }

    public function testOrderGoodsModels()
    {
        $param = [
            [
                'goods_id' => 1,
                'total' => 1
            ], [
                'goods_id' => 2,
                'total' => 2
            ]
        ];
        $order_goods_models = OrderService::getOrderGoodsModels($param);


        var_dump($order_goods_models[1]->price);
        var_dump($order_goods_models[1]->total);
        var_dump($order_goods_models[0]->price);
        var_dump($order_goods_models[0]->total);


    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\services\GoodsService;
use app\common\components\BaseController;

class GoodsController extends BaseController
{
    private $goods_id = null;
    private $shopset;
    private $shoppay;

    public function __construct()
    {
        $this->goods_id = (int)\YunShop::request()->id;
        $this->init();
    }

    public function init()
    {

    }

    public function index()
    {
        //$Good = new Goods();
        $goods = Goods::getList();
        echo "<pre>";
        print_r(Goods::getGoodsById(1)->hasManyParams);
        foreach(Goods::getGoodsById(1)->hasManyParams as $v){
            echo $v->title . '<br/>';
        }
        exit;
        $list = GoodsService::getList($goods);
        //$list = Goods::getGoodsById(2);
        echo "<pre>";
        print_r($list);
        exit;
        //或者模板路径可写全  $this->render('order/display/index',['list'=>$list]);
        //以下为简写
        $this->render('list', [
            'list' => $list
        ]);
    }

    public function create()
    {
        $lang = array(
            "shopname" => "商品名称",
            "mainimg" => "商品图片",
            "limittime" => "限时卖时间",
            "shopnumber" => "商品编号",
            "shopprice" => "商品价格",
            "putaway" => "上架",
            "soldout" => "下架",
            "good" => "商品",
            "price" => "价格",
            "repertory" => "库存",
            "copyshop" => "复制商品",
            "isputaway" => "是否上架",
            "shopdesc" => "商品描述",
            "shopinfo" => "商品详情",
            'shopoption' => "商品规格",
            'marketprice' => "销售价格",
            'shopsubmit' => "发布商品"
        );
        //print_r(\YunShop::app());exit;
        $goods = new Goods();
        //print_r($goods);exit;
        $this->render('goods', [
            'goods' => $goods,
            'lang'  => $lang,
        ]);
    }

    public function store()
    {
        $post = \YunShop::request()->goods;
        //print_r($post);exit;

        $goods = new Goods;
        $goods->fill($post);
        $goods->saveOrFail();
        echo 'insert ok!';

    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }

}
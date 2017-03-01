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
use app\common\models\Category;

class GoodsController extends BaseController
{
    private $goods_id = null;
    private $shopset;
    private $shoppay;
    private $goods;

    public function __construct()
    {
        $this->goods_id = (int)\YunShop::request()->id;
        $this->shopset   = m('common')->getSysset('shop');
        $this->init();
    }

    public function init()
    {
        $this->goods = new Goods();
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
        $goods = Goods::getGoodsById(2);
        $params = $goods->hasManyParams;

        $a = $goods->hasManySpecs;
        dd($a);exit;
        exit;
        $allspecs = [];
        //print_r($goods);exit;
        $this->render('goods/goods', [
            'goods' => $this->goods,
            'lang'  => $lang,
            'params'  => $params,
            'allspecs'  => $allspecs,
            'html'  => '',
            'virtual_types' => [],
            'shopset' => $this->shopset
        ]);
    }

    public function store()
    {
        $post = \YunShop::request()->goods;
        //print_r($post);exit;

        $this->goods->fill($post);
        $this->goods->saveOrFail();
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

    /**
     * 获取参数模板
     */
    public function getParamTpl()
    {
        $tag = random(32);
        $this->render('goods/tpl/param', [
            'tag' => $tag,
        ]);
        //include $this->template('web/shop/tpl/param');
    }

    public function getSpecTpl()
    {
        $spec = array(
            "id" => random(32),
            "title" => '',
            'items' => [
                /*"id" => random(32),
                "title" => 'test',
                "show" => 1*/
            ],
        );
        $this->render('goods/tpl/spec', [
            'spec' => $spec,
        ]);
    }

    public function getSpecItemTpl()
    {
        $spec     = array(
            "id" => \YunShop::request()->specid,
        );
        $specitem = array(
            "id" => random(32),
            "title" => \YunShop::request()->title,
            "show" => 1,
            'virtual' => '',
            'title2' => '',
            'thumb' => '',
        );
        $this->render('goods/tpl/spec_item', [
            'spec' => $spec,
            'goods' => $this->goods,
            'specitem' => $specitem,
        ]);
    }

    /**
     * 获取搜索商品
     * @return html
     */
    public function getSearchGoods()
    {
        $keyword = \YunShop::request()->keyword;
        $goods = Goods::getGoodsByName($keyword);
        $goods = set_medias($goods, array('thumb', 'share_icon'));
        include $this->template('web/shop/query');
    }

}
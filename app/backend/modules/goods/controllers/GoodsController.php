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
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\common\helpers\PaginationHelper;


class GoodsController extends BaseController
{
    private $goods_id = null;
    private $shopset;
    private $shoppay;
    private $goods;
    private $lang = null;

    public function __construct()
    {
        $this->lang = array(
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
        //增加商品属性搜索
        $product_attr_list = [
            'isnew' => '新品',
            'ishot' => '热卖',
            'isrecommand' => '推荐',
            'isdiscount' => '促销',
            'issendfree' => '包邮',
            'istime' => '限时',
            'isnodiscount' => '不参与折扣'
        ];

        $total = Goods::getList(\YunShop::app()->uniacid)->toArray();

        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 10;
        $pager = PaginationHelper::show($total, $pindex, $psize);

        $list = Goods::getList(\YunShop::app()->uniacid)->toArray();
        //或者模板路径可写全  $this->render('order/display/index',['list'=>$list]);
        //以下为简写
        $this->render('goods/index', [
            'list' => $list,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'pager' => $pager,
            'product_attr_list' => $product_attr_list,
        ]);
    }

    public function create()
    {

        $params = new GoodsParam();
        //$params = new GoodsParam();

        /*//print_r(\YunShop::app());exit;
        $goods = Goods::getGoodsById(2);
        $params = $goods->hasManyParams;

        $a = $goods->hasManySpecs;
        dd($a);exit;
        exit;*/
        $allspecs = [];
        $this->render('goods/goods', [
            'goods' => $this->goods,
            'lang'  => $this->lang,
            'params'  => $params,
            'allspecs'  => $allspecs,
            'html'  => '',
            'virtual_types' => [],
            'shopset' => $this->shopset
        ]);
    }

    public function store()
    {
        $post = \YunShop::request();

        //$this->goods->fill($post->goods);
        //$this->goods->saveOrFail();
        //GoodsParam::saveParam($post);
        GoodsSpec::saveSpec($post);
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

    /**
     * 获取规格模板
     */
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

    /**
     * 获取规格项模板
     */
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
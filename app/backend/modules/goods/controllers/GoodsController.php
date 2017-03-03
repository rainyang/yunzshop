<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\services\GoodsService;
use app\common\components\BaseController;
use app\backend\modules\goods\services\CategoryService;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\common\events\Event;
use app\common\events\TestGoodsEvent;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;


class GoodsController extends BaseController
{
    private $goods_id = null;
    private $shopset;
    private $shoppay;
    //private $goods;
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
        $this->shopset = Setting::get('shop');
        $this->init();
    }

    public function init()
    {
        //$this->goods = new Goods();
    }

    public function index()
    {

        //增加商品属性搜索
        $product_attr_list = [
            'is_new' => '新品',
            'is_hot' => '热卖',
            'is_recommand' => '推荐',
            'is_discount' => '促销',
        ];
        
        $list = Goods::getList()->toJson();
        \Event::fire(new TestGoodsEvent(['haha' => '哈哈哈']));
        //$list->links();
        //dd($list);
        //或者模板路径可写全  $this->render('order/display/index',['list'=>$list]);
        //以下为简写
        $this->render('goods/index', [
            'list' => $list,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'product_attr_list' => $product_attr_list,
        ]);
    }

    public function create()
    {
        $params = new GoodsParam();
        $goods = new Goods();
        //$params = new GoodsParam();

        /*//print_r(\YunShop::app());exit;
        $goods = Goods::getGoodsById(2);
        $params = $goods->hasManyParams;

        $a = $goods->hasManySpecs;
        dd($a);exit;
        exit;*/

        $catetorys = Category::getAllCategoryGroup();
        //dd($catetorys);
        if ($this->shopset['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        }
        //echo $catetory_menus;exit;

        $allspecs = [];
        $this->render('goods/goods', [
            'goods' => $goods,
            'lang'  => $this->lang,
            'params'  => $params,
            'allspecs'  => $allspecs,
            'html'  => '',
            'catetory_menus'  => $catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ]);
    }

    public function store()
    {
        $requestGoods = \YunShop::request()->goods;
        $goodsModel = new Goods();
        $goodsModel->fill($requestGoods);
        $goodsModel->saveOrFail();
        GoodsParam::saveParam(\YunShop::request());
        GoodsSpec::saveSpec(\YunShop::request());
        echo 'insert ok!';
    }



    public function edit()
    {
        $this->goods_id = \YunShop::request()->id;
        $requestGoods = \YunShop::request()->goods;
        $goodsModel = Goods::getGoodsById($this->goods_id);
        $goodsModel->piclist = unserialize($goodsModel->thumb_url);
        $params = $goodsModel->hasManyParams;
        $catetorys = Category::getAllCategoryGroup();
        //dd($catetorys);
        if ($requestGoods) {
            //将数据赋值到model
            $goodsModel->setRawAttributes($requestGoods);
            //其他字段赋值
            $goodsModel->uniacid = \YunShop::app()->uniacid;
            $goodsModel->id = $this->goods_id;
            //数据保存
            if ($goodsModel->save()) {
                //显示信息并跳转
                return $this->message('商品修改成功', Url::absoluteWeb('goods.goods.index'));
            }else{
                $this->error('商品修改失败');
            }
            /*
            //字段检测
            //$validator = Brand::validator($brandModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($brandModel->save()) {
                    //显示信息并跳转
                    return $this->message('品牌创建成功', Url::absoluteWeb('goods.brand.index'));
                }else{
                    $this->error('品牌创建失败');
                }
            }*/
        }
        if ($this->shopset['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        }
        //echo $catetory_menus;exit;

        $allspecs = [];
        $this->render('goods/goods', [
            'goods' => $goodsModel,
            'lang'  => $this->lang,
            'params'  => $params,
            'allspecs'  => $allspecs,
            'html'  => '',
            'catetory_menus'  => $catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ]);
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
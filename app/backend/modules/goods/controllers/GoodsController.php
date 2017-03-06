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
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\backend\modules\goods\services\GoodsOptionService;
use app\backend\modules\goods\services\GoodsService;
use app\common\components\BaseController;
use app\backend\modules\goods\services\CategoryService;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\common\components\Widget;
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
        //$this->init();
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
        
        $list = Goods::getList()->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $this->render('goods/index', [
            'list' => $list['data'],
            'pager' => $pager,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'product_attr_list' => $product_attr_list,
        ]);
    }

    public function create()
    {
        $params = new GoodsParam();
        $goodsModel = new Goods();
        $requestGoods = \YunShop::request()->goods;
        if ($requestGoods) {
            $sharePost = \YunShop::request()->share;
            $goodsModel->setRawAttributes($requestGoods);
            $goodsModel->widgets = \YunShop::request()->widgets;
            $goodsModel->uniacid = \YunShop::app()->uniacid;
            if ($goodsModel->save()) {
                GoodsParam::saveParam(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                GoodsSpec::saveSpec(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                GoodsOption::saveOption(\YunShop::request(), $goodsModel->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                return $this->message('商品创建成功', Url::absoluteWeb('goods.goods.index'));
            } else {
                $this->error('商品修改失败');
            }
        }



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

    public function edit()
    {
        $this->goods_id = \YunShop::request()->id;
        $requestGoods = \YunShop::request()->goods;
        $goodsModel = Goods::with('hasManyParams')->with('hasManySpecs')->find($this->goods_id);//->getGoodsById(2);
        //dd($goodsModel->hasManyParams->toArray());

        foreach ($goodsModel->hasManySpecs as &$spec)
        {
            $spec['items'] = GoodsSpecItem::where('specid', $spec['id'])->get()->toArray();
        }
        
        $optionsHtml = GoodsOptionService::getOptions($this->goods_id, $goodsModel->hasManySpecs);
        $goodsModel->piclist = unserialize($goodsModel->thumb_url);
        $catetorys = Category::getAllCategoryGroup();
        if ($requestGoods) {
            //将数据赋值到model
            $goodsModel->setRawAttributes($requestGoods);
            $goodsModel->widgets = \YunShop::request()->widgets;
            //其他字段赋值
            $goodsModel->uniacid = \YunShop::app()->uniacid;
            $goodsModel->id = $this->goods_id;
            //数据保存
            if ($goodsModel->save()) {
                GoodsParam::saveParam(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                GoodsSpec::saveSpec(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                GoodsOption::saveOption(\YunShop::request(), $goodsModel->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                //显示信息并跳转
                return $this->message('商品修改成功', Url::absoluteWeb('goods.goods.index'));
            } else {
                $this->error('商品修改失败');
            }
        }
        //获取分类2/3级联动
        if ($this->shopset['catlevel'] == 3) {
            $catetory_menus = CategoryService::tpl_form_field_category_level3(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        } else {
            $catetory_menus = CategoryService::tpl_form_field_category_level2(
                'category', $catetorys['parent'], $catetorys['children'], 0, 0, 0
            );
        }

        //dd($goodsModel);
        $this->render('goods/goods', [
            'goods' => $goodsModel,
            'lang'  => $this->lang,
            'params'  => $goodsModel->hasManyParams->toArray(),
            'allspecs'  => $goodsModel->hasManySpecs->toArray(),
            'html'  => $optionsHtml,
            'catetory_menus'  => $catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ]);
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
        $goodsModel = Goods::find($this->goods_id);

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
            'goods' => $goodsModel,
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
       return $this->render('web/shop/query',['goods'=>$goods]);

    }

}
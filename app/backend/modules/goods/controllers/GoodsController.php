<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

namespace app\backend\modules\goods\controllers;

use app\api\model\Good;
use app\backend\modules\goods\models\Brand;
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
use app\common\models\GoodsCategory;
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
        $this->shopset = Setting::get('shop.category');
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

        $brands = Brand::getBrands()->get()->toArray();

        $requestSearch = \YunShop::request()->search;
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0;
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }

        $catetory_menus = CategoryService::getCategoryMenu(
            [
                'catlevel' => $this->shopset['cat_level'],
                'ids'   => isset($categorySearch) ? array_values($categorySearch) : [],
            ]
        );
        //dd($requestSearch);
        $list = Goods::Search($requestSearch)->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $edit_url = 'goods.goods.edit';
        $delete_url = 'goods.goods.destroy';
        $delete_msg = '确认删除此商品？';

        return view('goods.index', [
            'list' => $list['data'],
            'pager' => $pager,
            //'status' => $status,
            'brands' => $brands,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $catetory_menus,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'product_attr_list' => $product_attr_list,
            'edit_url' => $edit_url,
            'delete_url' => $delete_url,
            'delete_msg' => $delete_msg
        ])->render();
    }

    public function copy()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            $this->error('请传入正确参数.');
        }

        $goodsModel = \app\common\models\Goods::uniacid()->find($id);
        if (!$goodsModel) {
            $this->error('商品不存在.');
        }

        $newGoods = $goodsModel->replicate();
        $newGoods->save();

        $goodsModel->load('hasOneShare', 'hasOneDiscount', 'hasOneGoodsDispatch', 'hasOnePrivilege');
        foreach($goodsModel->getRelations() as $relation => $item){
            if ($item) {
                unset($item->id);
                //dd($item);
                $newGoods->{$relation}()->create($item->toArray());
            }
        }

        $goodsModel->setRelations([]);
        $goodsModel->load('hasManyParams', 'hasManyOptions');
        foreach($goodsModel->getRelations() as $relation => $items){
            foreach($items as $item){
                if ($item) {
                    unset($item->id);
                    $newGoods->{$relation}()->create($item->toArray());
                }
            }
        }

        $goodsModel->setRelations([]);
        $goodsModel->load('hasManyGoodsCategory');
        foreach($goodsModel->getRelations() as $relation => $items){
            foreach($items as $item){
                if ($item) {
                    unset($item->id);
                    $item->goods_id = $newGoods->id;
                    $newGoods->{$relation}()->create($item->toArray());
                }
            }
        }


        //todo, 先复制老的规格,再复制规格项,再更新规格content字段,最后复制option,更新option specs字段
        $goodsSpecs = GoodsSpec::uniacid()->where('goods_id', $goodsModel->id)->get();

        $specItemIds = [];
        $item_ids = [];
        foreach($goodsSpecs as $goodsSpec){
            $newGoodsSpecModel = $goodsSpec->replicate();
            $newGoodsSpecModel->goods_id = $newGoods->id;
            //dd($newGoodsSpecModel);
            $newGoodsSpecModel->save();

            //获取旧的规格项
            $goodsSpecItems = GoodsSpecItem::uniacid()->where("specid", $goodsSpec->id)->get();

            foreach($goodsSpecItems as $goodsSpecItem){
                $newGoodsSpecItem = $goodsSpecItem->replicate();
                $newGoodsSpecItem->specid = $newGoodsSpecModel->id;
                $newGoodsSpecItem->save();

                $items = [
                    'old_item' => $goodsSpecItem->id,
                    'new_item' => $newGoodsSpecItem->id,
                ];

                array_push($item_ids, $items);
                array_push($specItemIds, $newGoodsSpecItem->id);
            }

            $newGoodsSpecModel->content = serialize($specItemIds);
            $newGoodsSpecModel->save();
        }

        $goodsOptions = GoodsOption::uniacid()->where('goods_id', $newGoods->id)->get();
        foreach($goodsOptions as $goodsOption){
            $specs = explode("_", $goodsOption->specs);
            $newSpecs = [];
            foreach($specs as $spec){
                foreach($item_ids as $item){
                    if ($item['old_item'] == $spec){
                        $newSpecs[] = $item['new_item'];
                    }
                }
            }
            $goodsOption->specs = implode("_", $newSpecs);
            $goodsOption->save();
        }

        return $this->message('商品复制成功', Url::absoluteWeb('goods.goods.index'));
    }

    public function create()
    {
        $params = new GoodsParam();
        $goodsModel = new Goods();
        $brands = Brand::getBrands()->get();

        $requestGoods = \YunShop::request()->goods;
        if ($requestGoods) {
            if (isset($requestGoods['thumb_url'])) {
                $requestGoods['thumb_url'] = serialize(
                    array_map(function ($item) {
                        return tomedia($item);
                    }, $requestGoods['thumb_url'])
                );
            }

            $goodsModel->setRawAttributes($requestGoods);
            $goodsModel->widgets = \YunShop::request()->widgets;
            $goodsModel->uniacid = \YunShop::app()->uniacid;

            $validator = $goodsModel->validator($goodsModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($goodsModel->save()) {
                    //dd($goodsModel);
                    GoodsService::saveGoodsCategory($goodsModel, \YunShop::request()->category, $this->shopset);
                    GoodsParam::saveParam(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                    GoodsSpec::saveSpec(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                    GoodsOption::saveOption(\YunShop::request(), $goodsModel->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                    return $this->message('商品创建成功', Url::absoluteWeb('goods.goods.index'));
                } else {
                    !session()->has('flash_notification.message') && $this->error('商品创建失败');
                }
            }
        }

        $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level']]);
        //dd($brands->toArray());
        $allspecs = [];
        return view('goods.goods', [
            'goods' => $goodsModel,
            'lang' => $this->lang,
            'params' => $params->toArray(),
            'brands' => $brands->toArray(),
            'allspecs' => $allspecs,
            'html' => '',
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ])->render();
    }

    public function edit()
    {
        $this->goods_id = intval(\YunShop::request()->id);

        if (!$this->goods_id){
            $this->message('请传入正确参数.');
        }

        $requestGoods = \YunShop::request()->goods;
        $goodsModel = Goods::with('hasManyParams')->with('hasManySpecs')->with('hasManyGoodsCategory')->find($this->goods_id);//->getGoodsById(2);
        //dd($goodsModel->hasManyGoodsCategory->toArray());

        //获取规格名及规格项
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['items'] = GoodsSpecItem::where('specid', $spec['id'])->get()->toArray();
        }

        //获取具体规格内容html
        $optionsHtml = GoodsOptionService::getOptions($this->goods_id, $goodsModel->hasManySpecs);

        //商品其它图片反序列化
        $goodsModel->thumb_url = !empty($goodsModel->thumb_url) ? unserialize($goodsModel->thumb_url) : [];
        //$goodsModel->piclist = !empty($goodsModel->thumb_url) ? $goodsModel->thumb_url : [];


        //$catetorys = Category::getAllCategoryGroup();
        if ($requestGoods) {
            //将数据赋值到model
            $requestGoods['thumb'] = tomedia($requestGoods['thumb']);

            if(isset($requestGoods['thumb_url'])){
                $requestGoods['thumb_url'] = serialize(
                    array_map(function($item){
                        return tomedia($item);
                    }, $requestGoods['thumb_url'])
                );
            }

            GoodsCategory::where("goods_id", $goodsModel->id)->first()->delete();
            GoodsService::saveGoodsCategory($goodsModel, \YunShop::request()->category, $this->shopset);

            $goodsModel->setRawAttributes($requestGoods);
            $goodsModel->widgets = \YunShop::request()->widgets;
            //其他字段赋值
            $goodsModel->uniacid = \YunShop::app()->uniacid;
            $goodsModel->id = $this->goods_id;

            $validator = $goodsModel->validator($goodsModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            }
            else {
                //数据保存
                if ($goodsModel->save()) {
                    GoodsParam::saveParam(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                    GoodsSpec::saveSpec(\YunShop::request(), $goodsModel->id, \YunShop::app()->uniacid);
                    GoodsOption::saveOption(\YunShop::request(), $goodsModel->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                    //显示信息并跳转
                    return $this->message('商品修改成功', Url::absoluteWeb('goods.goods.index'));
                } else {
                    !session()->has('flash_notification.message') && $this->error('商品修改失败');
                    //$this->error('商品修改失败');
                }
            }

        }

        $brands = Brand::getBrands()->get();

        //dd($goods_categorys);
        $catetory_menus = '';
        if (isset($goodsModel->hasManyGoodsCategory[0])){
            foreach($goods_categorys = $goodsModel->hasManyGoodsCategory->toArray() as $goods_category){
                $catetory_menus = CategoryService::getCategoryMenu(['catlevel' => $this->shopset['cat_level'], 'ids' => explode(",", $goods_category['category_ids'])]);
            }
        }

        //dd($this->lang);
        return view('goods.goods', [
            'goods' => $goodsModel,
            'lang' => $this->lang,
            'params' => $goodsModel->hasManyParams->toArray(),
            'allspecs' => $goodsModel->hasManySpecs->toArray(),
            'html' => $optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => $brands->toArray(),
            'catetory_menus' => $catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ])->render();
    }

    public function qrcode()
    {

        //$this->error($goods);
    }

    public function displayorder()
    {
        $displayOrders = \YunShop::request()->display_order;
        foreach($displayOrders as $id => $displayOrder){
            $goods = \app\common\models\Goods::find($id);
            $goods->display_order = $displayOrder;
            $goods->save();
        }
        return $this->message('商品排序成功', Url::absoluteWeb('goods.goods.index'));
        //$this->error($goods);
    }

    public function change()
    {
        //dd(\YunShop::request());
        $id = \YunShop::request()->id;
        $field = \YunShop::request()->type;
        $goods = \app\common\models\Goods::find($id);
        $goods->$field = \YunShop::request()->value;
        $goods->save();
        //$this->error($goods);
    }

    public function setProperty()
    {
        $id = \YunShop::request()->id;
        $field = \YunShop::request()->type;
        $data = (\YunShop::request()->data == 1 ? '0' : '1');
        $goods = \app\common\models\Goods::find($id);
        $goods->$field = $data;
        //dd($goods);
        $goods->save();
        echo json_encode(["data" => $data, "result" => 1]);
    }

    public function destroy()
    {
        $id = \YunShop::request()->id;
        $goods = Goods::destroy($id);
        return $this->message('商品删除成功', Url::absoluteWeb('goods.goods.index'));
    }

    /**
     * 获取参数模板
     */
    public function getParamTpl()
    {
        $tag = random(32);
        return view('goods.tpl.param', [
            'tag' => $tag,
        ])->render();
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
        return view('goods/tpl/spec', [
            'spec' => $spec,
        ])->render();
    }

    /**
     * 获取规格项模板
     */
    public function getSpecItemTpl()
    {
        $goodsModel = Goods::find($this->goods_id);

        $spec = array(
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

        return view('goods/tpl/spec_item', [
            'spec' => $spec,
            'goods' => $goodsModel,
            'specitem' => $specitem,
        ])->render();
    }

    /**
     * 获取搜索商品
     * @return html
     */
    public function getSearchGoods()
    {
        $keyword = \YunShop::request()->keyword;
        $goods = Goods::getGoodsByName($keyword);
        //$goods = set_medias($goods, array('thumb', 'share_icon'));
        return view('goods.query', [
            'goods' => $goods
        ])->render();

    }

    public function test()
    {
        $request = [
            'goods' =>
                ['title' => 'title1',],
            'widgets' => [
                'notice' => [
                    'uid' => 7, 'type' => [0, 2]
                ],
                'sale' => [
                    'love_money' => 1,
                    'max_point_deduct' => 2,
                    'max_balance_deduct' => 3,
                    'ed_num' => 4,
                    'ed_money' => 5,
                    'ed_areas' => '太原市;大同市;阳泉市;长治市;晋城市;朔州市;晋中市;运城市;忻州市;临汾市;吕梁市'
                ]

            ]
        ];
        $goods = new Goods($request['goods']);
        $goods->setRawAttributes($request['goods']);
        $goods->widgets = $request['widgets'];
        $goods->save();
    }


    public function getMyLinkGoods()
    {
        if (\YunShop::request()->kw) {
            $goods = Goods::getGoodsByName(\YunShop::request()->kw);
            $goods = set_medias($goods, array('thumb', 'share_icon'));
            echo json_encode($goods); exit;
        }
    }
}
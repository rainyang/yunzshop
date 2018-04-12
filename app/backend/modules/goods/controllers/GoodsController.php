<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
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
use app\backend\modules\goods\models\Sale;
use app\backend\modules\goods\services\CopyGoodsService;
use app\backend\modules\goods\services\CreateGoodsService;
use app\backend\modules\goods\services\EditGoodsService;
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
use app\frontend\modules\coupon\listeners\CouponSend;
use Setting;
use app\common\services\goods\VideoDemandCourseGoods;
use Yunshop\Designer\models\Store;


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
        $this->videoDemand = Setting::get('plugin.video_demand');
    }

    public function index()
    {
        
        //课程商品id集合
        $videoDemand = new VideoDemandCourseGoods();
        $courseGoods_ids = $videoDemand->courseGoodsIds();


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
                return $item !== '';// && $item !== 0;
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

        $list = Goods::Search($requestSearch)->pluginId()->orderBy('display_order', 'desc')->orderBy('yz_goods.id', 'desc')->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());



        $edit_url = 'goods.goods.edit';
        $delete_url = 'goods.goods.destroy';
        $delete_msg = '确认删除此商品？';
        $sort_url = 'goods.goods.displayorder';
        return view('goods.index', [
            'list' => $list,
            'pager' => $pager,
            //课程商品id
            'courseGoods_ids' => $courseGoods_ids,
            //'status' => $status,
            'brands' => $brands,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $catetory_menus,
            'shopset' => $this->shopset,
            'lang' => $this->lang,
            'product_attr_list' => $product_attr_list,
            'yz_url' => 'yzWebUrl',
            'edit_url' => $edit_url,
            'delete_url' => $delete_url,
            'delete_msg' => $delete_msg,
            'sort_url'  => $sort_url,
            'product_attr'  => $requestSearch['product_attr'],
            'copy_url' => 'goods.goods.copy'
        ])->render();
    }

    public function copy()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            $this->error('请传入正确参数.');
        }

        $result = CopyGoodsService::copyGoods($id);
        if (!$result) {
            $this->error('商品不存在.');
        }
        return $this->message('商品复制成功', Url::absoluteWeb('goods.goods.index'));
    }

    public function create(\Request $request)
    {
        $goods_service = new CreateGoodsService($request);
        $result = $goods_service->create();

        if (isset($goods_service->error)) {
            $this->error($goods_service->error);
        }
        if ($result['status'] == 1) {
            return $this->message('商品创建成功', Url::absoluteWeb('goods.goods.index'));
        } else if ($result['status'] == -1) {
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }

            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        return view('goods.goods', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang,
            'params' => $goods_service->params->toArray(),
            'brands' => $goods_service->brands->toArray(),
            'allspecs' => [],
            'html' => '',
            'var' => \YunShop::app()->get(),
            'catetory_menus' => $goods_service->catetory_menus,
            'virtual_types' => [],
            'shopset' => $this->shopset
        ])->render();
    }

    public function edit(\Request $request)
    {
        /*$this->goods_id = intval(\YunShop::request()->id);

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

            $requestGoods['has_option'] = $requestGoods['has_option'] ? $requestGoods['has_option'] : 0;
            if ($requestGoods['has_option'] && !\YunShop::request()['option_ids']) {
                $requestGoods['has_option'] = 0;
                //return $this->message('启用商品规格，必须添加规格项等信息', Url::absoluteWeb('goods.goods.index'));
            }
            //将数据赋值到model
            $requestGoods['thumb'] = tomedia($requestGoods['thumb']);

            if(isset($requestGoods['thumb_url'])){
                $requestGoods['thumb_url'] = serialize(
                    array_map(function($item){
                        return tomedia($item);
                    }, $requestGoods['thumb_url'])
                );
            }

            $category_model = GoodsCategory::where("goods_id", $goodsModel->id)->first();
            if (!empty($category_model)) {
                $category_model->delete();
            }
            GoodsService::saveGoodsCategory($goodsModel, \YunShop::request()->category, $this->shopset);

            $goodsModel->setRawAttributes($requestGoods);
            $goodsModel->widgets = \YunShop::request()->widgets;
            //其他字段赋值
            $goodsModel->uniacid = \YunShop::app()->uniacid;
            $goodsModel->id = $this->goods_id;
            $validator = $goodsModel->validator($goodsModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
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
        }*/

        //todo 所有操作去service里进行，供应商共用此方法。
        $goods_service = new EditGoodsService($request->id, \YunShop::request());
        if (!$goods_service->goods) {
            return $this->message('未找到商品或已经被删除', '', 'error');
        }
        $result = $goods_service->edit();
        if ($result['status'] == 1) {
            return $this->message('商品修改成功', Url::absoluteWeb('goods.goods.index'));
        } else if ($result['status'] == -1){
            if (isset($result['msg'])) {
                $this->error($result['msg']);
            }
            !session()->has('flash_notification.message') && $this->error('商品修改失败');
        }

        //dd($this->lang);
        return view('goods.goods', [
            'goods' => $goods_service->goods_model,
            'lang' => $this->lang,
            'params' => collect($goods_service->goods_model->hasManyParams)->toArray(),
            'allspecs' => collect($goods_service->goods_model->hasManySpecs)->toArray(),
            'html' => $goods_service->optionsHtml,
            'var' => \YunShop::app()->get(),
            'brands' => $goods_service->brands,
            'catetory_menus' => $goods_service->catetory_menus,
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

        if ($field == 'price') {
            $sale = Sale::getList($goods->id);

            if (!empty($sale->max_point_deduct)
                && $sale->max_point_deduct > \YunShop::request()->value) {
                echo json_encode(['status' => -1, 'msg' => '积分抵扣金额大于商品价格']);
                exit;
            }
        }

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
        if (!\YunShop::request()->kw) {
            $postData = file_get_contents('php://input', true);
            $obj=json_decode($postData);
            \YunShop::request()->kw = $obj->kw;
            //dd($obj->kw);
        }

        if (\YunShop::request()->kw) {
            $goods = \app\common\models\Goods::getGoodsByName(\YunShop::request()->kw);
            //判断门店和虚拟插件商品
            foreach ($goods as $key => $item) {
                if ($item['plugin_id'] == 31 || $item['plugin_id'] == 60) {
                    unset($goods[$key]);
                }
            }
            $goods = set_medias($goods, array('thumb', 'share_icon'));

            $goods = collect($goods)->map(function($item) {
                return array_add($item , 'url', yzAppFullUrl('goods/' . $item['id']));
            });

            echo json_encode($goods); exit;
        }
    }
}
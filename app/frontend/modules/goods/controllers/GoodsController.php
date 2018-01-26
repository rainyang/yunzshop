<?php
namespace app\frontend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\GoodsCategory;
use app\common\models\GoodsSpecItem;
use app\frontend\modules\goods\services\GoodsService;
use Illuminate\Support\Facades\DB;
use app\common\services\goods\SaleGoods;
use app\common\services\goods\VideoDemandCourseGoods;
use app\common\models\MemberShopInfo;
use app\frontend\modules\goods\services\GoodsDiscountService;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class GoodsController extends ApiController
{
    protected $publicAction = ['getRecommendGoods'];
    protected $ignoreAction = ['getRecommendGoods'];

    public function getGoods()
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            return $this->errorJson('请传入正确参数.');
        }

        $member = MemberShopInfo::uniacid()->ofMemberId(\YunShop::app()->getMemberId())->withLevel()->first();


        //$goods = new Goods();
        $goodsModel = Goods::uniacid()->with(['hasManyParams' => function ($query) {
            return $query->select('goods_id', 'title', 'value');
        }])->with(['hasManySpecs' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'description');
        }])->with(['hasManyOptions' => function ($query) {
            return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
        }])->with(['hasManyDiscount' => function ($query) use ($member) {
            return $query->where('level_id', $member->level_id);
        }])
        ->with('hasOneShare')
        ->with('hasOneGoodsDispatch')
        ->with('hasOnePrivilege')
        ->with('hasOneSale')
        ->with('hasOneGoodsCoupon')
        ->with(['hasOneBrand' => function ($query) {
            return $query->select('id', 'name');
        }])
        ->find($id);

        if (!$goodsModel) {
            return $this->errorJson('商品不存在.');
        }

        if (!$goodsModel->status) {
            return $this->errorJson('商品已下架.');
        }

        //商品营销
        $goodsModel->goods_sale = $this->getGoodsSale($goodsModel);
        //商品会员优惠
        $goodsModel->member_discount = $this->getDiscount($goodsModel, $member);
        //商品购买优惠卷赠送
        $goodsModel->hasOneGoodsCoupon->coupon_count = count($goodsModel->hasOneGoodsCoupon->coupon);
// dd($goodsModel->toArray());
        $goodsModel->content = html_entity_decode($goodsModel->content);

        if ($goodsModel->has_option) {
            $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
            $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
            $goodsModel->stock = $goodsModel->hasManyOptions->sum('stock');
        }

        $goodsModel->setHidden(
            [
                'deleted_at',
                'created_at',
                'updated_at',
                'cost_price',
                'real_sales',
                'is_deleted',
                'reduce_stock_method',
            ]);
        if ($goodsModel->thumb) {
            $goodsModel->thumb = yz_tomedia($goodsModel->thumb);
        }
        if ($goodsModel->thumb_url) {
            $thumb_url = unserialize($goodsModel->thumb_url);
            foreach ($thumb_url as &$item) {
                $item = yz_tomedia($item);
            }
            $goodsModel->thumb_url = $thumb_url;
        }
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->get();
            foreach ($spec['specitem'] as &$specitem) {
                $specitem['thumb'] = yz_tomedia($specitem['thumb']);
            }
        }
        // todo 商品详情挂件
        if (\Config::get('goods_detail')) {
            foreach (\Config::get('goods_detail') as $key_name => $row) {
                $row_res = $row['class']::$row['function']($id, true);
                if ($row_res) {
                    $goodsModel->$key_name = $row_res;
                }
            }
        }

        if($goodsModel->hasOneShare){
            $goodsModel->hasOneShare->share_thumb = yz_tomedia($goodsModel->hasOneShare->share_thumb);
        }
        $this->setGoodsPluginsRelations($goodsModel);

        //该商品下的推广
        $goodsModel->show_push = SaleGoods::getPushGoods($id);

        //销量等于虚拟销量加真实销量
        $goodsModel->show_sales += $goodsModel->virtual_sales;

        //判断该商品是否是视频插件商品
        $videoDemand = new VideoDemandCourseGoods();
        $goodsModel->is_course = $videoDemand->isCourse($id);

        //return $this->successJson($goodsModel);
        return $this->successJson('成功', $goodsModel);
    }
    private function setGoodsPluginsRelations($goods){
        $goodsRelations = app('GoodsManager')->tagged('GoodsRelations');
        collect($goodsRelations)->each(function($goodsRelation) use($goods){
            $goodsRelation->setGoods($goods);
        });
    }
    public function searchGoods()
    {
        $requestSearch = \YunShop::request()->search;

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }
        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';
        
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0 && $item !== "undefined";
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        $list = Goods::Search($requestSearch)->select('*', 'yz_goods.id as goods_id')
            ->where("status", 1)
            ->where("plugin_id", 0)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function($rows) {
                return collect($rows)->map(function($item, $key) {
                    if ($key == 'thumb' && preg_match('/^images/', $item)) {
                        return replace_yunshop(tomedia($item));
                    } else {
                        return $item;
                    }
                });
            })->toArray();
            $list['data'] = $data;
        }

        if (empty($list)) {
            return $this->errorJson('没有找到商品.');
        }
        return $this->successJson('成功', $list);
    }

    public function getGoodsCategoryList()
    {
        $category_id = intval(\YunShop::request()->category_id);

        if (empty($category_id)) {
            return $this->errorJson('请输入正确的商品分类.');
        }

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';

        $categorys = Category::uniacid()->select("name", "thumb", "id")->where(['id' => $category_id])->first();
        $goodsList = Goods::uniacid()->select('yz_goods.id','yz_goods.id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')
            ->where("category_id", $category_id)
            ->where('status', '1')
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        $categorys->goods = $goodsList;

        if (empty($categorys)) {
            return $this->errorJson('此分类下没有商品.');
        }
        return $this->successJson('成功', $categorys);
    }

    public function getGoodsBrandList()
    {
        $brand_id = intval(\YunShop::request()->brand_id);
        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])){
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';


        if (empty($brand_id)) {
            return $this->errorJson('请输入正确的品牌id.');
        }

        $brand = Brand::uniacid()->select("name", "logo", "id")->where(['id' => $brand_id])->first();

        if (!$brand) {
            return $this->errorJson('没有此品牌.');
        }
        $goodsList = Goods::uniacid()->select('id','id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('status', '1')
            ->where('brand_id', $brand_id)
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();

        if (empty($brand)) {
            return $this->errorJson('此品牌下没有商品.');
        }

        $brand->goods = $goodsList;

        return $this->successJson('成功', $brand);
    }

    public function getRecommendGoods()
    {
        $list = Goods::uniacid()
            ->select('id', 'id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('is_recommand', '1')
            ->whereStatus('1')
            ->orderBy('id', 'desc')
            ->get();
        return $this->successJson('获取推荐商品成功', $list);
    }

    /**
     * 会员折扣后的价格
     * @param  [type] $discountModel [description]
     * @param  [type] $member        [description]
     * @return [type]                [description]
     */
    public function getDiscount($goodsModel, $memberModel)
    {
        $discountModel = $goodsModel->hasManyDiscount[0];
// dd($discountModel);
// dd($goodsModel);
        switch ($discountModel->discount_method) {
            case 1:
                $discount_value = $goodsModel->price * ($discountModel->discount_value / 10);
                break;
            case 2:
                $discount_value = $goodsModel->price - $discountModel->discount_value;
                break;
            default:
                $discount_value = 0;
                break;
        }
// dd($discount_value);
        if ($memberModel->level) {

            if (!$discount_value) {
                $discount_value = $goodsModel->price * ($memberModel->level->discount / 10);
            }

            $data = [
                'level_name' => $memberModel->level->level_name,
                'discount_value' => $discount_value,

            ];
        } else {
            
            $data = [
                'level_name' => '普通会员',
                'discount_value' => $discount_value,

            ];

        }

        return $data['discount_value'] ? $data : [];
    }

    /**
     * 商品的营销
     * @param  [type] $goodsModel [description]
     * @return [type]             [description]
     */
    public function getGoodsSale($goodsModel)
    {

        $data = [
            'ed_num' => '',
            'ed_money' => '',
            'goods_full_reduction' => '',
            'award_balance' => '',
            'point' => '',
            'max_point_deduct' => '',
            'sale_count' => 0,
        ];
        if ($goodsModel->hasOneSale->ed_num) {
            $data['ed_num'] = '本商品满'.$goodsModel->hasOneSale->ed_num.'件包邮';
            $data['sale_count'] += 1;
        }

        if ($goodsModel->hasOneSale->ed_money) {
            $data['ed_money'] = '本商品满￥'.$goodsModel->hasOneSale->ed_money.'元包邮';
            $data['sale_count'] += 1;

        }

        if (ceil($goodsModel->hasOneSale->ed_full) || ceil($goodsModel->hasOneSale->ed_reduction)) {
            $data['goods_full_reduction'] = '本商品满￥'.$goodsModel->hasOneSale->ed_full.'元立减￥'.$goodsModel->hasOneSale->ed_reduction.'元';
            $data['sale_count'] += 1;

        }

        if ($goodsModel->hasOneSale->award_balance) {
            $data['award_balance'] = $goodsModel->hasOneSale->award_balance;
            $data['sale_count'] += 1;

        }

        if ($goodsModel->hasOneSale->point) {
            $data['point'] = $goodsModel->hasOneSale->point;
            $data['sale_count'] += 1;

        }

        if ($goodsModel->hasOneSale->max_point_deduct) {
            $data['max_point_deduct'] = $goodsModel->hasOneSale->max_point_deduct;
            $data['sale_count'] += 1;
            
        }

        
        return $data;
    }
}
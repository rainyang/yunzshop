<?php
namespace app\frontend\modules\shop\controllers;

use app\api\Base;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Category;
use app\common\models\Goods;
use app\common\models\Slide;
use app\frontend\modules\goods\models\Brand;
use Illuminate\Support\Facades\DB;
use app\common\services\goods\VideoDemandCourseGoods;
use app\common\models\Adv;
use app\common\helpers\Cache;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class IndexController extends ApiController
{
    protected $publicAction = ['getDefaultIndex'];

    public function getDefaultIndex()
    {
        $set = Setting::get('shop.category');
        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
        $category = $this->getRecommentCategoryList();
        foreach ($category  as &$item){
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }
        $data = [
            'ads' => $this->getAds(),
            'category' => $category,
            'set' => $set,
            'goods' => $this->getRecommentGoods(),
        ];
        return $this->successJson('成功', $data);
    }
    //获取推荐品牌
    public function getRecommentBrandList()
    {
        $request = Brand::uniacid()->select('id', 'name', 'logo')->where('is_recommend', 1)->get();

        foreach ($request as &$item) {
            if ($item['logo']) {
                $item['logo'] = replace_yunshop(yz_tomedia($item['logo']));
            }
        }
        return $request;
    }

    //获取限时购商品
    public function getTimeLimitGoods()
    {
        $time = time();
        $field = ['id', 'thumb', 'title', 'price', 'market_price'];
        $timeGoods = Goods::uniacid()->select(DB::raw(implode(',', $field)))
            ->whereHas('hasOneGoodsLimitBuy', function ($query) use ($time) {
                $query->where('status', 1)->where('start_time', '<=', $time);
            })
            ->with('hasOneGoodsLimitBuy')
            ->where("is_recommand", 1)
            ->where("status", 1)
            ->where(function ($query) {
                $query->where('plugin_id', 0)
                ->orWhere('plugin_id', 40);
            })
            ->orderBy("display_order", 'desc')
            ->orderBy("id", 'desc')
            ->get();
        if (!empty($timeGoods->toArray())) {
            foreach ($timeGoods as $key => &$value) {
                $value->thumb = yz_tomedia($value->thumb);
                $value->hasOneGoodsLimitBuy->start_time = date('Y/m/d H:i:s',  $value->hasOneGoodsLimitBuy->start_time);
                $value->hasOneGoodsLimitBuy->end_time = date('Y/m/d H:i:s',  $value->hasOneGoodsLimitBuy->end_time);
            }
        }
        return $timeGoods;
    }
    public function getRecommentGoods()
    {
        //$goods = new Goods();
        $field = ['id as goods_id', 'thumb', 'title', 'price', 'market_price'];
        if(!Cache::has('YZ_Index_goodsList')) {

            $goodsList = Goods::uniacid()->select(DB::raw(implode(',', $field)))
                ->where("is_recommand", 1)
                ->where("status", 1)
                ->where(function ($query) {
                    $query->where('plugin_id', 0)
                        ->orWhere('plugin_id', 40);
                })
                ->orderBy("display_order", 'desc')
                ->orderBy("id", 'desc')
                ->get();
            foreach ($goodsList as &$value) {
                $value->thumb = yz_tomedia($value->thumb);
            }
            Cache::put('YZ_Index_goodsList',$goodsList,4200);

        } else {
            $goodsList = Cache::get('YZ_Index_goodsList');

        }
        /*//是否是课程商品
        $videoDemand = new VideoDemandCourseGoods();
        foreach ($goodsList as &$value) {
            $value->thumb = yz_tomedia($value->thumb);
            $value->is_course = $videoDemand->isCourse($value->goods_id);

        }*/
        
        return $goodsList;
    }

    public function getRecommentCategoryList()
    {

        $request = Category::getRecommentCategoryList()
        ->where('is_home','1')
        ->pluginId()
        ->get();
        foreach ($request as &$item) {
            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
        }

        return $request;
    }

    /**
     * @param $goods_id
     * @param null $option_id
     * @return bool|\Illuminate\Database\Eloquent\Model|null|static
     */
    public function getAds()
    {
        $slide = [];
        $slide = Slide::getSlidesIsEnabled()->get();
        if($slide){
            $slide = $slide->toArray();
            foreach ($slide as &$item)
            {
                $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
            }
        }
        return $slide;
    }

    public function getAdv()
    {
        $adv = Adv::first();
        $advs = [];
        if ($adv) {
            $i = 0;
            foreach ($adv->advs as $key => $value) {
                if ($value['img'] || $value['link']) {
                    $advs[$i]['img'] = yz_tomedia($value['img']);
                    $advs[$i]['link'] = $value['link'];
                    $i +=1;
                }
            }    
        }
        return $advs;
    }

}
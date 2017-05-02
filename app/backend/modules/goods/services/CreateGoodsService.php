<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/5/2
 * Time: 上午11:51
 */

namespace app\backend\modules\goods\services;

use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\models\GoodsSpec;
use app\backend\modules\goods\models\GoodsOption;
use Setting;

class CreateGoodsService
{
    public $params;
    public $brands;
    public $request;
    public $error = null;
    public $catetory_menus;
    public $goods_model;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function create()
    {
        $this->params = new GoodsParam();
        $this->goods_model = new Goods();
        $this->brands = Brand::getBrands()->get();

        if ($this->request->goods) {
            if ($this->request->goods['has_option'] && !\YunShop::request()['option_ids']) {
                $this->request->goods['has_option'] = 0;
            }
            if (isset($this->request->goods['thumb_url'])) {
                $this->request->goods['thumb_url'] = serialize(
                    array_map(function ($item) {
                        return tomedia($item);
                    }, $this->request->goods['thumb_url'])
                );
            }

            $this->goods_model->setRawAttributes($this->request->goods);
            $this->goods_model->widgets = \YunShop::request()->widgets;
            $this->goods_model->uniacid = \YunShop::app()->uniacid;

            $validator = $this->goods_model->validator($this->goods_model->getAttributes());
            if ($validator->fails()) {
                $this->error = $validator->messages();
            } else {
                if ($this->goods_model->save()) {
                    GoodsService::saveGoodsCategory($this->goods_model, \YunShop::request()->category, Setting::get('shop.category'));
                    GoodsParam::saveParam(\YunShop::request(), $this->goods_model->id, \YunShop::app()->uniacid);
                    GoodsSpec::saveSpec(\YunShop::request(), $this->goods_model->id, \YunShop::app()->uniacid);
                    GoodsOption::saveOption(\YunShop::request(), $this->goods_model->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                    return ['status' => 1];
                } else {
                    return ['status' => -1];
                }
            }
        }

        $this->catetory_menus = CategoryService::getCategoryMenu(['catlevel' => Setting::get('shop.category')['cat_level']]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/5/2
 * Time: 上午11:51
 */

namespace app\backend\modules\goods\services;

use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\backend\modules\goods\models\GoodsParam;
use app\backend\modules\goods\models\GoodsSpec;
use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\Brand;
use Setting;

class EditGoodsService
{
    public $goods_id;
    public $goods_model;
    public $request;
    public $catetory_menus;
    public $brands;
    public $optionsHtml;

    public function __construct($goods_id, $request)
    {
        $this->goods_id = $goods_id;
        $this->request = $request;
        $this->goods_model = Goods::with('hasManyParams')->with('hasManySpecs')->with('hasManyGoodsCategory')->find($goods_id);
    }

    public function edit()
    {
        //获取规格名及规格项
        foreach ($this->goods_model->hasManySpecs as &$spec) {
            $spec['items'] = GoodsSpecItem::where('specid', $spec['id'])->get()->toArray();
        }

        //获取具体规格内容html
        $this->optionsHtml = GoodsOptionService::getOptions($this->goods_id, $this->goods_model->hasManySpecs);

        //商品其它图片反序列化
        $this->goods_model->thumb_url = !empty($this->goods_model->thumb_url) ? unserialize($this->goods_model->thumb_url) : [];

        if ($this->request) {
            $this->request['has_option'] = $this->request['has_option'] ? $this->request['has_option'] : 0;
            if ($this->request['has_option'] && !$this->request['option_ids']) {
                $this->request['has_option'] = 0;
                //return $this->message('启用商品规格，必须添加规格项等信息', Url::absoluteWeb('goods.goods.index'));
            }
            //将数据赋值到model
            $this->request['thumb'] = tomedia($this->request['thumb']);

            if(isset($this->request['thumb_url'])){
                $this->request['thumb_url'] = serialize(
                    array_map(function($item){
                        return tomedia($item);
                    }, $this->request['thumb_url'])
                );
            }

            $this->goods_model->setRawAttributes($this->request);
            $this->goods_model->widgets = $this->request->widgets;
            //其他字段赋值
            $this->goods_model->uniacid = \YunShop::app()->uniacid;
            $this->goods_model->id = $this->goods_id;
            //数据保存
            if ($this->goods_model->save()) {
                GoodsParam::saveParam($this->request, $this->goods_model->id, \YunShop::app()->uniacid);
                GoodsSpec::saveSpec($this->request, $this->goods_model->id, \YunShop::app()->uniacid);
                GoodsOption::saveOption($this->request, $this->goods_model->id, GoodsSpec::$spec_items, \YunShop::app()->uniacid);
                //显示信息并跳转
                return ['status' => 1];
            } else {
                return ['status' => -1];
            }
        }

        $this->brands = Brand::getBrands()->get();

        if (isset($this->goods_model->hasManyGoodsCategory[0])){
            foreach($goods_categorys = $this->goods_model->hasManyGoodsCategory->toArray() as $goods_category){
                $this->catetory_menus = CategoryService::getCategoryMenu(['catlevel' => Setting::get('shop.category')['cat_level'], 'ids' => explode(",", $goods_category['category_ids'])]);
            }
        }
    }
}
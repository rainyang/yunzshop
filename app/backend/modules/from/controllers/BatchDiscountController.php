<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 15:28
 */

namespace app\backend\modules\from\controllers;


use app\backend\modules\from\models\CategoryDiscount;
use app\backend\modules\goods\models\Category;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\GoodsCategory;
use app\common\models\GoodsDiscount;

class BatchDiscountController extends BaseController
{
    public function index()
    {
        $category = CategoryDiscount::uniacid()->get()->toArray();

        foreach ($category as $k => $item) {
            $category[$k]['category_ids'] = Category::select('id', 'name')->whereIn('id', explode(',', $item['category_ids']))->get()->toArray();
        }
//        dd($category);
        return view('from.discount',[
            'category' => json_encode($category),
        ])->render();
    }

    public function updateSet()
    {
        $id = request()->id;
        $categoryDiscount = CategoryDiscount::find($id);
        $categoryDiscount['category_ids'] = Category::select('id', 'name')->whereIn('id', explode(',', $categoryDiscount['category_ids']))->get()->toArray();

        return $this->successJson('ok', $categoryDiscount);
    }

    public function allSet()
    {
        $set = Setting::get('from.all_set');

        return view('from.all-set',[
            'set' => json_encode($set),
        ])->render();
    }

    public function allSetStore()
    {
        $set_data = request()->form_data;

        Setting::set('from.all_set', $set_data);

        return $this->successJson('ok');
    }

    public function store()
    {
        $levels = MemberLevel::getMemberLevelList();
        $levels = array_merge($this->defaultLevel(), $levels);

        $id = request()->id;
        $categoryDiscount = CategoryDiscount::find($id);
        $categoryDiscount['category_ids'] = Category::select('id', 'name')->whereIn('id', explode(',', $categoryDiscount['category_ids']))->get()->toArray();

        return view('from.set', [
            'levels' => json_encode($levels),
            'categoryDiscount' => $categoryDiscount,
        ])->render();
    }

    public function storeSet()
    {
        $form_data = request()->form_data;
        $categorys = $form_data['search_categorys'];
        foreach ($categorys as $v){
            $categorys_r[] = $v['id'];
        }
        $category_ids = implode(',', $categorys_r);
        $discountModel = new CategoryDiscount();
        $level_discount = array_filter($form_data['discount']);

        $level_id = array_keys($level_discount)[0];
        $dicount_value =current($level_discount);

        $data = [
            'level_id' => $level_id,
            'category_ids' => $category_ids,
            'uniacid' => \YunShop::app()->uniacid,
            'level_discount_type' => $form_data['discount_type'],
            'discount_method' => $form_data['discount_method'],
            'discount_value' => $dicount_value,
            'created_at' => time(),
        ];

//        dd($data);
        $id = $discountModel->insertGetId($data);

        $result = CategoryDiscount::find($id)->toArray();

        $goods_ids = GoodsCategory::select('goods_id')->whereIn('category_id', explode(',', $result['category_ids']))->get()->toArray();

        foreach ($goods_ids as $goods_id) {
            $item_id[] = $goods_id['goods_id'];
        }

        GoodsDiscount::whereIn('goods_id', $item_id)->update([
            'discount_method' => $result['discount_method'],
            'level_id' => $result['level_id'],
            'discount_value' => $result['discount_value'],
        ]);

        $this->successJson('ok');
    }

    public function selectCategory()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $category = Category::getCategorysByName($kwd);
            return $this->successJson('ok', $category);
        }
    }

    public function deleteSet()
    {
        if (CategoryDiscount::find(request()->id)->delete()) {
            return $this->successJson('ok');
        };
    }

    private function defaultLevel()
    {
        return [
            '0'=> [
                'id' => "0",
                'level_name' => \Setting::get('shop.member.level_name') ?: '普通会员'
            ],
        ];
    }
}
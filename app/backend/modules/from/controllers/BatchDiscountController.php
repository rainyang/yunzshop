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

class BatchDiscountController extends BaseController
{
    public function index()
    {
        $category = CategoryDiscount::uniacid()->get()->toArray();
//        dd($category);
        return view('from.discount',[
            'category' => json_encode($category),
        ])->render();
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

        return view('from.set', [
            'levels' => json_encode($levels),
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
        $discountModel->insert($data);

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
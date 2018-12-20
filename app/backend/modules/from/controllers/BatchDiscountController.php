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
        return view('from.discount')->render();
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
//        dd($form_data);
        $discountModel = new CategoryDiscount();
        foreach ($form_data['discount'] as $k => $v) {
            $data[] = [
                'level_id' => intval($k),
                'category_ids' => serialize($categorys_r),
                'uniacid' => \YunShop::app()->uniacid,
                'level_discount_type' => $form_data['discount_type'],
                'discount_method' => $form_data['discount_method'],
                'discount_value' => $v,
            ];
        }
//        dd($data);
        foreach ($data as $discount) {
            $discountModel->insert($discount);
        }

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
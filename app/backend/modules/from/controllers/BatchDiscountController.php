<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/14
 * Time: 15:28
 */

namespace app\backend\modules\from\controllers;


use app\backend\modules\goods\models\Category;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;

class BatchDiscountController extends BaseController
{
    public function index()
    {
        return view('from.discount')->render();
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
        dd(request()->form_data);
        $this->successJson('ok');
    }

    public function selectCategory()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $category = Category::getCategorysByName($kwd);
//            dd($category);
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
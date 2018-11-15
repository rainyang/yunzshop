<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:20
 */

namespace app\frontend\modules\order\controllers;

use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use app\frontend\modules\order\models\Trade;

class GoodsBuyController extends PreOrderController
{
    protected function getMemberCarts()
    {
        $goods_params = [
            'goods_id' => request()->input('goods_id'),
            'total' => request()->input('total'),
            'option_id' => request()->input('option_id'),
        ];

        $result = new MemberCartCollection();
        $result->push(MemberCartService::newMemberCart($goods_params));
        return $result;
    }
    protected function validateParam(){
        $this->validate([
            'goods_id' => 'required|integer',
            'options_id' => 'integer',
            'total' => 'integer|min:1',
        ]);
    }
    public function index()
    {
        $this->validateParam();
        dd((new Trade())->toArray());
        return parent::index();
    }
}
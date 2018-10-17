<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 11:51
 */
namespace app\backend\modules\charts\modules\income\controllers;

use app\common\components\BaseController;
use app\common\models\order\OrderPluginBonus;

class ShopIncomeListController extends BaseController
{
    public function index()
    {
        OrderPluginBonus::getInfoAttribute();
        return view('charts.income.shop_income_list',[

        ])->render();
    }

}
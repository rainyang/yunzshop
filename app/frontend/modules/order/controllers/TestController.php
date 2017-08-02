<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\Recharge\models\OrderModel;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends ApiController
{
    public $transactionActions = [''];
    public function index()
    {
        return ;
        if (\Schema::hasTable('mc_members')) {
            $db_name =\YunShop::app()->config['db']['master']['database'];
            $engine = DB::select("show table status from ".$db_name."  where name='ims_mc_members'");
            if (isset($engine['0']['Engine']) && strtolower($engine['0']['Engine']) == 'myisam') {
                DB::statement("ALTER TABLE ims_mc_members engine = InnoDB");
            }
        }
        //(new MessageService(\app\frontend\models\Order::completed()->first()))->received();
    }

    public function index1()
    {
        // 最简单的单例
        $result = app()->share(function ($var) {
            return $var + 1;
        });
        dd($result(100));

        dd($result(3));
    }

}
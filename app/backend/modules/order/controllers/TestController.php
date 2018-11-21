<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\OrderAddress;
use app\common\services\TestContract;
use Illuminate\Support\Facades\Schema;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends BaseController
{

    public function index()
    {
        \Illuminate\Support\Facades\DB::select('
            update ims_yz_plugin_store_order as so  join ims_yz_order as o on o.id = so.order_id and so.amount=0 set so.amount = if((o.price - so.fee)>0,o.price - so.fee,0);
');
        exit;

    }

    private function test()
    {
        $permissions = \Config::get('menu');
            dd($permissions);
           exit;
        dd($this->getAllNodes($permissions['system']['child']));
    }

    private function getAllNodes($tree)
    {
        $result = [];
        foreach ($tree as $key => $node) {
            if (!isset($node['child'])) {
                $result[$key] = $node;
            } else {
                $result[$key] = $this->getAllNodes($node);
            }
        }
        return $result;
    }
}
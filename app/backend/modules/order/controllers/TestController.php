<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
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
        dd(\YunShop::app()->username);exit;
        $this->test();
        exit;
        //dd(Schema::table('yz_member'));
        //exit;
        $table = 'yz_member';

        collect(Schema::getColumnListing('yz_member'))->each(
            function ($column) use ($table) {
                dd($column);
                return;
                $type = Schema::getColumnType('yz_member', $column);

                $table->$type($column);

                if (Schema::hasColumn('yz_member', $column)) {
                    $table->change();
                }
            }
        );

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
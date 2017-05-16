<?php

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\OrderAddress;
use app\common\services\TestContract;
use Illuminate\Support\Facades\Schema;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 11:34
 */
class TestController extends BaseController
{

    public function index()
    {
        //dd(Schema::table('yz_member'));
        //exit;
        $table = 'yz_member';

        collect(Schema::getColumnListing('yz_member'))->each(
            function ($column) use ($table) {
                dd($column);
                return ;
                $type = Schema::getColumnType('yz_member', $column);

                $table->$type($column);

                if (Schema::hasColumn('yz_member', $column)) {
                    $table->change();
                }
            }
        );

    }

}
<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 19/03/2017
 * Time: 00:48
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;
use app\common\models\goods\GoodsService;
use Yunshop\ServiceFee\models\ServiceFeeModel;
class IndexController extends BaseController
{
    public function index()
    {

        return view('index',[])->render();
    }

    public function changeField()
    {
        $sql = 'ALTER TABLE `' . DB::getTablePrefix() . 'mc_members` MODIFY `pay_password` varchar(30) NOT NULL DEFAULT 0';

        try {
            DB::select($sql);
            echo '数据已修复';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function changeAgeField()
    {
        $sql = 'ALTER TABLE `' . DB::getTablePrefix() . 'mc_members` MODIFY `age` tinyint(3) NOT NULL DEFAULT 0';

        try {
            DB::select($sql);
            echo '数据已修复';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
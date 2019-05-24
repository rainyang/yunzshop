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
        $goods_id =[213,3,310,99];
        $serviceFee = (new ServiceFeeModel())->select(['fee','goods_id','is_open'])->whereIn('goods_id' , $goods_id)->get()->toArray();
        $service = \Setting::get('goods.service');
        $i = 0;
        foreach ($goods_id as $id){
            foreach ($serviceFee as $serviceid){
                if ($id == $serviceid){
                    continue;
                }
                $serviceFee[$i]['goods_id'] = $id;
                $serviceFee[$i]['fee'] = 0;
                $serviceFee[$i]['is_open'] = 0;
                break;
            }
            ++$i;
        }
        $serviceFee['set'] = $service['service'];

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
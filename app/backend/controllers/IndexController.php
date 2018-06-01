<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 19/03/2017
 * Time: 00:48
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\services\Check;
use Illuminate\Support\Facades\DB;

class IndexController extends BaseController
{
    public function index()
    {
        strpos(request()->getBaseUrl(),'/web/index.php') === 0 && Check::setKey();
        //redirect(Url::absoluteWeb('goods.goods.index'))->send();
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
}
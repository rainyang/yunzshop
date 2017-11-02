<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 19/03/2017
 * Time: 00:48
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\services\Check;

class IndexController extends BaseController
{
    public function index()
    {
        strpos(request()->getBaseUrl(),'/web/index.php') === 0 && Check::setKey();
        redirect(Url::absoluteWeb('goods.goods.index'))->send();
        //return view('index',[])->render();
    }
}
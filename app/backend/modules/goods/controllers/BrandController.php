<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\services\BrandService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 上午9:17
 */
class BrandController extends BaseController
{
    public function index()
    {
        $shopset   = m('common')->getSysset('shop');
        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 10;
        
        $total = Brand::getBrandTotal(\YunShop::app()->uniacid);
        $list = Brand::getBrands(\YunShop::app()->uniacid, $pindex, $psize);
        $pager = PaginationHelper::pagination($total, $pindex, $psize);
        
        $this->render('list', [
            'list' => $list,
            'pager' => $pager,
            'shopset' => $shopset
        ]);
    }



}
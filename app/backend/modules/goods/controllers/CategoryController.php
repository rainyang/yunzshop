<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

class CategoryController extends BaseController
{
    
    public function index()
    {

        $shopset   = m('common')->getSysset('shop');
        $list = CategoryService::getLists(Category::getCategorys(\YunShop::app()->uniacid));
        $this->render('list', [
            'list' => $list,
            'shopset' => $shopset
        ]);
    }

    public function addCategory()
    {
        $item = [
            'id'            => '',
            'name'          => '',
            'thumb'         => '',
            'description'   => '',
            'adv_img'       => '',
            'adv_url'       => '',
            'is_home'       => 0,
            'enabled'       => 0,
            'display_order' => 0
        ];

        $this->render('info', [
            'item' => $item,
            'level' => '1'
        ]);
    }

    public function addSave()
    {

        $result = Category::saveAddCategory(CategoryService::saveCategory(\YunShop::request()->category, \YunShop::app()->uniacid));
        if($result) {
            message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
        }
    }

    public function saveAll()
    {
        ca('shop.category.view');
        
        $categorys = CategoryService::processCategory(\YunShop::request()->datas);
        
        //编辑一级分类
        Category::editAllCategorys($categorys['parents'], \YunShop::app()->uniacid);
        //编辑二级分类
        Category::editAllCategorys($categorys['childrens'], \YunShop::app()->uniacid);
        //编辑三级分类
        Category::editAllCategorys($categorys['thirds'], \YunShop::app()->uniacid);
        
        //删除未保存分类
        Category::delCategorys($categorys['cateids'], \YunShop::app()->uniacid);

        message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
    }
}
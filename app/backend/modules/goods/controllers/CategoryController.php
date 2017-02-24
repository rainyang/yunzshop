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
        ca('shop.category.add');

        $level = \YunShop::request()->level ? \YunShop::request()->level : '1';
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';

        $item = [
            'id'            => '',
            'name'          => '',
            'thumb'         => '',
            'description'   => '',
            'adv_img'       => '',
            'adv_url'       => '',
            'is_home'       => 0,
            'enabled'       => 0,
            'display_order' => 0,
            'level'         => $level,
            'parent_id'     => $parent_id
        ];
        $this->render('info', [
            'item' => $item,
            'level' => $level
        ]);
    }

    
    public function addSave()
    {
        ca('shop.category.view');
        $result = Category::saveAddCategory(CategoryService::saveCategory(\YunShop::request()->category, \YunShop::app()->uniacid));
        if($result) {
            Header("Location: ".$this->createWebUrl('goods.category.index'));exit;
            //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
        }
    }

    public function editCategory()
    {
        ca('shop.category.edit');
        $category = Category::getCategory(\YunShop::request()->id);
        $item = CategoryService::editCategory($category);
        $this->render('info', [
            'item' => $item,
            'level' => $category->level
        ]);

    }
    
    public function editSave()
    {
        ca('shop.category.edit');
        $result = Category::saveEditCategory(CategoryService::saveCategory(\YunShop::request()->category, \YunShop::app()->uniacid), \YunShop::request()->id);
        if($result) {
            Header("Location: ".$this->createWebUrl('goods.category.index'));exit;
            //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
        }
    }
    
    public function deletedCategory()
    {
        ca('shop.category.delete');

        $category = Category::getCategory(\YunShop::request()->id);
        if( empty($category) ) {
            Header("Location: ".$this->createWebUrl('goods.category.index'));exit;
        }

        $result = Category::daletedCategory(\YunShop::request()->id);
        if($result) {
            Header("Location: ".$this->createWebUrl('goods.category.index'));exit;
            //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
        }
    }

}
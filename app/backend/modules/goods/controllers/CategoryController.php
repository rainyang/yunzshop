<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Input;

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
        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 10;

        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $total = Category::getCategoryTotal(\YunShop::app()->uniacid, $parent_id);
        $list = Category::getCategorys(\YunShop::app()->uniacid, $pindex, $psize, $parent_id);
        $pager = PaginationHelper::show($total, $pindex, $psize);

        $parent = [];
        if($parent_id > 0) {
            $parent = Category::getCategory($parent_id);
        }

        $this->render('list', [
            'list' => $list,
            'pager' => $pager,
            'parent' => $parent,
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
        $category = \YunShop::request()->category;
        $category['uniacid'] = \YunShop::app()->uniacid;

        $validator = Category::validator($category);

        if($validator->fails()){
            print_r($validator->messages());
        }else {
            $result = Category::saveAddCategory($category);
            if ($result) {
                Header("Location: " . $this->createWebUrl('goods.category.index'));
                exit;
                //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
            }
        }
    }

    public function editCategory()
    {
        ca('shop.category.edit');
        $category = Category::getCategory(\YunShop::request()->id);
        $this->render('info', [
            'item' => $category,
            'level' => $category['level']
        ]);

    }
    
    public function editSave()
    {
        ca('shop.category.edit');
        $category = \YunShop::request()->category;
        $category['uniacid'] = \YunShop::app()->uniacid;
        
        $validator = Category::validator($category);
        if($validator->fails()) {
            print_r($validator->messages());
        }else{
            $result = Category::saveEditCategory($category, \YunShop::request()->id);
            if($result) {
                Header("Location: ".$this->createWebUrl('goods.category.index'));exit;
                //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
            }
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
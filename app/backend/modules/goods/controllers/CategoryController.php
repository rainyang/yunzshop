<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Category;
use app\backend\modules\goods\services\CategoryService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Input;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午1:51
 */

class CategoryController extends BaseController
{
    /**
     * 商品分类列表
     */
    public function index()
    {
        $shopset   = Setting::get('shop');
        $pageSize = 3;
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';
        $parent = Category::getCategory($parent_id);

        $list = Category::getCategorys($parent_id)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('goods.category.list', [
            'list' => $list['data'],
            'parent' => $parent,
            'pager' => $pager,
            'shopset' => $shopset
        ])->render();
    }

    /**
     * 添加商品分类
     */
    public function addCategory()
    {
        ca('shop.category.add');

        $level = \YunShop::request()->level ? \YunShop::request()->level : '1';
        $parent_id = \YunShop::request()->parent_id ? \YunShop::request()->parent_id : '0';

        $categoryModel = new Category();
        $categoryModel->level = $level;
        $categoryModel->parent_id = $parent_id;
        $parent = [];
        if($parent_id > 0) {
            $parent = Category::getCategory($parent_id);
        }
        
        $requestCategory = \YunShop::request()->category;
        if ($requestCategory) {
            //将数据赋值到model
            $categoryModel->setRawAttributes($requestCategory);
            //其他字段赋值
            $categoryModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = Category::validator($categoryModel->getAttributes());
            if ($validator->fails()) {
                //检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($categoryModel->save()) {
                    //显示信息并跳转
                    return $this->message('分类创建成功', Url::absoluteWeb('goods.category.index'));
                }else{
                    $this->error('分类创建失败');
                }
            }
        }

        return view('goods.category.info', [
            'item' => $categoryModel,
            'parent' => $parent,
            'level' => $level
        ])->render();
    }
    
    /**
     * 修改分类
     */
    public function editCategory()
    {
        ca('shop.category.edit');
        $categoryModel = Category::getCategory(\YunShop::request()->id);
        if(!$categoryModel){
            return $this->message('无此记录或已被删除','','error');
        }

        $requestCategory = \YunShop::request()->category;
        if($requestCategory) {
            //将数据赋值到model
            $categoryModel->setRawAttributes($requestCategory);
            //字段检测
            $validator = Category::validator($categoryModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($categoryModel->save()) {
                    //显示信息并跳转
                    return $this->message('分类保存成功', Url::absoluteWeb('goods.category.index'));
                }else{
                    $this->error('分类保存失败');
                }
            }
        }

        $this->render('info', [
            'item' => $categoryModel,
            'level' => $categoryModel->level
        ]);
        return view('goods.category.info', [
            'item' => $categoryModel,
            'level' => $categoryModel->level
        ])->render();
    }

    /**
     * 删除商品分类
     */
    public function deletedCategory()
    {
        $category = Category::getCategory(\YunShop::request()->id);
        if(!$category) {
            return $this->message('无此分类或已经删除','','error');
        }

        $result = Category::daletedCategory(\YunShop::request()->id);
        if($result) {
            return $this->message('删除分类成功',Url::absoluteWeb('goods.category.index'));
        }else{
            return $this->message('删除分类失败','','error');
        }
    }

}
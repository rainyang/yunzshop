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
    /**
     * 商品品牌列表
     */
    public function index()
    {
        $shopset   = m('common')->getSysset('shop');
        $pindex = max(1, intval(\YunShop::request()->page));
        $psize = 5;
        
        $total = Brand::getBrandTotal(\YunShop::app()->uniacid);
        $list = Brand::getBrands(\YunShop::app()->uniacid, $pindex, $psize);
        $pager = PaginationHelper::show($total, $pindex, $psize);
        
        $this->render('list', [
            'list' => $list,
            'pager' => $pager,
            'shopset' => $shopset
        ]);
    }

    /**
     * 添加品牌
     */
    public function addBrand()
    {
        ca('shop.brand.add');
        
        $item = new Brand();

        $brand = \YunShop::request()->brand;
        if($brand) {
            $brand['uniacid'] = \YunShop::app()->uniacid;
            $validator = Brand::validator($brand);

            $item = new Brand($brand);
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                $result = $item->save();
                if ($result) {
                    $this->success('保存成功');
                    Header("Location: " . $this->createWebUrl('goods.brand.index'));
                    exit;
                }else{
                    $this->error('error');
                }
            }
        }

        $this->render('info', [
            'item' => $item
        ]);
    }

    /**
     * 保存添加品牌
     */
    public function addSave()
    {
        ca('shop.brand.add');
        
        $brand = \YunShop::request()->brand;
        $brand['uniacid'] = \YunShop::app()->uniacid;
        $validator = Brand::validator($brand);

        if($validator->fails()){
            print_r($validator->messages());
        }else{
            $result = Brand::saveAddBrand($brand);
            if($result) {
                Header("Location: ".$this->createWebUrl('goods.brand.index'));exit;
                //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
            }
        }
    }

    /**
     * 编辑商品品牌
     */
    public function editBrand()
    {
        ca('shop.brand.edit');
        
        $brand = Brand::getBrand(\YunShop::request()->id);

        $this->render('info', [
            'item' => $brand
        ]);
    }

    /**
     * 保存编辑品牌
     */
    public function editSave()
    {
        ca('shop.brand.edit');
        
        $brand = \YunShop::request()->brand;
        $brand['uniacid'] = \YunShop::app()->uniacid;
        $validator = Brand::validator($brand);

        if($validator->fails()){
            print_r($validator->messages());
        }else{
            $result = Brand::saveEditBrand($brand, \YunShop::request()->id);
            if($result) {
                Header("Location: ".$this->createWebUrl('goods.brand.index'));exit;
                //message('分类保存成功!', $this->createWebUrl('goods.category.index'), 'success');
            }
        }
    }

    /**
     * 删除商品品牌
     */
    public function deletedBrand()
    {
        ca('shop.brand.delete');

        $brand = Brand::getBrand(\YunShop::request()->id);
        if( empty($brand) ) {
            Header("Location: ".$this->createWebUrl('goods.brand.index'));exit;
        }

        $result = Brand::daletedBrand(\YunShop::request()->id);
        if($result) {
            Header("Location: ".$this->createWebUrl('goods.brand.index'));exit;
        }
    }

}
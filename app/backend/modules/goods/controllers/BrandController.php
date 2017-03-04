<?php
namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\backend\modules\goods\services\BrandService;
use app\backend\modules\member\models\TestMember;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;
use Illuminate\Support\Facades\DB;

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
        //$shopset   = Setting::get('shop');

        $pageSize = 5;
        $list = Brand::getBrands($pageSize);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $this->render('list', [
            'list' => $list,
            'pager' => $pager,
            //'shopset' => $shopset
        ]);
    }

    /**
     * 添加品牌
     */
    public function add()
    {
        $brandModel = new Brand();

        $requestBrand = \YunShop::request()->brand;
        if($requestBrand) {
            //将数据赋值到model
            $brandModel->setRawAttributes($requestBrand);
            //其他字段赋值
            $brandModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = Brand::validator($brandModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($brandModel->save()) {
                    //显示信息并跳转
                    return $this->message('品牌创建成功', Url::absoluteWeb('goods.brand.index'));
                }else{
                    $this->error('品牌创建失败');
                }
            }
        }

        $this->render('info', [
            'brandModel' => $brandModel
        ]);
    }


    /**
     * 编辑商品品牌
     */
    public function edit()
    {

        $brandModel = Brand::getBrand(\YunShop::request()->id);
        if(!$brandModel){
            return $this->message('无此记录或已被删除','','error');
        }
        $requestBrand = \YunShop::request()->brand;
        if($requestBrand) {
            //将数据赋值到model
            $brandModel->setRawAttributes($requestBrand);
            //字段检测
            $validator = Brand::validator($brandModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($brandModel->save()) {
                    //显示信息并跳转
                    return $this->message('品牌保存成功', Url::absoluteWeb('goods.brand.index'));
                }else{
                    $this->error('品牌保存失败');
                }
            }
        }

        $this->render('info', [
            'brandModel' => $brandModel
        ]);
    }

    /**
     * 删除商品品牌
     */
    public function deletedBrand()
    {

        $brand = Brand::getBrand(\YunShop::request()->id);
        if(!$brand) {
            return $this->message('无此品牌或已经删除','','error');
        }

        $result = Brand::daletedBrand(\YunShop::request()->id);
        if($result) {
           return $this->message('删除品牌成功',Url::absoluteWeb('goods.brand.index'));
        }else{
            return $this->message('删除品牌失败','','error');
        }
    }

}
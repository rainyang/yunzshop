<?php
/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/3/3
 * Time: 下午4:30
 */

namespace app\backend\modules\goods\controllers;


use app\common\components\BaseController;
use app\backend\modules\goods\models\Dispatch;
use app\backend\modules\goods\models\Area;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Setting;

class DispatchController extends BaseController
{
    /**
     * 配送模板列表
     * @return array $item
     */
    public function index()
    {
        $shopset = Setting::get('shop');
        $pageSize = 5;
        $list = Dispatch::getList($pageSize);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        $this->render('list', [
            'list' => $list,
            'pager' => $pager,
            'shopset' => $shopset
        ]);
    }

    /**
     * 配送模板添加
     * @return array $item
     */
    public
    function add()
    {
        $dispatchModel = new Dispatch();
        $areas = [];
        $citysList = [];
        $areasList = [];
        $provincesList = Area::getProvinces(0);
        foreach ($provincesList as $key => $province) {
            $citysList = Area::getCitysByProvince($province['id']);
        }
        foreach ($citysList) {
            
        }
        $requestDispatch = \YunShop::request()->dispatch;
        if ($requestDispatch) {
            //将数据赋值到model
            $dispatchModel->setRawAttributes($requestDispatch);
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
                } else {
                    $this->error('品牌创建失败');
                }
            }
        }

        $this->render('info', [
            'dispatch' => $dispatchModel,
            'areas'  => $areas,
        ]);
    }

    /**
     * 配送模板编辑
     * @return array $item
     */
    public
    function edit()
    {

    }

    /**
     * 配送模板删除
     * @return array $item
     */
    public
    function delete()
    {

    }

    /**
     * 配送模板排序
     * @return array $item
     */
    public
    function sort()
    {

    }
}
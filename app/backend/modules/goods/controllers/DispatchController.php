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
        $pageSize = 10;
        $list = Dispatch::uniacid()->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('goods.dispatch.list', [
            'list' => $list,
            'pager' => $pager,
            'shopset' => $shopset
        ])->render();
    }

    /**
     * 配送模板添加
     * @return array $item
     */
    public function add()
    {
        $dispatchModel = new Dispatch();
        $areas = Area::getProvinces(0);
        foreach ($areas as &$province) {
            $province['city'] = Area::getCitysByProvince($province['id']);
        }
        $requestDispatch = \YunShop::request()->dispatch;
        if ($requestDispatch) {
            //将数据赋值到model
            $dispatchModel->setRawAttributes($requestDispatch);
            //其他字段赋值
            $dispatchModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = Dispatch::validator($dispatchModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($dispatchModel->save()) {
                    //显示信息并跳转
                    return $this->message('配送模板创建成功', Url::absoluteWeb('goods.dispatch.index'));
                } else {
                    $this->error('配送模板创建失败');
                }
            }
        }
        return view('goods.dispatch.info', [
            'dispatch' => $dispatchModel,
            'parents' => $areas->toArray()
        ])->render();
    }

    /**
     * 配送模板编辑
     * @return array $item
     */
    public function edit()
    {
        $dispatchModel = Dispatch::getOne(\YunShop::request()->id);
        if (!$dispatchModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }
        $areas = Area::getProvinces(0);
        foreach ($areas as &$province) {
            $province['city'] = Area::getCitysByProvince($province['id']);
        }
        $requestDispatch = \YunShop::request()->dispatch;
        if ($requestDispatch) {
            //将数据赋值到model
            $dispatchModel->setRawAttributes($requestDispatch);
            //其他字段赋值
            $dispatchModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = Dispatch::validator($dispatchModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($dispatchModel->save()) {
                    //显示信息并跳转
                    return $this->message('配送模板更新成功', Url::absoluteWeb('goods.dispatch.index'));
                } else {
                    $this->error('配送模板更新失败');
                }
            }
        }

        return view('goods.dispatch.info', [
            'dispatch' => $dispatchModel,
            'parents' => $areas->toArray()
        ])->render();
    }

    /**
     * 配送模板删除
     * @return array $item
     */
    public function delete()
    {
        $dispatch = Dispatch::getOne(\YunShop::request()->id);
        if (!$dispatch) {
            return $this->message('无此配送模板或已经删除', '', 'error');
        }

        $result = Dispatch::deletedDispatch(\YunShop::request()->id);
        if ($result) {
            return $this->message('删除品牌成功', Url::absoluteWeb('goods.dispatch.index'));
        } else {
            return $this->message('删除品牌失败', '', 'error');
        }
    }

    /**
     * 配送模板排序
     * @return array $item
     */
    public function sort()
    {

    }
}
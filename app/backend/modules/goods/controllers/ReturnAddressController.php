<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/25
 * Time: 9:31
 */

namespace app\backend\modules\goods\controllers;

use app\backend\modules\goods\services\DispatchService;
use app\common\components\BaseController;
use app\backend\modules\goods\models\Dispatch;
use app\common\models\member\Address;
use app\backend\modules\goods\models\ReturnAddress;
use app\backend\modules\goods\models\Area;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Street;
use Setting;

class ReturnAddressController extends BaseController
{
    /**
     * 退货地址列表
     * @return array $item
     */
    public function index()
    {
        $pageSize = 10;
        $plugins_id = 0;//商城
        $list = ReturnAddress::uniacid()->where('plugins_id', $plugins_id)->orderBy('id', 'desc')->orderBy('id', 'desc')->paginate($pageSize)->toArray();
//        dd($list);
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        return view('goods.return.list', [
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }

    /**
     * 退货地址添加
     * @return array $item
     */
    public function add()
    {
        $addressModel = new ReturnAddress();
        $requestAddress = \YunShop::request()->address;
        if ($requestAddress) {
            if (!$requestAddress['province_id']) {
                $this->message('请选择省份', '', 'error');
            }
            if (!$requestAddress['city_id']) {
                $this->message('请选择城市', '', 'error');
            }
            if (!$requestAddress['district_id']) {
                $this->message('请选择区域', '', 'error');
            }
            if (!$requestAddress['street_id']) {
                $this->message('请选择街道', '', 'error');
            }
            //将数据赋值到model
            $addressModel->setRawAttributes($requestAddress);
            //其他字段赋值
            $province = Address::find($requestAddress['province_id'])->areaname;
            $city = Address::find($requestAddress['city_id'])->areaname;
            $district = Address::find($requestAddress['district_id'])->areaname;
            $street = Street::find($requestAddress['street_id'])->areaname;
            $addressModel->province_name = $province;
            $addressModel->city_name = $city;
            $addressModel->district_name = $district;
            $addressModel->street_name = $street;
            $addressModel->plugins_id = 0;//商城
            $addressModel->uniacid = \YunShop::app()->uniacid;
            //字段检测
            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //取消其他默认模板
                if($addressModel->is_default){
                    $defaultModel = ReturnAddress::getOneByDefault();
                    if ($defaultModel) {
                        $defaultModel->is_default = 0;
                        $defaultModel->save();
                    }
                }
                //数据保存
                if ($addressModel->save()) {
                    //显示信息并跳转
                    return $this->message('退货地址创建成功', Url::absoluteWeb('goods.return-address.edit',['id' => $addressModel->id]));
                } else {
                    $this->error('退货地址创建失败');
                }
            }
        }
        return view('goods.return.info', [
            'address' => $addressModel,
        ])->render();
    }

    /**
     * 退货地址编辑
     * @return array $item
     */
    public function edit()
    {
        $addressModel = ReturnAddress::find(\YunShop::request()->id);
        if (!$addressModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }
        $requestAddress = \YunShop::request()->address;

        if ($requestAddress) {
            if (!$requestAddress['province_id']) {
                $this->message('请选择省份', '', 'error');
            }
            if (!$requestAddress['city_id']) {
                $this->message('请选择城市', '', 'error');
            }
            if (!$requestAddress['district_id']) {
                $this->message('请选择区域', '', 'error');
            }
            if (!$requestAddress['street_id']) {
                $this->message('请选择街道', '', 'error');
            }
            //将数据赋值到model
            $addressModel->setRawAttributes($requestAddress);
            //其他字段赋值
            $province = Address::find($requestAddress['province_id'])->areaname;
            $city = Address::find($requestAddress['city_id'])->areaname;
            $district = Address::find($requestAddress['district_id'])->areaname;
            $street = Street::find($requestAddress['street_id'])->areaname;
            $addressModel->province_name = $province;
            $addressModel->city_name = $city;
            $addressModel->district_name = $district;
            $addressModel->street_name = $street;
            $addressModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //取消其他默认模板
                if($addressModel->is_default){
                    $defaultModel = ReturnAddress::getOneByDefault();

                    if ($defaultModel && ($defaultModel->id != \YunShop::request()->id) ) {
                        $defaultModel->is_default = 0;
                        $defaultModel->save();
                    }
                }

                //数据保存
                if ($addressModel->save()) {
                    //显示信息并跳转
                    return $this->message('退货地址更新成功', '');
                } else {
                    $this->error('退货地址更新失败');
                }
            }
        }

        return view('goods.return.info', [
            'address' => $addressModel,
        ])->render();
    }

    /**
     * 退货地址删除
     * @return array $item
     */
    public function delete()
    {
        $address = ReturnAddress::getOne(\YunShop::request()->id);
        if (!$address) {
            return $this->message('无此配送模板或已经删除', '', 'error');
        }

        $model = ReturnAddress::find(\YunShop::request()->id);
        if ($model->delete()) {
            return $this->message('删除模板成功', '');
        } else {
            return $this->message('删除模板失败', '', 'error');
        }
    }

    public function addressSave($addressModel) {

    }
}
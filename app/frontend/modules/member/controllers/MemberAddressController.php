<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午8:40
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\BaseController;
use app\common\models\member\Address;
use app\frontend\modules\member\models\MemberAddress;

class MemberAddressController extends BaseController
{
    /*
     * 会员收货地址列表
     *
     * */
    public function index()
    {
        //$memberId = \YunShop::app()->getMemberId();
        $memberId = '9'; //测试使用
        $addressList = MemberAddress::getAddressList($memberId);
        //var_dump(!empty($addressList));
        $msg = "获取列表成功";
        return $this->successJson($msg, $addressList);
    }

    /*
     * 地址JSON数据接口
     *
     * */
    public function address()
    {
        $address = Address::getAllAddress();
        $province = [];
        foreach ($address as $key)
        {
            if ($key['parentid'] == 0 && $key['level'] == 1){
            }

        }
        dd($province);
    }

    /*
     * 添加会员搜获地址
     *
     * */
    public function store()
    {
        $addressModel = new MemberAddress();
        $requestAddress = \YunShop::request();
        if ($requestAddress) {
            $data = array(
                //'uid' => $requestAddress->uid,
                //'uniacid' => \YunShop::app()->uniacid,
                'username' => $requestAddress->username,
                'mobile' => $requestAddress->mobile,
                'zipcode' => '',
                'isdefault' => $requestAddress->isdefault,
                'province' => $requestAddress->province,
                'city' => $requestAddress->city,
                'district' => $requestAddress->district,
                'address' => $requestAddress->address,
            );

            $addressModel->fill($data);

            $memberId = \YunShop::request()->member_id;
            $memberId = '9'; //测试使用
            //验证默认收货地址状态并修改
            $addressList = MemberAddress::getAddressList($memberId);
            if (empty($addressList)) {
                $addressModel->isdefault = '1';
            } elseif ($addressModel->isdefault == '1') {
                //修改默认收货地址
                MemberAddress::cancelDefaultAddress($memberId);
            }

            $addressModel->uid = $memberId;
            $addressModel->uniacid = \YunShop::app()->uniacid;

            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {
                return $this->errorJson($validator->messages());
            }
            if ($addressModel->save()) {
                 return $this->successJson('新增地址成功');
            } else {
                return $this->errorJson("数据写入出错，请重试！");
            }
        }
        return $this->errorJson("未获取到数据，请重试！");
    }

    public function update()
    {
        $addressModel = MemberAddress::getAddressById(\YunShop::request()->address_id);
        if (!$addressModel) {
            return $this->errorJson("未找到数据或已删除");
        }
        $requestAddress = \YunShop::request()->address;
        if ($requestAddress) {
            $addressModel->setRawAttributes($requestAddress);

            $validator = $addressModel->validator($addressModel->getAttributes());
            if ($validator->fails()) {
                return $this->errorJson($validator->message());
            }
            if ($addressModel->isdefault == '1') {
                //$member_id为负值！！！！
                MemberAddress::cancelDefaultAddress($addressModel->member_id);
            }
            if ($addressModel->save()) {
                return $this->successJson();
            } else {
                return $this->errorJson("写入数据出错，请重试！");
            }
        }


    }

    public function destroy()
    {
        $addressId = \YunShop::request()->id;
        $addressModel = MemberAddress::getAddressById($addressId);
        if (!$addressModel) {
            return $this->errorJson("未找到数据或已删除");
        }
        $result = MemberAddress::destroyAddress($addressId);
        if ($result) {
            return $this->successJson();
        } else {
            return $this->errorJson("数据写入出错，删除失败！");
        }
    }


}

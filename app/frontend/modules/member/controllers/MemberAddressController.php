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
        if (!$address) {
            return $this->errorJson('数据收取失败，请联系管理员！');
        }

        $msg = '数据获取成功';
        return $this->successJson($msg, $this->addressService($address));
    }

    /*
     * 添加会员收获地址
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
                //$member_id未附值！！！！
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
        $addressId = \YunShop::request()->address_id;
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

    /*
     * 服务地址接口数据重构
     * */
    private function addressService($address)
    {
        $province = [];
        $city = [];
        $district = [];
        foreach ($address as $key)
        {
            if ($key['parentid'] == 0 && $key['level'] == 1) {
                $province[] = $key;
            } elseif ($key['parentid'] != 0 && $key['level'] == 2 ) {
                $city[] = $key;
            } else {
                $district[] = $key;
            }
        }
        return array(
            'province' => $province,
            'city' => $city,
            'district' => $district,
        );
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午8:40
 */

namespace app\frontend\modules\member\controllers;


use app\frontend\modules\member\models\MemberAddress;

class MemberAddressController
{
    public function index()
    {
        $memberId = \YunShop::request()->member_id;
        $memberId = '57'; //测试使用
        $addressList = MemberAddress::getMemberAddressByMemberId($memberId);
        dd($addressList);
    }

    public function create()
    {

    }

    public function updateById()
    {

    }

    public function destroy()
    {
        $addressId = \YunShop::request()->id;
        $addressModel = MemberAddress::getAddressById($addressId);
        if ($addressModel) {
            $msg = "未找到数据或已删除";
            $this->errorResult($msg);
        }
        $result = MemberAddress::destroyAddress($addressId);
        if ($result) {
            $msg = "删除成功";
            $this->successResult($msg);
        } else {
            $msg = "数据写入出错，删除失败！";
            $this->errorResult($msg);
        }
    }
    protected function errorResult($msg = "操作失败！", $data='')
    {
        $result = array(
            'result' => '0',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }
    protected function successResult($msg = "操作成功。", $data='')
    {
        $result = array(
            'result' => '1',
            'msg' => $msg,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }
}

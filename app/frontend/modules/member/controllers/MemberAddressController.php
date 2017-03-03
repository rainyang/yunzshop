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

    public function delete()
    {

    }
}
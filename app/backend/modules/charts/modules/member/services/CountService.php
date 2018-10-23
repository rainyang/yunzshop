<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/12 下午12:10
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\services;


use app\backend\modules\charts\modules\member\models\Member;


class CountService
{
    public $memberModel;


    public function __construct()
    {
        $this->memberModel = new Member();
    }


    public function getMemberSexStatistic()
    {
        return [
            [
                'name'  => '男',
                'value' => $this->getManSexCount()
            ],
            [
                'name'  => '女',
                'value' => $this->getFemaleSexCount()
            ],
            [
                'name'  => '未知',
                'value' => $this->getUnknownSex()
            ]
        ];
    }


    public function getMemberSourceStatistic()
    {
        return [
            [
                'name'  => '微信授权',
                'value' => $this->getWechatAuthorizeCount()
            ],
            [
                'name'  => '绑定手机',
                'value' => $this->getHasMobile()
            ]
        ];
    }


    public function getManSexCount()
    {
        return $this->memberModel->whereHas('yzMember')->manSex()->count();
    }

    public function getFemaleSexCount()
    {
        return $this->memberModel->whereHas('yzMember')->femaleSex()->count();
    }

    public function getUnknownSex()
    {
        return $this->memberModel->whereHas('yzMember')->UnknownSex()->count();
    }

    public function getHasMobile()
    {
        return $this->memberModel->whereHas('yzMember')->hasMobile()->count();
    }

    public function getWechatAuthorizeCount()
    {
        return $this->memberModel->whereHas('yzMember')->whereHas('hasOneFans')->count();
    }




}

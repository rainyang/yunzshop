<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/12/29 下午3:40
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberAddress;
use app\common\components\BaseController;

class MemberAddressController extends BaseController
{

    public function index()
    {
        //dd($this->getMemberModel());

        return view('member.address.records',[
            'member' => $this->getMemberModel()
        ])->render();
    }

    private function getMemberModel()
    {
        return Member::select('uid', 'nickname', 'realname', 'mobile', 'avatar')
            ->with('address')
            ->where('uid', $this->getMemberId())
            ->first();
    }


    private function getMemberId()
    {
        return trim(\YunShop::request()->member_id);
    }

}

<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/10 上午10:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\charts\modules\member\services\CountService;
use app\common\components\BaseController;

class CountController extends BaseController
{
    protected $memberService;

    public function __construct()
    {
        parent::__construct();
        $this->memberService = new CountService();
    }

    public function index()
    {
        return view('charts.member.count',[
            'gender' => json_encode($this->memberService->getMemberSexStatistic()),
            'source' => json_encode($this->memberService->getMemberSourceStatistic()),
            'member_count' => $this->memberCount(),
        ])->render();
    }



    private function memberCount()
    {
        return [
            [
                'first_name'    => '会员总数',
                'second_name'   => '微信授权会员',
                'third_name'    => '微信授权会员（通过微信授权登录的会员）',
                'first_value'   => $this->memberService->memberModel->whereHas('yzMember')->count(),
                'second_value'  => $this->memberService->getWechatAuthorizeCount(),
                'third_value'   => $this->proportionMath($this->memberService->getWechatAuthorizeCount())
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '绑定手机会员',
                'third_name'    => '绑定手机会员（包含手机号注册和微信绑定手机号的会员）',
                'first_value'   => $this->memberService->memberModel->whereHas('yzMember')->count(),
                'second_value'  => $this->memberService->getHasMobile(),
                'third_value'   => $this->proportionMath($this->memberService->getHasMobile())
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：男',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberModel->whereHas('yzMember')->count(),
                'second_value'  => $this->memberService->getManSexCount(),
                'third_value'   => $this->proportionMath($this->memberService->getManSexCount())
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：女',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberModel->whereHas('yzMember')->count(),
                'second_value'  => $this->memberService->getFemaleSexCount(),
                'third_value'   => $this->proportionMath($this->memberService->getFemaleSexCount())
            ],
            [
                'first_name'    => '会员总数',
                'second_name'   => '性别：未知',
                'third_name'    => '所占比例',
                'first_value'   => $this->memberService->memberModel->whereHas('yzMember')->count(),
                'second_value'  => $this->memberService->getUnknownSex(),
                'third_value'   => $this->proportionMath($this->memberService->getUnknownSex())
            ]
        ];
    }

    private function proportionMath($divisor)
    {
        $member_count = $this->memberService->memberModel->count();
        $member_count = $member_count > 0 ? (int)$member_count : 1;

        return (bcdiv($divisor, $member_count,2) * 100) . "%";
    }

}

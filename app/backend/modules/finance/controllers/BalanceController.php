<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/30
 * Time: 下午3:56
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\finance\BalanceRecharge;

class BalanceController extends BaseController
{
    //余额基础设置页面
    public function index()
    {
        $balance = Setting::get('balance.recharge');

        $requestModel = \YunShop::request()->balance;
        if ($requestModel) {
            //dd($requestModel);
            $requestModel[''] = '';
            if (Setting::set('balance.recharge', $requestModel)) {
                return $this->message('余额基础设置保存成功', Url::absoluteWeb('finance.balance.index'));
            } else {
                $this->error('余额基础设置保存失败！！');
            }
        }

        return view('finance.balance.index', [
            'balance' => $balance,
            'pager' => ''
        ])->render();
    }

    //用户余额管理
    public function member()
    {
        //dd(MemberGroup::getMemberGroupList());
        $pageSize = 5;
        $memberList = Member::getMembers()->paginate($pageSize);
        $pager = PaginationHelper::show($memberList->total(), $memberList->currentPage(), $memberList->perPage());

        //todo 搜索，会员组，会员等级显示

        return view('finance.balance.member', [
            'memberList'    => $memberList,
            'pager'         => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList()
        ])->render();
    }

    //后台会员充值
    public function recharge()
    {
        //todo 缺少会员头像路径转换

        $memberId = '55';
        $memberInfo = Member::getMemberInfoById($memberId);
        if (!$memberInfo) {
            $this->error('未获取到会员信息，请刷新重试');
        }

        if (\YunShop::request()->num && $memberInfo['id']) {
            $rechargeMode = new BalanceRecharge();
            $recordData = array(
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $memberId,
            );

        }

        //dd($memberInfo);

        return view('finance.balance.recharge', [
            'rechargeMenu'  => $this->getRechargeMenu(),
            'memberInfo'    => $memberInfo,
        ])->render();
    }

    //余额充值菜单
    private function getRechargeMenu()
    {
        $rechargeMenu = array(
            'title'     => '余额充值',
            'name'      => '粉丝',
            'profile'   => '会员信息',
            'old_value' => '当前余额',
            'charge_value' => '充值金额',
            'type'      => 'balance'
        );
        return $rechargeMenu;
    }


}

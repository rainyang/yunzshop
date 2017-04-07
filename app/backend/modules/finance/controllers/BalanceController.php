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
use app\common\models\finance\BalanceTransfer;
use app\common\services\fiance\Balance;

/*
 * 余额基础设置页面
 * 用户余额管理页面
 * 后台会员充值
 * 余额充值记录列表
 *
 * */
class BalanceController extends BaseController
{
    //余额基础设置页面[完成]
    public function index()
    {
        $balance = Setting::get('balance.recharge');

        $requestModel = \YunShop::request()->balance;
        if ($requestModel) {
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

    //余额明细记录[完成]
    public function balanceDetail()
    {
        $pageSize = 3;
        $detailList = \app\common\models\finance\Balance::getPageList($pageSize);
        $pager = PaginationHelper::show($detailList->total(), $detailList->currentPage(), $detailList->perPage());

        return view('finance.balance.detail', [
            'detailList' => $detailList,
            'pager' => $pager
        ])->render();
    }

    //用户余额管理 【完成】
    public function member()
    {
        $pageSize = 10;
        $search = \YunShop::request()->search;
        $memberList = Member::getMembers()->paginate($pageSize);
        if ($search) {
            $memberList = Member::searchMembers($search)->paginate($pageSize);
        }
        $pager = PaginationHelper::show($memberList->total(), $memberList->currentPage(), $memberList->perPage());

        return view('finance.balance.member', [
            'search'        => $search,
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
        $memberId = \YunShop::request()->member_id;
        $rechargeMoney = trim(\YunShop::request()->num);

        $memberInfo = Member::getMemberInfoById($memberId);
        if (!$memberInfo) {
            $this->error('未获取到会员信息，请刷新重试');
        }
//todo 需要获取商城登陆操作者ID  operator_id 字段
        $data = array(
            'member_id'     => $memberId,
            'change_money'  => $rechargeMoney,
            'operator'      => '0',
            'operator_id'   => '0', // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => '后台充值' . '余额' . $rechargeMoney .'元',
            'type'          => 1,
        );
        if ($rechargeMoney && $memberInfo['uid']) {
            $result = (new Balance())->rechargeBalance($data);
            if ($result === true ) {
                return $this->message('余额充值成功', Url::absoluteWeb('finance.balance.recharge',array('member_id' => $memberId)), 'success');
            } else {
                $this->error($result);
            }
        }

        return view('finance.balance.recharge', [
            'rechargeMenu'  => $this->getRechargeMenu(),
            'memberInfo'    => $memberInfo,
        ])->render();
    }

    //充值记录
    public function rechargeRecord()
    {
//todo 搜索功能
        $pageSize = 10;
        $recordList = BalanceRecharge::getPageList($pageSize);
        if ($search = \YunShop::request()->search) {
            $recordList = BalanceRecharge::getSearchPageList($pageSize,$search);
            dd($recordList);

        }
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        //支付类型：1后台支付，2 微信支付 3 支付宝， 4 其他支付
        return view('finance.balance.rechargeRecord', [
            'recordList'  => $recordList,
            'pager'    => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList()
        ])->render();
    }

    //会员余额转让记录
    public function transferRecord()
    {
//todo 搜索功能
        $pageSize = 10;
        $tansferList = BalanceTransfer::getTansferPageList($pageSize);
        $pager = PaginationHelper::show($tansferList->total(), $tansferList->currentPage(), $tansferList->perPage());

        return view('finance.balance.transferRecord', [
            'tansferList'  => $tansferList,
            'pager'    => $pager,
        ])->render();
    }

    //余额充值菜单
    private function getRechargeMenu()
    {
        return array(
            'title'     => '余额充值',
            'name'      => '粉丝',
            'profile'   => '会员信息',
            'old_value' => '当前余额',
            'charge_value' => '充值金额',
            'type'      => 'balance'
        );
    }

}

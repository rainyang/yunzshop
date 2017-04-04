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
        $pageSize = 5;
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
        //$memberId = '55';
        $memberInfo = Member::getMemberInfoById($memberId);
        if (!$memberInfo) {
            $this->error('未获取到会员信息，请刷新重试');
        } else {
            if (\YunShop::request()->num && $memberInfo['uid']) {
                if (!is_numeric(\YunShop::request()->num)) {
                    $this->error('请输入正确的充值金额');
                } else {
                    $rechargeMode = new BalanceRecharge();

                    //验证订单号是否可以用
                    $ordersn = createNo('RV', true);
                    while (1) {
                        if (!BalanceRecharge::validatorOrderSn($ordersn)) {
                            break;
                        }
                        $ordersn = createNo('RV', true);
                    }

                    $recordData = array(
                        'uniacid' => \YunShop::app()->uniacid,
                        'member_id' => $memberId,
                        'old_money' => $memberInfo['credit2'],
                        'money' => trim(\YunShop::request()->num),

//todo 增加金额值处理

                        'new_money' => $memberInfo['credit2'] + trim(\YunShop::request()->num),
                        'type' => 1,
                        'ordersn' => $ordersn,
                        'status' => 0
                    );


                    $rechargeMode->fill($recordData);
                    $validator = $rechargeMode->validator();
                    if ($validator->fails()) {
                        $this->error($validator->messages());
                    } else {
                        if ($rechargeMode->save()) {
                            (new Balance())->balanceChange($rechargeMode->member_id, $rechargeMode->money);
                            $rechargeMode->status = 1;
                            $rechargeMode->save();

//todo 请求修改余额接口，完成余额充值
                            return $this->message('余额充值成功', Url::absoluteWeb('finance.balance.recharge',array('member_id' => $memberId)), 'success');
                        }
                    }

                }
            }
        }



        //dd($memberInfo);

        return view('finance.balance.recharge', [
            'rechargeMenu'  => $this->getRechargeMenu(),
            'memberInfo'    => $memberInfo,
        ])->render();
    }

    //充值记录
    public function rechargeRecord()
    {
//todo 搜索功能
        $pageSize = 20;
        $recordList = BalanceRecharge::getRechargeRecord($pageSize);
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        return view('finance.balance.rechargeRecord', [
            'recordList'  => $recordList,
            'pager'    => $pager,
        ])->render();
    }

    //会员余额转让记录
    public function transferRecord()
    {
        $pageSize = 1;
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

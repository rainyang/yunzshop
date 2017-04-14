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
use app\common\models\Withdraw;
use app\common\services\finance\Balance;

/*
 * 余额基础设置页面
 * 用户余额管理页面
 * 后台会员充值
 * 余额充值记录列表
 *
 * */
class BalanceController extends BaseController
{
    /**
     * 余额基础设置页面[完成]
     *
     * @return mixed|string
     * @Author yitian */
    public function index()
    {
        //todo 数值验证
        $balance = Setting::get('finance.balance');
        $requestModel = \YunShop::request()->balance;
        if ($requestModel) {
            $requestModel['sale'] = $this->rechargeSale($requestModel);
            unset($requestModel['enough']);
            unset($requestModel['give']);
            if (Setting::set('finance.balance', $requestModel)) {
                return $this->message('余额基础设置保存成功', Url::absoluteWeb('finance.balance.index'));
            } else {
                $this->error('余额基础设置保存失败！！');
            }
        }
        return view('finance.balance.index', [
            'balance' => $balance,
        ])->render();
    }

    /**
     * 余额明细记录[完成]
     *
     * @return string
     * @Author yitian */
    public function balanceDetail()
    {
        //todo 搜索
        $pageSize = 20;
        $detailList = \app\common\models\finance\Balance::getPageList($pageSize);
        $pager = PaginationHelper::show($detailList->total(), $detailList->currentPage(), $detailList->perPage());

        return view('finance.balance.detail', [
            'detailList' => $detailList,
            'pager' => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList()
        ])->render();
    }

    /**
     * 查看余额明细详情
     *
     * @return string
     * @Author yitian */
    public function lookBalanceDetail()
    {
        $id = \YunShop::request()->id;
        $detailModel = \app\common\models\finance\Balance::getDetailById($id);

        return view('finance.balance.look-detail', [
            'detailModel' => $detailModel,
            'pager' => ''
        ])->render();
    }

    /**
     * 用户余额管理 【完成】
     *
     * @return string
     * @Author yitian */
    public function member()
    {
        $pageSize = 20;
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

    /**
     * 后台会员充值
     *
     * @return mixed|string
     * @Author yitian */
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
            'operator'      => BalanceRecharge::PAY_TYPE_SHOP,
            'operator_id'   => '0', // 来源ID，如：文章营销某一篇文章的ID，订单ID，海报ID
            'remark'        => '后台充值' . '余额' . $rechargeMoney .'元',
            'service_type'  => \app\common\models\finance\Balance::BALANCE_RECHARGE,
            'recharge_type' => BalanceRecharge::PAY_TYPE_SHOP
        );
        if ($rechargeMoney && $memberInfo['uid']) {
            $result = (new Balance())->changeBalance($data);
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

    /**
     * 余额提现详情
     *
     * @return string
     * @Author yitian */
    public function withdrawInfo()
    {
        $withdrawModel = Withdraw::getBalanceWithdrawById(\YunShop::request()->id)->toArray();

        return view('finance.balance.withdraw', [
            'item' => $withdrawModel,
            'set' => '',
        ])->render();
    }

    /**
     * 充值记录
     *
     * @return string
     * @Author yitian */
    public function rechargeRecord()
    {
        $pageSize = 10;
        $recordList = BalanceRecharge::getPageList($pageSize);
        if ($search = \YunShop::request()->search) {
            $recordList = BalanceRecharge::getSearchPageList($pageSize, $search);
            //dd($search);

        }
        $pager = PaginationHelper::show($recordList->total(), $recordList->currentPage(), $recordList->perPage());

        //支付类型：1后台支付，2 微信支付 3 支付宝， 4 其他支付
        return view('finance.balance.rechargeRecord', [
            'recordList'    => $recordList,
            'pager'         => $pager,
            'memberGroup'   => MemberGroup::getMemberGroupList(),
            'memberLevel'   => MemberLevel::getMemberLevelList(),
            'search'        => $search
        ])->render();
    }

    /**
     * 会员余额转让记录
     *
     * @return string
     * @Author yitian */
    public function transferRecord()
    {
        $pageSize = 20;
        $tansferList = BalanceTransfer::getTransferPageList($pageSize);
        if ($search = \YunShop::request()->search) {
            $tansferList = BalanceTransfer::getSearchPageList($pageSize, $search);
            //dd($tansferList);
        }

        $pager = PaginationHelper::show($tansferList->total(), $tansferList->currentPage(), $tansferList->perPage());

        return view('finance.balance.transferRecord', [
            'tansferList'  => $tansferList,
            'pager'    => $pager,
            'search' => $search
        ])->render();
    }

    /**
     * 余额充值菜单
     *
     * @return array
     * @Author yitian */
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

    /**
     * 处理充值赠送数据，满额赠送数据
     *
     * @param $data
     * @return array
     * @Author yitian */
    private function rechargeSale($data)
    {
        $result = array();
        $sale = is_array($data['enough']) ? $data['enough'] : array();
        foreach ($sale as $key => $value) {
            $enough = trim($value);
            if ($enough) {
                $result[] = array(
                    'enough' => trim($data['enough'][$key]),
                    'give' => trim($data['give'][$key])
                );

            }
        }
        return $result;
    }

}

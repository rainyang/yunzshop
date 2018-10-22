<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 16:48
 */

namespace app\backend\modules\charts\modules\income\controllers;


use app\common\components\BaseController;
use app\backend\modules\finance\models\Withdraw;
use app\common\helpers\PaginationHelper;
use app\backend\modules\charts\models\Income;

class MemberIncomeController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $search = \Yunshop::request();

        $list = Income::uniacid()
            ->search($search)
            ->selectRaw('sum(amount) as total_amount, sum(if(status=4,amount,0)) as withdraw, sum(if(status<4,amount,0)) as unwithdraw, member_id')
            ->selectRaw('sum(if(incometable_type like "%AreaDividend", amount, 0)) as area_dividend')
            ->selectRaw('sum(if(incometable_type like "%CommissionOrder", amount, 0)) as commission_dividend')
            ->selectRaw('sum(if(incometable_type like "%MerchantBonusLog", amount, 0)) as merchant_dividend')
            ->selectRaw('sum(if(incometable_type like "%AreaDividend", amount, 0)) as area_dividend')
            ->selectRaw('sum(if(incometable_type like "%AreaDividend", amount, 0)) as area_dividend')
            ->with([
                'hasOneWithdraw' => function($q) {
                    $q->selectRaw('sum(poundage) as totalPoundage, member_id')->groupBy('member_id');
                }
            ])
            ->groupBy('member_id')
            ->orderBy('totalAmount', 'desc')
            ->get();
//            ->paginate();
        dd($list->toArray());

        $page = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());
        return view('charts.income.member_income',[
            'list' => $list,
            'page' => $page,
        ])->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function detail()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;
        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }

        $member = Member::getMemberInfoById($uid);

        if (!empty($member)) {
            $member = $member->toArray();

            if (1 == $member['yz_member']['is_agent'] && 2 == $member['yz_member']['status']) {
                $member['agent'] = 1;
            } else {
                $member['agent'] = 0;
            }

            $myform = json_decode($member['yz_member']['member_form']);
        }


        //检测收入数据
        $incomeModel = Income::getIncomes()->where('member_id', $uid)->get();
        $config = \Config::get('income');
        unset($config['balance']);
        $incomeAll = [
            'title' => '推广收入',
            'type' => 'total',
            'type_name' => '推广佣金',
            'income' => $incomeModel->sum('amount'),
            'withdraw' => $incomeModel->where('status', 1)->sum('amount'),
            'no_withdraw' => $incomeModel->where('status', 0)->sum('amount')
        ];
        foreach ($config as $key => $item) {

            $typeModel = $incomeModel->where('incometable_type', $item['class']);
            $incomeData[$key] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['title'],
                'income' => $typeModel->sum('amount'),
                'withdraw' => $typeModel->where('status', 1)->sum('amount'),
                'no_withdraw' => $typeModel->where('status', 0)->sum('amount')
            ];
            if ($item['agent_class']) {
                $agentModel = $item['agent_class']::$item['agent_name'](\YunShop::app()->getMemberId());

                if ($item['agent_status']) {
                    $agentModel = $agentModel->where('status', 1);
                }

                //推广中心显示
                if (!$agentModel) {
                    $incomeData[$key]['can'] = false;
                } else {
                    $agent = $agentModel->first();
                    if ($agent) {
                        $incomeData[$key]['can'] = true;
                    } else {
                        $incomeData[$key]['can'] = false;
                    }
                }
            } else {
                $incomeData[$key]['can'] = true;
            }

        }

        return view('member.income', [
            'member' => $member,
            'levels' => $levels,
            'groups' => $groups,
            'incomeAll' => $incomeAll,
            'myform' => $myform,
//            'parent_name' => $parent_name,
            'item' => $incomeData
        ])->render();
    }
}
<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\Balance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class BalanceController extends ChartsController
{
    protected $time = array();
    protected $balanceLog;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allBalanceData = [];
        $balanceUseData = [];
        $balanceUsedData = [];
        $balanceWithdrawData = [];
        $balanceGivenData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime = strtotime($search['time']['start']);
        }
        $balanceTime = $this->getBalanceTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allBalanceData[$key] = Balance::uniacid()
                ->selectRaw('sum(if(service_type in (5,7),change_money,0)) as givenBalance, sum(change_money) as useBalance, sum(if(type=2,change_money,0))*-1 as usedBalance, sum(if(service_type=6,change_money,0))*-1 as withdrawBalance')
                ->where('created_at','<=', strtotime($time))
                ->first()
                ->toArray();
            $balanceGivenData[$key] = $allBalanceData[$key]['givenBalance'];
            $balanceUseData[$key] = $allBalanceData[$key]['useBalance'];
            $balanceWithdrawData[$key] = $allBalanceData[$key]['withdrawBalance'];
            $balanceUsedData[$key] = $allBalanceData[$key]['usedBalance'];
            $allBalanceData[$key]['date'] = $time;
        }
        krsort($allBalanceData);
        return view('charts.finance.balance',[
            'search' => $search,
            'balanceGivenCount' => $allBalanceData[6]['givenBalance'],
            'balanceUseCount' => $allBalanceData[6]['useBalance'],
            'balanceWithdrawCount' => $allBalanceData[6]['withdrawBalance'],
            'balanceUsedCount' => $allBalanceData[6]['usedBalance'],
            'balanceTime' => json_encode($balanceTime),
            'balanceUseData' => json_encode($balanceUseData),
            'balanceUsedData' => json_encode($balanceUsedData),
            'balanceWithdrawData' => json_encode($balanceWithdrawData),
            'balanceGivenData' => json_encode($balanceGivenData),
            'AllBalanceData' => $allBalanceData,
        ])->render();
    }

    public function getBalanceTime($searchTime = null)
    {
        $count = 6;
        if ($searchTime) {
            while($count >= 0)
            {
                $this->time[] = Carbon::createFromTimestamp($searchTime)->subDay($count)->startOfDay()->format('Y-m-d');
                $count--;
            }
        } else {
            $this->time = [
                Carbon::now()->subDay(6)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(5)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(4)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(3)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(2)->startOfDay()->format('Y-m-d'),
                Carbon::now()->subDay(1)->startOfDay()->format('Y-m-d'),
                Carbon::now()->startOfDay()->format('Y-m-d'),
            ];
        }
        return $this->time;
    }

}
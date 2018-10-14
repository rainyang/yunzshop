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
    /**
     * @return string
     */
    public function index()
    {
        $searchTime = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime['start'] = strtotime($search['time']['start']);
            $searchTime['end'] = strtotime($search['time']['end']);
        }
        $balanceLog = new Balance();
        $balanceUsedCount = $balanceLog->getUsedCount($searchTime);
        $balanceUseCount = $balanceLog->getUseCount($searchTime);
        $balanceWithdrawCount = $balanceLog->getWithdrawCount($searchTime);
        $balanceGivenCount = $balanceLog->getGivenCount($searchTime);
        return view('charts.finance.balance',[
            'search' => $search,
            'balanceGivenCount' => $balanceGivenCount,
            'balanceUseCount' => $balanceUseCount,
            'balanceWithdrawCount' => $balanceWithdrawCount * -1,
            'balanceUsedCount' => $balanceUsedCount * -1,
            'balanceTime' => json_encode($this->getBalanceTime($searchTime)),
            'balanceUseData' => json_encode($this->getBalanceUseData($searchTime)),
            'balanceWithdrawData' => json_encode($this->getBalanceWithdrawData($searchTime)),
            'balanceUsedData' => json_encode($this->getBalanceUsedData($searchTime)),
            'balanceGivenData' => json_encode($this->getBalanceGivenData($searchTime)),
            'AllBalanceData' => $this->getAllBalanceData($searchTime),
        ])->render();
    }

    public function getBalanceTime($searchTime)
    {
        if ($searchTime) {
            $day1 = Carbon::createFromTimestamp($searchTime['start']);
            $day2 = Carbon::createFromTimestamp($searchTime['end']);
            $count = $day1->diffInDays($day2, false);

            while($count >= 0)
            {
                $this->time[] = Carbon::createFromTimestamp($searchTime['end'])->subDay($count)->startOfDay()->format('Y-m-d');
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

    public function getBalanceUseData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $balanceData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(change_money) as balanceSum FROM ims_yz_balance where created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");
        foreach ($this->time as $key => $time) {
            foreach ($balanceData as $balance) {
                if ($time == $balance['date']) {
                    $data[$key] = $balance['balanceSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }

//        $data = [
//            $balanceData[0]['balanceSum'],
//            $balanceData[1]['balanceSum'],
//            $balanceData[2]['balanceSum'],
//            $balanceData[3]['balanceSum'],
//            $balanceData[4]['balanceSum'],
//            $balanceData[5]['balanceSum'],
//            $balanceData[6]['balanceSum'],
//        ];
        return $data;
    }
    public function getBalanceUsedData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $balanceData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(change_money)*-1 as balanceSum FROM ims_yz_balance where type=2 AND created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");

        foreach ($this->time as $key => $time) {
            foreach ($balanceData as $balance) {
                if ($time == $balance['date']) {
                    $data[$key] = $balance['balanceSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
//        $data = [
//            $balanceData[0]['balanceSum'],
//            $balanceData[1]['balanceSum'],
//            $balanceData[2]['balanceSum'],
//            $balanceData[3]['balanceSum'],
//            $balanceData[4]['balanceSum'],
//            $balanceData[5]['balanceSum'],
//            $balanceData[6]['balanceSum'],
//        ];
        return $data;
    }
    public function getBalanceWithdrawData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $balanceData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(change_money)*-1 as balanceSum FROM ims_yz_balance where service_type=2 and created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");
//        $data = [
//            $balanceData[0]['balanceSum'],
//            $balanceData[1]['balanceSum'],
//            $balanceData[2]['balanceSum'],
//            $balanceData[3]['balanceSum'],
//            $balanceData[4]['balanceSum'],
//            $balanceData[5]['balanceSum'],
//            $balanceData[6]['balanceSum'],
//        ];
        foreach ($this->time as $key => $time) {
            foreach ($balanceData as $balance) {
                if ($time == $balance['date']) {
                    $data[$key] = $balance['balanceSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
        return $data;
    }
    public function getBalanceGivenData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $balanceData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(change_money) as balanceSum FROM ims_yz_balance where service_type IN (5,7) and created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");
//        $data = [
//            $balanceData[0]['balanceSum'],
//            $balanceData[1]['balanceSum'],
//            $balanceData[2]['balanceSum'],
//            $balanceData[3]['balanceSum'],
//            $balanceData[4]['balanceSum'],
//            $balanceData[5]['balanceSum'],
//            $balanceData[6]['balanceSum'],
//        ];
        foreach ($this->time as $key => $time) {
            foreach ($balanceData as $balance) {
                if ($time == $balance['date']) {
                    $data[$key] = $balance['balanceSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
        return $data;
    }

    public function getAllBalanceData($searchTime = null)
    {
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->subDay(6)->startOfDay()->timestamp;
        }
        $balanceData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(change_money) as balanceUes, sum(if(type=-1, change_money,0))*-1 as balanceUsed, sum(if(service_type=2, change_money,0))*1 as balanceWithdraw, sum(if(service_type IN (5,7), change_money,0)) as balanceGiven FROM ims_yz_balance where created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");

        return $balanceData;
    }

}
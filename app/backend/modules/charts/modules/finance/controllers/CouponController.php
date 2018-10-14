<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\CouponLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class CouponController extends ChartsController
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
            $couponData = DB::select("select count(id) as couponGiven, sum(if(used=1,used,0)) as couponUsed from ims_yz_member_coupon where get_time>=". $searchTime['start']. " and get_time<=" . $searchTime['end']);
        } else {
            $couponData = DB::select("select count(id) as couponGiven, sum(if(used=1,used,0)) as couponUsed from ims_yz_member_coupon");
        }
        $couponGivenCount = $couponData[0]['couponGiven'];
        $couponUsedCount = $couponData[0]['couponUsed'];
        return view('charts.finance.coupon',[
            'search' => $search,
            'couponGivenCount' => $couponGivenCount,
            'couponUsedCount' => $couponUsedCount,
            'couponTime' => json_encode($this->getCouponTime($searchTime)),
            'couponUsedData' => json_encode($this->getCouponUsedData($searchTime)),
            'couponGivenData' => json_encode($this->getCouponGivenData($searchTime)),
            'AllCouponData' => $this->getAllCouponData($searchTime),
        ])->render();
    }

    public function getCouponTime($searchTime)
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

    public function getCouponUsedData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $couponData = DB::select("select FROM_UNIXTIME(get_time,'%Y-%m-%d')as date,sum(used) as couponSum FROM ims_yz_member_coupon where used=1 AND get_time>=". $searchTime['start']. " and get_time<=" . $searchTime['end'] . " GROUP BY date;");

        foreach ($this->time as $key => $time) {
            foreach ($couponData as $coupon) {
                if ($time == $coupon['date']) {
                    $data[$key] = $coupon['couponSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
        return $data;
    }

    public function getCouponGivenData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $couponData = DB::select("select FROM_UNIXTIME(get_time,'%Y-%m-%d')as date,count(id) as couponSum FROM ims_yz_member_coupon where get_time>=". $searchTime['start']. " and get_time<=" . $searchTime['end'] . " GROUP BY date;");

        foreach ($this->time as $key => $time) {
            foreach ($couponData as $coupon) {
                if ($time == $coupon['date']) {
                    $data[$key] = $coupon['couponSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
        return $data;
    }

    public function getAllCouponData($searchTime = null)
    {
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->subDay(6)->startOfDay()->timestamp;
        }
        $couponData = DB::select("select FROM_UNIXTIME(get_time,'%Y-%m-%d')as date,count(id) as couponGiven, sum(if(used=1,used,0)) as couponUsed FROM ims_yz_member_coupon where get_time>=". $searchTime['start']. " and get_time<=" . $searchTime['end'] . " GROUP BY date;");

        return $couponData;
    }

}
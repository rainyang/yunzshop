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
    protected $couponLog;


    public function __construct()
    {
        $this->couponLog = new CouponLog();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allCouponData = [];
        $couponUsedData = [];
        $couponGivenData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime = strtotime($search['time']['start']);
        }
        $couponTime = $this->getCouponTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allCouponData[$key] = CouponLog::uniacid()
                ->selectRaw('count(id) as givenCoupon, sum(used) as usedCoupon')
                ->where('get_time','<=', strtotime($time))
                ->first()
                ->toArray() ;
            $couponGivenData[$key] = $allCouponData[$key]['givenCoupon'];
            $couponUsedData[$key] = $allCouponData[$key]['usedCoupon'];
            $allCouponData[$key]['date'] = $time;
        }
        return view('charts.finance.coupon',[
            'search' => $search,
            'couponGivenCount' => $allCouponData[6]['givenCoupon'],
            'couponUsedCount' => $allCouponData[6]['usedCoupon'],
            'couponTime' => json_encode($couponTime),
            'couponUsedData' => json_encode($couponUsedData),
            'couponGivenData' => json_encode($couponGivenData),
            'AllCouponData' => $allCouponData,
        ])->render();
    }

    public function getCouponTime($searchTime = null)
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
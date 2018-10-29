<?php

namespace app\backend\modules\charts\modules\finance\controllers;

use app\backend\modules\charts\controllers\ChartsController;
use app\backend\modules\charts\models\PointLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/10/13
 * Time: 15:51
 */
class PointController extends ChartsController
{
    protected $time = array();
    protected $pointLog;

    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $searchTime = [];
        $allPointData = [];
        $pointUseData = [];
        $pointUsedData = [];
        $pointGivenData = [];
        $search = \YunShop::request()->search;
        if ($search['is_time'] && $search['time']) {
            $searchTime = strtotime($search['time']['start']);
        }
        $pointTime = $this->getPointTime($searchTime);

        foreach ($this->time as $key => $time) {
            $allPointData[$key] = PointLog::uniacid()->selectRaw('sum(if(point_income_type=1,point,0)) as givenPoint, sum(point) as usePoint, sum(if(point_income_type=-1,point,0))*-1 as usedPoint')->where('created_at','<=', strtotime($time))->first()->toArray() ;
            $pointGivenData[$key] = $allPointData[$key]['givenPoint'];
            $pointUseData[$key] = $allPointData[$key]['usePoint'];
            $pointUsedData[$key] = $allPointData[$key]['usedPoint'];
            $allPointData[$key]['date'] = $time;
        }
        arsort($allPointData);
        return view('charts.finance.point',[
            'search' => $search,
            'pointGivenCount' => $allPointData[6]['givenPoint'],
            'pointUseCount' => $allPointData[6]['usePoint'],
            'pointUsedCount' => $allPointData[6]['usedPoint'],
            'pointTime' => json_encode($pointTime),
            'pointUseData' => json_encode($pointUseData),
            'pointUsedData' => json_encode($pointUsedData),
            'pointGivenData' => json_encode($pointGivenData),
            'AllPointData' => $allPointData,
        ])->render();
    }

    public function getPointTime($searchTime = null)
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
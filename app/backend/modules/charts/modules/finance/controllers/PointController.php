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
        $pointLog = new PointLog();
        $pointUsedCount = $pointLog->getUsedCount($searchTime);
        $pointUseCount = $pointLog->getUseCount($searchTime);
        $pointGivenCount = $pointLog->getGivenCount($searchTime);
        return view('charts.finance.point',[
            'search' => $search,
            'pointGivenCount' => $pointGivenCount,
            'pointUseCount' => $pointUseCount,
            'pointUsedCount' => $pointUsedCount * -1,
            'pointTime' => json_encode($this->getPointTime($searchTime)),
            'pointUseData' => json_encode($this->getPointUseData($searchTime)),
            'pointUsedData' => json_encode($this->getPointUsedData($searchTime)),
            'pointGivenData' => json_encode($this->getPointGivenData($searchTime)),
            'AllPointData' => $this->getAllPointData($searchTime),
        ])->render();
    }

    public function getPointTime($searchTime)
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

    public function getPointUseData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $pointData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(point) as pointSum FROM ims_yz_point_log where created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");
        foreach ($this->time as $key => $time) {
            foreach ($pointData as $point) {
                if ($time == $point['date']) {
                    $data[$key] = $point['pointSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }

//        $data = [
//            $pointData[0]['pointSum'],
//            $pointData[1]['pointSum'],
//            $pointData[2]['pointSum'],
//            $pointData[3]['pointSum'],
//            $pointData[4]['pointSum'],
//            $pointData[5]['pointSum'],
//            $pointData[6]['pointSum'],
//        ];
        return $data;
    }
    public function getPointUsedData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $pointData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(point) as pointSum FROM ims_yz_point_log where point_income_type=-1 AND created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");

        foreach ($this->time as $key => $time) {
            foreach ($pointData as $point) {
                if ($time == $point['date']) {
                    $data[$key] = $point['pointSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
//        $data = [
//            $pointData[0]['pointSum'],
//            $pointData[1]['pointSum'],
//            $pointData[2]['pointSum'],
//            $pointData[3]['pointSum'],
//            $pointData[4]['pointSum'],
//            $pointData[5]['pointSum'],
//            $pointData[6]['pointSum'],
//        ];
        return $data;
    }
    public function getPointGivenData($searchTime = null)
    {
        $data = [];
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->startOfDay()->timestamp;
        }
        $pointData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(point) as pointSum FROM ims_yz_point_log where point_income_type=-1 and created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");
//        $data = [
//            $pointData[0]['pointSum'],
//            $pointData[1]['pointSum'],
//            $pointData[2]['pointSum'],
//            $pointData[3]['pointSum'],
//            $pointData[4]['pointSum'],
//            $pointData[5]['pointSum'],
//            $pointData[6]['pointSum'],
//        ];
        foreach ($this->time as $key => $time) {
            foreach ($pointData as $point) {
                if ($time == $point['date']) {
                    $data[$key] = $point['pointSum'];
                }
            }
            $data[$key] = $data[$key] ?: 0;
        }
        return $data;
    }

    public function getAllPointData($searchTime = null)
    {
        if (!$searchTime) {
            $searchTime['start'] = Carbon::now()->subDay(6)->startOfDay()->timestamp;
            $searchTime['end'] = Carbon::tomorrow()->subDay(6)->startOfDay()->timestamp;
        }
        $pointData = DB::select("select FROM_UNIXTIME(created_at,'%Y-%m-%d')as date,sum(point) as pointUes, sum(if(point_income_type=-1, point,0)) as pointUsed, sum(if(point_income_type=1, point,0)) as pointGiven FROM ims_yz_point_log where created_at>=". $searchTime['start']. " and created_at<=" . $searchTime['end'] . " GROUP BY date;");

        return $pointData;
    }

}
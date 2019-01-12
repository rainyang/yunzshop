<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/11
 * Time: 20:09
 */
namespace app\backend\modules\charts\modules\team\services;
use app\backend\modules\charts\modules\team\models\MemberMonthOrder;
use app\common\models\Member;
use app\common\models\Order;

class TeamService
{
    public function OrderStatistics(){
        $members = Member::uniacid()->get(['uid']);
        $time = time();
        $year = date('Y',$time);
        $month = date('n',$time)-1;
        if($month == 0){
            $year -= 1;
            $month = 12;
        }
        $start = strtotime(date('Y-m-01 00:00:00',strtotime('-1 month')));
        $end = strtotime(date("Y-m-d 23:59:59", strtotime(-date('d').'day')));
        $range = [$start,$end];
        foreach ($members as $v){
              $main = Order::uniacid()->where(['uid'=>$v['uid'],'status'=>Order::COMPLETE])->whereBetween('created_at',$range);
              $num = $main->count();
              $price =$main->sum('price');
              $data=[];
              $data['member_id'] = $v['uid'];
              $data['year'] = $year;
              $data['month'] = $month;
              $data['order_num'] = $num;
              $data['order_price'] = $price;
              MemberMonthOrder::create($data);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 17:59
 */

namespace app\backend\modules\charts\modules\team\services;



use app\common\models\member\MemberChildren;
use app\common\models\member\MemberMonthOrder;
use app\common\models\member\MemberMonthRank;

class TeamRankService
{
    public function getRank()
    {
        $time=time();
        $nowyear = date('Y',$time);
        $nowmonth = date('n',$time);
        if($nowmonth == 1){
            $nowyear = $nowyear -1;
            $nowmonth =12 ;
        }
        $allMember = MemberChildren::uniacid()->where('level',1)->orWhere('level',2)->get();
        $allMember = $allMember->groupBy('member_id')->map(function($item) use($nowyear,$nowmonth){
            $count = 0;
            foreach ($item as $v){
                $finder = MemberMonthOrder::where(['year'=>$nowyear,'month'=>$nowmonth,'member_id'=>$v['child_id']])->first();
                if($finder){
                    $count = bcadd($count,$finder->order_price,2);
                }
            }
            return $count;
        });
        $data = [];
        foreach ($allMember as $k=>$v){
            $data[] = ['member_id'=>$k,'price'=>$v,'year'=>$nowyear,'month'=>$nowmonth];
        }
        $filtered = collect($data)->filter(function ($value, $key) {
            return $value['price'] != 0;
        });
        $data = $filtered->all();
        $data = collect($data)->sortByDesc('price')->values()->map(function($item,$key){
            $item['rank'] = $key + 1;
            return $item;
        });
        list($keys,$values) = array_divide($data->toArray());
        MemberMonthRank::insert($values);
    }

}
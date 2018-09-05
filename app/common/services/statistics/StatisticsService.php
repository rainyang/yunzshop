<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/7/30
 * Time: 10:25
 */

namespace app\common\services\statistics;

use Illuminate\Support\Facades\DB;
use app\common\models\statistic\MemberRelationStatisticsModel;
use app\common\models\statistic\MemberRelationOrderStatisticsModel;

class StatisticsService
{
    private $team_total;//整个团队总人数
    private $second_total;//二级下线总人数
    private $third_total;//三级下线总人数
    private $first_order_quantity;//一级下线订单总数
    private $first_order_amount;//一级下线订单总额
    private $second_order_quantity;
    private $second_order_amount;
    private $third_order_quantity;
    private $third_order_amount;
    private $team_order_quantity;
    private $team_order_amount;
    private $member_ids;
    private $member_orders;

    /**
     * @return bool
     */
    public function statistics() {
        $member_relation_model = new MemberRelationStatisticsModel();
        $member_orders_model = new MemberRelationOrderStatisticsModel();

        //抛开model，对象，直接查询
        $member_ids = DB::select('select member_id,parent_id,uniacid from ims_yz_member');
        $mc_member = DB::select('select uid,uniacid from ims_mc_member');
        $member_orders = DB::select('select * from ims_yz_order_count');

        //用集合查询，减少开关数据库次数
        $this->member_ids = collect($member_ids);
        $mc_member = collect($mc_member);
        $this->member_orders = collect($member_orders);

        foreach ($member_ids as $member_id) {
            //每个会员初始化数据
            $this->team_total = 1;
            $this->second_total = 0;
            $this->third_total = 0;
            $this->first_order_quantity = 0;
            $this->first_order_amount = 0;
            $this->second_order_quantity = 0;
            $this->second_order_amount = 0;
            $this->third_order_quantity = 0;
            $this->third_order_amount = 0;
            $this->team_order_quantity = 0;
            $this->team_order_amount = 0;

            //前三级计算
            if ($mc_member->where('uid',$member_id['member_id'])->first()) {
                $data = $this->threeCount($member_id['member_id']);
            }
            unset($member_id['parent_id']);
            $count_total = array_merge($member_id,$data['member_relation']);
            $count_order_total = array_merge($member_id,$data['member_order']);

            //判断数据库是否有数据，分别进行更新或插入
            $member_relation_model->updateOrCreate(['member_id' => $member_id['member_id']],$count_total);
            $member_orders_model->updateOrCreate(['member_id' => $member_id['member_id']],$count_order_total);

        }
        return true;

    }

    /**
     * 前三级下线计算
     * @param $member_id
     * @param int $level
     * @return array
     */
    public function threeCount($member_id, $level = 1) {
        $member_ids = $this->member_ids->where('parent_id',$member_id);

        $this->team_total += count($member_ids);//计算团队总数
        $this->team_order_quantity += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_quantity');
        $this->team_order_amount += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_amount');

        //一级下线
        $first_total = count($member_ids);

        //二级下线
        if ($level == 2) {
            $this->second_total += count($member_ids);//计算二级总数
            $this->first_order_quantity += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_quantity');
            $this->first_order_amount += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_amount');
        }

        //三级下线
        if ($level == 3) {
            $this->third_total += count($member_ids);//计算三级总数
            $this->second_order_quantity += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_quantity');
            $this->second_order_amount += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_amount');

            //三级后执行独立递归
            foreach ($member_ids as $member_id) {
                $this->third_order_quantity += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_quantity');
                $this->third_order_amount += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_amount');
                $this->count($member_id['member_id']);
            }
        } else {
            //三级前执行当前递归
            $level++;
            foreach ($member_ids as $member_id) {
                $this->threeCount($member_id['member_id'],$level);
            }
        }

        return [
            'member_relation' => [
                'first_total' => $first_total,
                'second_total' => $this->second_total,
                'third_total' => $this->third_total,
                'team_total' => $this->team_total,
            ],
            'member_order' => [
                'first_order_quantity' => $this->first_order_quantity,
                'first_order_amount' => $this->first_order_amount,
                'second_order_quantity' => $this->second_order_quantity,
                'second_order_amount' => $this->second_order_amount,
                'third_order_amount' => $this->third_order_amount,
                'third_order_quantity' => $this->third_order_quantity,
                'team_order_quantity' => $this->team_order_quantity,
                'team_order_amount' => $this->team_order_amount,
            ]
        ];
    }

    /**
     * 三级后的下线计算
     * @param $member_id
     */
    public function count($member_id) {
        $member_ids = $this->member_ids->where('parent_id',$member_id);

        $this->team_total += count($member_ids);
        $this->team_order_quantity += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_quantity');
        $this->team_order_amount += $this->member_orders->where('parent_id',$member_id)->sum('total_complete_amount');

        foreach ($member_ids as $member_id) {
            $this->count($member_id['member_id']);
        }

    }

}
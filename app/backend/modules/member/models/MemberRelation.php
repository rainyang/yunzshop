<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 上午9:32
 */

namespace app\backend\modules\member\models;

use app\backend\models\BackendModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\SubMemberModel;
use app\frontend\modules\order\models\OrderListModel;

class MemberRelation extends BackendModel
{
    public $table = 'yz_member_relation';

    public $timestamps = false;

    /**
     * 可以批量赋值的属性
     *
     * @var array
     */
    public $fillable = ['uniacid', 'status', 'become', 'become_order', 'become_child', 'become_ordercount',
        'become_moneycount', 'become_goods_id', 'become_info', 'become_check'];

    /**
     * 不可批量赋值的属性
     *
     * @var array
     */
    public $guarded = [];

    /**
     * 获取会员关系链数据
     *
     * @return mixed
     */
    public static function getSetInfo()
    {
        return self::uniacid();
    }

    /**
     * 用户是否达到发展下线条件
     *
     * @return bool
     */
    public static function checkAgent($uid)
    {
        $info = self::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo($uid);

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        if ($data['is_agent'] == 0) {
            switch ($info['become']) {
                case 0:
                    $isAgent = true;
                    break;
                case 2:
                    $cost_num = OrderListModel::getCostTotalNum($uid);

                    if ($cost_num >= $info['become_ordercount']) {
                        $isAgent = true;
                    }
                    break;
                case 3:
                    $cost_price = OrderListModel::getCostTotalPrice($uid);

                    if ($cost_price >= $info['become_moneycount']) {
                        $isAgent = true;
                    }
                    break;
                case 4:
                    $isAgent = self::checkOrderGoods($info['become_goods_id']);
                    break;
                default:
                    $isAgent = false;
            }
        }

        if ($isAgent) {
            if ($info['become_check'] == 0) {
                $member_info->is_agent = 1;
                $member_info->status = 2;
                $member_info->save();
            }
        }
    }

    /**
     * 设置用户关系链
     *
     * @return void
     */
    public function setAgent()
    {
        $info = self::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId())->first();

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        $isAgent = false;
        if ($info['status'] == 1 && $data['is_agent'] == 0) {
            $mid = \YunShop::request()->mid ? \YunShop::request()->mid : 0;
            if ($mid != 0 && $data['member_id'] != $mid) {
                $member_info->parent_id = $mid;
                $member_info->save();
            }
        }
    }

    /**
     * 检查用户订单中是否包含指定商品
     *
     * @param $goods_id
     * @return bool
     */
    public static function checkOrderGoods($goods_id)
    {
        $list = OrderListModel::getRequestOrderList(3,\YunShop::app()->getMemberId());

        if (!empty($list)) {
            foreach ($list as $rows) {
                foreach ($rows['has_many_order_goods'] as $item) {
                    if ($item['goods_id'] == $goods_id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }


//    public function createChildAgent($mid, MemberShopInfo $model)
//    {
//        $child_info = $this->getChildAgentInfo();
//            switch ($child_info) {
//                case 0:
//                    $this->becomeChildAgent($mid, $model);
//                    break;
//                case 1:
//                    $list = OrderListModel::getRequestOrderList(0,\YunShop::app()->getMemberId())->get();
//
//                    if (!empty($list)) {
//                        $result = $list->toArray();
//                        $count = count($result);
//
//                        if ($count == 1) {
//                            $this->becomeChildAgent($mid, $model);
//                        }
//                    }
//                    break;
//                case 2:
//                    $list = OrderListModel::getRequestOrderList(1,\YunShop::app()->getMemberId())->get();
//
//                    if (!empty($list)) {
//                        $result = $list->toArray();
//
//                        $count = count($result);
//
//                        if ($count == 1) {
//                            $this->becomeChildAgent($mid, $model);
//                        }
//                    }
//                    break;
//            }
//
//        return 0;
//
//    }

    /**
     * 获取成为下线条件
     *
     * @return int
     */
    public function getChildAgentInfo()
    {
        $info = self::getSetInfo()->first();

        if (!empty($info)) {
            $data = $info->toArray();

            return $data['become_child'];
        }
    }

    /**
     * 成为下线
     *
     * @param $mid
     * @param MemberShopInfo $model
     */
    private function changeChildAgent($mid, MemberShopInfo $model)
    {
        $member_info = SubMemberModel::getMemberShopInfo($mid);

        if ($member_info && $member_info->is_agent) {
            $model->parent_id = $mid;
            $model->child_time = time();

            if ($model->save()) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * 检查是否能成为下线
     *
     * 首次点击分享连接 / 无条件发展下线权利
     *
     * 触发 注册
     *
     * @param $mid
     * @param MemberShopInfo $user
     */
    public function becomeChildAgent($mid, MemberShopInfo $model)
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = SubMemberModel::getMemberShopInfo($model->member_id);

        if (empty($member)) {
            return;
        }

        $parent = false;

        if (!empty($mid)) {
            $parent =  SubMemberModel::getMemberShopInfo($mid);
        }

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        if ($member->is_agent == 1) {
            return;
        }

        $become_child =  intval($set->become_child);
        $become_check = intval($set->become_check);

        if ($parent_is_agent && empty($member->parent_id)) {
            if ($member->member_id != $parent->member_id) {
                if (empty($become_child)) {
                    $this->changeChildAgent($mid, $model);

                    // TODO message notice
                } else {
                    $model->inviter = $parent->member_id;

                    $model->save();
                }
            }
        }

        if (empty($set->become) ) {
            $member->is_agent = 1;

            if ($become_check == 0) {
                $member->status = 2;
                $member->agent_time = time();

                // TODO message notice
            } else {
                $member->status = 1;
            }

            $member->save();
        }
    }

    /**
     * 成为下线条件 首次下单
     *
     * 触发 确认订单
     *
     * @return void
     */
    public static function checkOrderConfirm()
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }


        $member = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($member)) {
            return;
        }

        $become_child = intval($set->become_child);

        if (empty($become_child)) {
            $parent = SubMemberModel::getMemberShopInfo($member->parent_id);
        } else {
            $parent = SubMemberModel::getMemberShopInfo($member->inviter);
        }

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        if ($parent_is_agent) {
            if ($become_child == 1) {
                if (empty($member->parent_id) && $member->member_id != $parent->member_id) {
                    $member->parent_id = $parent->member_id;
                    $member->child_time = time();

                    $member->save();

                    // TODO message notice
                }
            }
        }
    }

    /**
     * 发展下线资格 付款后
     *
     * 成为下线条件 首次付款
     *
     * 触发 支付回调
     *
     * @return void
     */
    public static function checkOrderPay()
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());
        if (empty($member)) {
            return;
        }

        $become_child = intval($set->become_child);

        if (empty($become_child)) {
            $parent = SubMemberModel::getMemberShopInfo($member->parent_id);
        } else {
            $parent = SubMemberModel::getMemberShopInfo($member->inviter);
        }

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        //成为下线
        if ($parent_is_agent) {
            if ($become_child == 2) {
                if (empty($member->parent_id) && $member->member_id != $parent->member_id) {
                    $member->parent_id = $parent->member_id;
                    $member->child_time = time();

                    $member->save();

                    // TODO message notice
                }
            }
        }
        $isagent = $member->is_agent == 1 && $member->status == 2;

        if (!$isagent) {
            if (intval($set->become) == 4 && !empty($set->become_goods_id)) {
                $result = self::checkOrderGoods($set['become_goods_id']);

                if ($result) {
                    $member->status = 2;
                    $member->is_agent = 1;
                    $member->agent_time = time();

                    // TODO message notice
                }
            }
        }

        //发展下线资格
        if (!$isagent && empty($set->become_order)) {
            if ($set->become == 2 || $set->become == 3) {
                $parentisagent = true;

                if (!empty($member->parent_id)) {
                    $parent = SubMemberModel::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    if ($set->become == '2') {
                        $ordercount = OrderListModel::getCostTotalNum($member->member_id);

                        $can = $ordercount >= intval($set->become_ordercount);
                    } else if ($set->become == '3') {
                        $moneycount = OrderListModel::getCostTotalPrice($member->member_id);

                        $can = $moneycount >= floatval($set->become_moneycount);
                    }

                    if ($can) {
                        $become_check = intval($set->become_check);

                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            // TODO message notice
                        } else {
                            $member->status = 1;
                        }

                        $member->save();
                    }
                }
            }
        }
    }

    /**
     * 发现下线资格 完成后
     *
     * 触发 订单完成
     *
     * @return void
     */
    public static function checkOrderFinish()
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = SubMemberModel::getMemberShopInfo(\YunShop::app()->getMemberId());

        if (empty($member)) {
            return;
        }

        $isagent = $member->is_agent == 1 && $member->status == 2;

        if (!$isagent && $set->become_order == 1) {
            if ($set->become == 2 || $set->become == 3) {
                $parentisagent = true;

                if (!empty($member->parent_id)) {
                    $parent = SubMemberModel::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    if ($set->become == '2') {
                        $ordercount = OrderListModel::getCostTotalNum($member->member_id);

                        $can = $ordercount >= intval($set->become_ordercount);
                    } else if ($set->become == '3') {
                        $moneycount = OrderListModel::getCostTotalPrice($member->member_id);

                        $can = $moneycount >= floatval($set->become_moneycount);
                    }

                    if ($can) {
                        $become_check = intval($set->become_check);

                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            // TODO message notice
                        } else {
                            $member->status = 1;
                        }

                        $member->save();
                    }
                }
            }
        }
    }
}
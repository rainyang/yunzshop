<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/8
 * Time: 上午9:32
 */

namespace app\backend\modules\member\models;

use app\backend\models\BackendModel;
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

        $member_info = SubMemberModel::getMemberShopInfo($uid)->first();

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

    /**
     * 检查是否能成为下线
     *
     * @param $mid
     * @param MemberShopInfo $user
     */
    public function createChildAgent($mid, MemberShopInfo $model)
    {
        $child_info = $this->getChildAgentInfo();
            switch ($child_info) {
                case 0:
                    $this->becomeChildAgent($mid, $model);
                    break;
                case 1:
                    $list = OrderListModel::getRequestOrderList(0,\YunShop::app()->getMemberId())->get();

                    if (!empty($list)) {
                        $result = $list->toArray();
                        $count = count($result);

                        if ($count == 1) {
                            $this->becomeChildAgent($mid, $model);
                        }
                    }
                    break;
                case 2:
                    $list = OrderListModel::getRequestOrderList(1,\YunShop::app()->getMemberId())->get();

                    if (!empty($list)) {
                        $result = $list->toArray();

                        $count = count($result);

                        if ($count == 1) {
                            $this->becomeChildAgent($mid, $model);
                        }
                    }
                    break;
            }

    }

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
    private function becomeChildAgent($mid, MemberShopInfo $model)
    {
        $info = self::getSetInfo()->first()->toArray();

        $member_info = SubMemberModel::getMemberShopInfo($mid);

        if ($member_info && $member_info->is_agent) {
            $model->parent_id = $mid;
            $model->save();
        }
    }


}
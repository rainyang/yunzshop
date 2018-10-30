<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/23 下午2:16
 * Email: livsyitian@163.com
 */

namespace app\common\models;


use app\common\events\member\MemberCreateRelationEvent;
use app\common\events\member\MemberFirstChilderenEvent;
use app\common\events\member\MemberRelationEvent;
use app\common\models\notice\MessageTemp;
use app\common\services\MessageService;

class MemberRelation extends BaseModel
{
    static protected $needLog = true;

    public $table = 'yz_member_relation';

    public $timestamps = false;

    /**
     * 可以批量赋值的属性
     *
     * @var array
     */
    public $fillable = ['uniacid', 'status', 'become', 'become_order', 'become_child', 'become_ordercount',
        'become_moneycount', 'become_goods_id', 'become_info', 'become_check', 'become_slefmoney'];

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
        $info = self::getSetInfo()->first();

        if (empty($info)) {
            return [];
        }

        $member_info = MemberShopInfo::getMemberShopInfo($uid);

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        if ($data['is_agent'] == 0) {
            switch ($info['become']) {
                case 0:
                    $isAgent = true;
                    break;
                case 2:
                    $cost_num = Order::getCostTotalNum($uid);

                    if ($cost_num >= $info['become_ordercount']) {
                        $isAgent = true;
                    }
                    break;
                case 3:
                    $cost_price = Order::getCostTotalPrice($uid);

                    if ($cost_price >= $info['become_moneycount']) {
                        $isAgent = true;
                    }
                    break;
                case 4:
                    $isAgent = self::checkOrderGoods($info['become_goods_id'], $uid);
                    break;
                case 5:
                    $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
                    if ($sales_money >= $info['become_selfmoney']) {
                        $isAgent = true;
                    }
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

        $member_info = MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId())->first();

        if (!empty($member_info)) {
            $data = $member_info->toArray();
        }

        $isAgent = false;
        if ($info['status'] == 1 && $data['is_agent'] == 0) {
            $mid = \app\common\models\Member::getMid();
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
    public static function checkOrderGoods($goods_id, $uid)
    {
        $list = Order::getOrderListByUid($uid);

        if (!empty($list)) {
            $list = $list->toArray();

            foreach ($list as $rows) {
                foreach ($rows['has_many_order_goods'] as $item) {
                    if ($item['goods_id'] == $goods_id) {
                        \Log::debug('购买商品指定商品', [$goods_id]);
                        return true;
                    }
                }
            }
        }

        return false;
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

            return $info->become_child;
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
        \Log::debug(sprintf('成为下线mid-%d', $mid));
        $member_info = MemberShopInfo::getMemberShopInfo($mid);

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
     * 触发 入口
     *
     * @param $mid
     * @param MemberShopInfo $user
     */
    public function becomeChildAgent($mid, \app\common\models\MemberShopInfo $model)
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($model->member_id);

        if (empty($member)) {
            return;
        }

        if ($member->is_agent == 1) {
            return;
        }

        $parent = null;

        $become_child =  intval($set->become_child);
        $become_check = intval($set->become_check);

        if (!empty($mid)) {
            $parent =  MemberShopInfo::getMemberShopInfo($mid);
        } else {
            if ($member->inviter == 0 && $member->parent_id == 0) {
                if (empty($become_child)) {
                    $model->child_time = time();
                    $model->inviter = 1;
                    \Log::debug(sprintf('会员id-%d确定上线id-%d', $model->member_id, $mid));
                    $model->save();
                }
            }
        }

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        if ($parent_is_agent && empty($member->inviter)) {
            if ($member->member_id != $parent->member_id) {
                $this->changeChildAgent($mid, $model);

                if (empty($become_child)) {
                    $model->inviter = 1;
                    \Log::debug(sprintf('会员id-%d确定上线id-%d', $model->member_id, $mid));
                    //notice
                    self::sendAgentNotify($member->member_id, $mid);
                } else {
                    \Log::debug(sprintf('会员id-%d未确定上线id-%d', $model->member_id, $mid));
                    $model->inviter = 0;
                }

                $model->save();

                event(new MemberCreateRelationEvent($model->member_id, $mid));
            }
        }

        if (empty($set->become) ) {
            $model->is_agent = 1;

            if ($become_check == 0) {
                $model->status = 2;
                $model->agent_time = time();

                if ($model->inviter == 0) {
                    \Log::debug(sprintf('会员id-%d无条件会员上线id-%d', $model->member_id, $mid));
                    $model->inviter = 1;
                    $model->parent_id = 0;
                }
            } else {
                $model->status = 1;
            }

            if ($model->save()) {
                self::setRelationInfo($model);
            }
        }
    }

    /**
     * 成为下线条件 首次下单
     *
     * 触发 确认订单
     *
     * @return void
     */
    public static function checkOrderConfirm($uid)
    {
        $set = self::getSetInfo()->first();

        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($member)) {
            return;
        }

        $become_child = intval($set->become_child);

        if ($member->parent_id == 0) {
            \Log::debug(sprintf('会员上线ID进入时1-: %d', $member->parent_id));
            if ($become_child == 1 && empty($member->inviter)) {
                $member->child_time = time();
                $member->inviter = 1;

                $member->save();
            }
        } else {
            $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
            \Log::debug(sprintf('会员上线ID进入时2-: %d', $member->parent_id));
            $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

            if ($parent_is_agent) {
                if ($become_child == 1) {
                    if (empty($member->inviter) && $member->member_id != $parent->member_id) {
                        \Log::debug(sprintf('会员赋值 parent_id: %d', $parent->member_id));
                        $member->parent_id = $parent->member_id;
                        $member->child_time = time();
                        $member->inviter = 1;

                        $member->save();

                        event(new MemberCreateRelationEvent($member->member_id, $member->parent_id));
                        //message notice
                        self::sendAgentNotify($member->member_id, $parent->member_id);
                    }
                }
            }
        }

        event(new MemberFirstChilderenEvent(['member_id' => $uid]));
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
    public static function checkOrderPay($uid)
    {
        $set = self::getSetInfo()->first();
        $become_check = intval($set->become_check);

        \Log::debug('付款后');
        if (empty($set)) {
            return;
        }

        $member = MemberShopInfo::getMemberShopInfo($uid);
        if (empty($member)) {
            return;
        }
        \Log::debug(sprintf('会员上线-%d', $member->parent_id));
        $become_child = intval($set->become_child);

        $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);

        $parent_is_agent = !empty($parent) && $parent->is_agent == 1 && $parent->status == 2;

        //成为下线
        if ($member->parent_id == 0) {
            if ($become_child == 2 && empty($member->inviter)) {
                $member->child_time = time();
                $member->inviter = 1;

                $member->save();
            }
        } else {
            if ($parent_is_agent) {
                if ($become_child == 2) {
                    if (empty($member->inviter) && $member->member_id != $parent->member_id) {
                        $member->parent_id = $parent->member_id;
                        $member->child_time = time();
                        $member->inviter = 1;

                        $member->save();

                        event(new MemberCreateRelationEvent($member->member_id, $member->parent_id));

                        //message notice
                        self::sendAgentNotify($member->member_id, $parent->member_id);
                    }
                }
            }
        }

        //发展下线资格
        $isagent = $member->is_agent == 1 && $member->status == 2;

        if (!$isagent && empty($set->become_order)) {
            if (intval($set->become) == 4 && !empty($set->become_goods_id)) {
                $result = self::checkOrderGoods($set->become_goods_id, $uid);

                if ($result) {
                    $member->is_agent = 1;

                    if ($become_check == 0) {
                        $member->status = 2;
                        $member->agent_time = time();

                        if ($member->inviter == 0) {
                            $member->inviter = 1;
                            $member->parent_id = 0;
                        }
                    } else {
                        $member->status = 1;
                    }

                    if ($member->save()) {
                        self::setRelationInfo($member);
                    }
                }
            }

            if ($set->become == 2 || $set->become == 3) {
                $parentisagent = true;

                if (!empty($member->parent_id)) {
                    $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    if ($set->become == '2') {
                        $ordercount = Order::getCostTotalNum($member->member_id);
                        \Log::debug('用户：'. $ordercount);
                        \Log::debug('系统：'. intval($set->become_ordercount));
                        $can = $ordercount >= intval($set->become_ordercount);
                    } else if ($set->become == '3') {
                        $moneycount = Order::getCostTotalPrice($member->member_id);

                        $can = $moneycount >= floatval($set->become_moneycount);
                    }

                    if ($can) {
                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            if ($member->inviter == 0) {
                                $member->inviter = 1;
                                $member->parent_id = 0;
                            }
                        } else {
                            $member->status = 1;
                        }

                        if ($member->save()) {
                            self::setRelationInfo($member);
                        }
                    }
                }
            }

            if ($set->become == 5) {
                $parentisagent = true;
                if (!empty($member->parent_id)) {
                    $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
                    if ($sales_money >= $set->become_selfmoney) {
                        $can = true;
                    }

                    if ($can) {
                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            if ($member->inviter == 0) {
                                $member->inviter = 1;
                                $member->parent_id = 0;
                            }
                        } else {
                            $member->status = 1;
                        }

                        if ($member->save()) {
                            self::setRelationInfo($member);
                        }
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
    public static function checkOrderFinish($uid)
    {
        $set = self::getSetInfo()->first();
        $become_check = intval($set->become_check);

        \Log::debug('订单完成');

        if (empty($set)) {
            return;
        }
        \Log::debug('关系链设置');
        $member = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($member)) {
            return;
        }

        $isagent = $member->is_agent == 1 && $member->status == 2;

        if (!$isagent && $set->become_order == 1) {
            //购买指定商品
            if (intval($set->become) == 4 && !empty($set->become_goods_id)) {
                $result = self::checkOrderGoods($set->become_goods_id, $uid);

                if ($result) {
                    $member->is_agent = 1;

                    if ($become_check == 0) {
                        $member->status = 2;
                        $member->agent_time = time();

                        if ($member->inviter == 0) {
                            $member->inviter = 1;
                            $member->parent_id = 0;
                        }
                    } else {
                        $member->status = 1;
                    }

                    if ($member->save()) {
                        self::setRelationInfo($member);
                    }
                }
            }

            \Log::debug('条件完成后');
            //消费
            if ($set->become == 2 || $set->become == 3) {
                $parentisagent = true;

                if (!empty($member->parent_id)) {
                    $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    if ($set->become == '2') {
                        $ordercount = Order::getCostTotalNum($member->member_id);
                        \Log::debug('系统：' . intval($set->become_ordercount));
                        \Log::debug('会员：' . $ordercount);
                        $can = $ordercount >= intval($set->become_ordercount);
                    } else if ($set->become == '3') {
                        $moneycount = Order::getCostTotalPrice($member->member_id);

                        $can = $moneycount >= floatval($set->become_moneycount);
                    }

                    if ($can) {
                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            if ($member->inviter == 0) {
                                $member->inviter = 1;
                                $member->parent_id = 0;
                            }
                        } else {
                            $member->status = 1;
                        }

                        if ($member->save()) {
                            self::setRelationInfo($member);
                        }
                    }
                }
            }

            if ($set->become == 5) {
                $parentisagent = true;
                if (!empty($member->parent_id)) {
                    $parent = MemberShopInfo::getMemberShopInfo($member->parent_id);
                    if (empty($parent) || $parent->is_agent != 1 || $parent->status != 2) {
                        $parentisagent = false;
                    }
                }

                if ($parentisagent) {
                    $can = false;

                    $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
                    if ($sales_money >= $set->become_selfmoney) {
                        $can = true;
                    }

                    if ($can) {
                        $member->is_agent = 1;

                        if ($become_check == 0) {
                            $member->status = 2;
                            $member->agent_time = time();

                            if ($member->inviter == 0) {
                                $member->inviter = 1;
                                $member->parent_id = 0;
                            }
                        } else {
                            $member->status = 1;
                        }

                        if ($member->save()) {
                            self::setRelationInfo($member);
                        }
                    }
                }
            }
        }
    }

    /**
     * 获得推广权限通知
     *
     * @param $uid
     */
    public static function sendGeneralizeNotify($uid)
    {
        \Log::debug('获得推广权限通知');

        $member = Member::getMemberByUid($uid)->with('hasOneFans')->first();

        event(new MemberRelationEvent($member));

        $member->follow = $member->hasOneFans->follow;
        $member->openid = $member->hasOneFans->openid;

        $uniacid = \YunShop::app()->uniacid ?: $member->uniacid;

        self::generalizeMessage($member, $uniacid);
    }

    public static function generalizeMessage($member, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($member->uid)->with('hasOneFans')->first();

        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('relation_base')['member_agent'];

        if (!$temp_id) {
            return;
        }

        $params = [
            ['name' => '昵称', 'value' => $member->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())]
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);

        if (!$msg) {
            return;
        }

        MessageService::notice(MessageTemp::$template_id, $msg, $member->uid);
    }

    /**
     * 新增下线通知
     *
     * @param $uid
     */
    public static function sendAgentNotify($uid, $puid)
    {
        \Log::debug('新增下线通知');
        $parent = Member::getMemberByUid($puid)->with('hasOneFans')->first();
        $parent->follow = $parent->hasOneFans->follow;
        $parent->openid = $parent->hasOneFans->openid;

        $member = Member::getMemberByUid($uid)->first();

        $uniacid = \YunShop::app()->uniacid ?: $parent->uniacid;

        self::agentMessage($parent, $member, $uniacid);
    }

    public static function agentMessage($parent, $member, $uniacid)
    {
        $noticeMember = Member::getMemberByUid($parent->uid)->with('hasOneFans')->first();

        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('relation_base')['member_new_lower'];

        if (!$temp_id) {
            return;
        }

        $params = [
            ['name' => '昵称', 'value' => $parent->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '下级昵称', 'value' => $member->nickname]
        ];

        $msg = MessageTemp::getSendMsg($temp_id, $params);

        if (!$msg) {
            return;
        }

        MessageService::notice(MessageTemp::$template_id, $msg, $parent->uid);
    }

    private static function setRelationInfo($member)
    {
        if ($member->is_agent == 1 && $member->status == 2) {
            Member::setMemberRelation($member->member_id,$member->parent_id);

            //message notice
            self::sendGeneralizeNotify($member->member_id);
        }
    }

}

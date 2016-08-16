<?php
/**
 * 管理后台APP API分销商
 *
 * PHP version 5.6.15
 *
 * @package
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\commission;

use model\api\order;

class Apply extends \api\YZ
{
    private $commission_apply_model;
    private $set;
    private $member;
    private $order_list;
    private $apply_info;
    private $commission_info;
    public function __construct()
    {
        parent::__construct();
        $this->commission_apply_model = new \model\api\commissionApply();
        $this->set = $this->commission_apply_model->getSet();

    }
    private function _Info(){
        $para = $this->getPara();
        $this->apply_info = $this->_getApplyInfo($para['commission_apply_id'], $para['uniacid']);
        $apply = $this->apply_info;

        $this->agent_level = $this->_getAgentLevel($apply['mid']);
        $agent_level = $this->agent_level;

        $this->order_list = $this->_getOrderInfo($apply['orderids'], $para['uniacid'], $agent_level['id']);

        $this->member = $this->_getMemberInfo($apply['mid']);

        $this->commission_info = $this->_getTotalCommissionInfo($this->order_list);
    }


    private function _getTotalCommissionInfo()
    {
        $total =  array_sum(array_column($this->order_list, 'commission'));
        $pay = array_sum(array_column($this->order_list, 'commission_pay'));
        return compact('total','pay');
    }

    public function pay()
    {
        $this->ca('commission.apply.pay');
        $this->_Info();
        $para = $this->getPara();
        $apply = $this->apply_info;
        if ($apply['status'] != 2 ) {
            $this->returnError('此操作与提现申请状态不符');
        }
        $member = $this->member;
        $order = $this->order_list;
        $now_time = time();
        $totalpay = $this->commission_info['total'];
        $totalcommission =  $this->commission_info['pay'];
        if ($apply['type'] == 1 || $apply['type'] == 2) {
            $totalpay *= 100;
        }

        if ($apply['type'] == 2) {
            if ($totalpay <= 20000 && $totalpay >= 1) {
                $result = m('finance')->sendredpack($member['openid'], $totalpay, 0, $desc = '佣金提现金额', $act_name = '佣金提现金额', $remark = '佣金提现金额以红包形式发送');
            } else {
                $this->returnError('红包提现金额限制1-200元！');
            }
        } else {
            $result = m('finance')->pay($member['openid'], $apply['type'], $totalpay, $apply['applyno']);
        }

        if (is_error($result)) {
            if (strexists($result['message'], '系统繁忙')) {
                $updateno['applyno'] = $apply['applyno'] = m('common')->createNO('commission_apply', 'applyno', 'CA');
                pdo_update('sz_yi_commission_apply', $updateno, array('id' => $apply['id']));
                $result = m('finance')->pay($member['openid'], $apply['type'], $totalpay, $apply['applyno']);
                if (is_error($result)) {
                    $this->returnError($result['message']);
                }
            }
            $this->returnError($result['message']);
        }
        foreach ($order as $row) {
            foreach ($row['goods'] as $g) {
                $update = array();
                if ($row['level'] == 1 && $g['status1'] == 2) {
                    $update = array('paytime1' => $now_time, 'status1' => 3);
                } else if ($row['level'] == 2 && $g['status2'] == 2) {
                    $update = array('paytime2' => $now_time, 'status2' => 3);
                } else if ($row['level'] == 3 && $g['status3'] == 2) {
                    $update = array('paytime3' => $now_time, 'status3' => 3);
                }
                if (!empty($update)) {
                    pdo_update('sz_yi_order_goods', $update, array('id' => $g['id']));
                }
            }
        }


        pdo_update('sz_yi_commission_apply', array('status' => 3, 'paytime' => $now_time, 'commission_pay' => $totalpay), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
        $log = array('uniacid' => $para['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'commission' => $totalcommission, 'commission_pay' => $totalpay, 'createtime' => $now_time);
        pdo_insert('sz_yi_commission_log', $log);
        $this->commission_apply_model->sendMessage($member['openid'], array('commission' => $totalpay, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_PAY);
        $this->commission_apply_model->upgradeLevelByCommissionOK($member['openid']);
        plog('commission.apply.pay', "佣金打款 ID: {$para['commission_apply_id']} 申请编号: {$apply['applyno']} 总佣金: {$totalcommission} 审核通过佣金: {$totalpay} ");
        $this->returnSuccess('佣金打款处理成功!');
    }

    public function cancel()
    {
        $this->ca('commission.apply.cancel');
        $this->_Info();

        $para = $this->getPara();
        $apply = $this->apply_info;
        //$member = $this->member_info;
        $order = $this->order_list;
        if (!($apply['status'] == 2 || $apply['status'] == -1)) {
            $this->returnError('此操作与提现申请状态不符');
        }

        foreach ($order as $row) {
            $update = array();
            foreach ($row['goods'] as $g) {
                $update = array();
                if ($row['level'] == 1) {
                    $update = array('checktime1' => 0, 'status1' => 1);
                } else if ($row['level'] == 2) {
                    $update = array('checktime2' => 0, 'status2' => 1);
                } else if ($row['level'] == 3) {
                    $update = array('checktime3' => 0, 'status3' => 1);
                }
                if (!empty($update)) {
                    pdo_update('sz_yi_order_goods', $update, array('id' => $g['id']));
                }
            }
        }
        pdo_update('sz_yi_commission_apply', array('status' => 1, 'checktime' => 0, 'invalidtime' => 0), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
        plog('commission.apply.cancel', "重新审核申请 ID: {$para['commission_apply_id']} 申请编号: {$apply['applyno']} ");
        $this->returnSuccess([], '撤销审核处理成功!');
    }

    public function check()
    {
        $this->ca('commission.apply.check');
        $para = $this->getPara();
        $this->_Info();

        $apply = $this->apply_info;
        if ($apply['status'] != 1) {
            $this->returnError('此操作与提现申请状态不符');
        }

        $member = $this->member;
        $order = $this->order_list;

        $agent_level = $this->agent_level;
        $paycommission = 0;
        $ogids = array();

        foreach ($order as $row) {
            $goods = pdo_fetchall('SELECT id from ' . tablename('sz_yi_order_goods') . ' where uniacid = :uniacid and orderid=:orderid and nocommission=0', array(':uniacid' => $para['uniacid'], ':orderid' => $row['order_id']));
            foreach ($goods as $g) {
                $ogids[] = $g['id'];
            }
        }
        if (!is_array($ogids)) {
            $this->returnError('数据出错，请重新设置!');
        }
        $time = time();
        $isAllUncheck = true;
        foreach ($ogids as $ogid) {
            $g = pdo_fetch('SELECT total, commission1,commission2,commission3,commissions from ' . tablename('sz_yi_order_goods') . '  ' . 'where id=:id and uniacid = :uniacid limit 1', array(':uniacid' => $para['uniacid'], ':id' => $ogid));
            if (empty($g)) {
                continue;
            }
            $commissions = iunserializer($g['commissions']);
            if ($this->set['level'] >= 1) {
                $commission = iunserializer($g['commission1']);
                if (empty($commissions)) {
                    $g['commission1'] = isset($commission['level' . $agent_level['id']]) ? $commission['level' . $agent_level['id']] : $commission['default'];
                } else {
                    $g['commission1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
                }
            }
            if ($this->set['level'] >= 2) {
                $commission = iunserializer($g['commission2']);
                if (empty($commissions)) {
                    $g['commission2'] = isset($commission['level' . $agent_level['id']]) ? $commission['level' . $agent_level['id']] : $commission['default'];
                } else {
                    $g['commission2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
                }
            }
            if ($this->set['level'] >= 3) {
                $commission = iunserializer($g['commission3']);
                if (empty($commissions)) {
                    $g['commission3'] = isset($commission['level' . $agent_level['id']]) ? $commission['level' . $agent_level['id']] : $commission['default'];
                } else {
                    $g['commission3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
                }
            }
            $update = array();
            $level = $agent_level['id'];

            if (isset($para["status"][$ogid])) {
                if (intval($para["status"][$ogid]) == 2) {
                    $paycommission += $g["commission{$level}"];
                    $isAllUncheck = false;
                }
                $update = array("checktime{$level}" => $time, "status{$level}" => intval($para["status"][$ogid]), "content{$level}" => '');
            }
            if (!empty($update)) {
                pdo_update('sz_yi_order_goods', $update, array('id' => $ogid));
            }
        }

        if ($isAllUncheck) {
            pdo_update('sz_yi_commission_apply', array('status' => -1, 'invalidtime' => $time), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
        } else {
            pdo_update('sz_yi_commission_apply', array('status' => 2, 'checktime' => $time), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
            $this->commission_apply_model->sendMessage($member['openid'], array('commission' => $paycommission, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_CHECK);
        }
        //todo 需要重构_getOrderGoods 提取出获取这两个统计的方法
        $totalmoney = 0;
        plog('commission.apply.check', "佣金审核 ID: {$para['commission_apply_id']} 申请编号: {$apply['applyno']} 总佣金: {$totalmoney} 审核通过佣金: {$paycommission} ");
        $this->returnSuccess('', '申请处理成功!');
    }

    public function getInfo()
    {
        $this->_Info();
        $apply = $this->apply_info;

        if (empty($apply)) {
            $this->returnError('提现申请不存在!');
        }
        if ($apply['status'] == -1) {
            $this->ca('commission.apply.view_1');
        } else {
            $this->ca('commission.apply.view' . $apply['status']);
        }

        $member = $this->member;

        $order = $this->order_list;

        $totalcount = count($order);
        $totalmoney = array_sum(array_column($order, 'price'));
        $apply = array_part('type,type_name,status_name,applytime,checktime,paytime,invalidtime,status,total,commission', $apply) + compact('totalcount', 'totalmoney');

        $agent_level = $this->agent_level;
        $member['level_name'] = $agent_level['levelname'];
        $commission = $this->commission_info;
        $res = compact('order', 'member', 'apply','commission');
        dump($res);
        $this->returnSuccess($res);
    }

    private function _getApplyInfo($commission_apply_id, $uniacid)
    {
        $id = intval($commission_apply_id);
        //dump($this->commission_apply_model);
        $apply = $this->commission_apply_model->getBaseInfo($uniacid, $id);

        return $apply;
    }

    private function _getMemberInfo($member_id)
    {
        $member = $this->commission_apply_model->getInfo($member_id, array('total', 'ok', 'apply', 'lock', 'check'));
        $member = array_part('nickname,avatar,realname,id,mobile,weixin,commission_total,commission_apply,commission_check,commission_lock,openid', $member);
        $member['member_id'] = $member['id'];
        unset($member['id']);
        //$agentLevel[‘levelname’];

        return $member;
    }

    private function _getAgentLevel($mid)
    {
        $agentLevel = $this->commission_apply_model->getLevel($mid);
        if (empty($agentLevel['id'])) {
            $agentLevel = array('levelname' => empty($this->set['levelname']) ? '普通等级' : $this->set['levelname'], 'commission1' => $this->set['commission1'], 'commission2' => $this->set['commission2'], 'commission3' => $this->set['commission3'],);
        }
        return $agentLevel;
    }

    private function _getOrderInfo($orderids, $uniacid, $agent_level_id)
    {
        $orderids = iunserializer($orderids);
        if (!is_array($orderids) || count($orderids) <= 0) {
            $this->returnError('无任何订单，无法查看!');
        }

        $id_array = array_column($orderids, 'orderid');

        $order_list = pdo_fetchall('select id as order_id, ordersn,price,createtime, paytype,dispatchprice from ' . tablename('sz_yi_order') . ' where id in ( ' . implode(',', $id_array) . ' );');

        $order_level_map = array_column($orderids, 'level', 'orderid');
        foreach ($order_list as &$order) {
            $order['level'] = $order_level_map[$order['order_id']];

            $goods_list = $this->_getOrderGoods($uniacid, $order, $agent_level_id);
            $order['commission'] = array_sum(array_column($goods_list, 'commission'));
            $order['commission_pay'] = array_sum(array_column($goods_list, 'commission_pay'));

            $order['goods'] = $goods_list;
            $order['createtime'] = date('Y-m-d H:i', $order['createtime']);
            $order_model = new \model\api\order();
            $pay_type_name_mapping = $order_model->getPayTypeName();
            $order['pay_type_name'] = $pay_type_name_mapping[$order['paytype']];
        }
        return $order_list;
    }

    //todo 需要继续拆分出 $row 和 佣金和 支付和
    private function _getOrderGoods($uniacid, $order)
    {
        $goods_list = pdo_fetchall('SELECT og.id as order_goods_id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,og.optionname,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3,og.content1,og.content2,og.content3 
from ' . tablename('sz_yi_order_goods') . ' og' . ' 
left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid  ' . ' 
left join ' . tablename('sz_yi_order') . ' o on o.id=og.orderid  ' . ' 
where og.uniacid = :uniacid and og.orderid=:orderid and og.nocommission=0 
order by og.createtime  desc '
            , array(':uniacid' => $uniacid, ':orderid' => $order['order_id']));

        foreach ($goods_list as &$goods) {
            $goods = $this->_getCommissionInfo($goods, $order['level']);


            $goods['level'] = $order['level'];
            $goods = set_medias($goods, "thumb");
            $goods['total_price'] = $goods['realprice'] * $goods['total'];
            $goods = array_part('order_goods_id,thumb,title,realprice,total,total_price,status,status_name,optionname,commission,commission_name', $goods);
        }
        return $goods_list;
    }

    private function _getCommissionInfo($goods, $order_commission_level)
    {
        $commission_name_mapping = array(
            '1' => '一级佣金',
            '2' => '二级佣金',
            '3' => '三级佣金'
        );
        $status_name_mapping = array(
            '-1' => '未通过',
            '2' => '已通过',
            '3' => '已打款'
        );
        $agent_level_id = $this->agent_level['id'];
        $commissions = iunserializer($goods['commissions']);
        //dump($commissions);exit;
        /*如果商城分销等级大于或等于1
        *  商品的分销佣金 等于 商品的一级分销佣金
         * 如果提现申请里的订单等级 等于 1
         *    商品的1级佣金 计入总佣金
         *    如果商品的提心申请通过了
         *       商品的1级别佣金 计入已支付佣金
        */
        $shop_max_commission_level = $this->set['level'];
        //dump($order_commission_level);
        $commission_level = min($shop_max_commission_level, $order_commission_level);//
        $commission = iunserializer($goods['commission' . $commission_level]);
        if (empty($commissions)) {
            $goods['commission'] = isset($commission['level' . $agent_level_id]) ? $commission['level' . $agent_level_id] : $commission['default'];

        } else {
            $goods['commission'] = isset($commissions['level' . $commission_level]) ? floatval($commissions['level' . $commission_level]) : 0;
        }
        if ($goods['status' . $commission_level] >= 2) {
            $goods['commission_pay'] = $goods['commission'];
        } else {
            $goods['commission_pay'] = 0;
        }
        $goods['commission_name'] = $commission_name_mapping[$commission_level];
        $goods['status'] = $goods['status' . $commission_level];
        $goods['status_name'] = $status_name_mapping[$goods['status']];
        return $goods;
    }

    public function index()
    {
        $para = $this->getPara();
        if ($para['status'] == -1) {
            $this->ca('commission.apply.view_1');
        } else {
            $this->ca('commission.apply.view' . $para['status']);
        }

        $list = $this->commission_apply_model->getList(array(
            'id' => $para['commission_apply_id'],
            'uniacid' => $para['uniacid'],
            'status' => $para['status'],
        ));


        dump($list);
        $this->returnSuccess($list);
    }
}

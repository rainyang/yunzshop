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
namespace admin\api\controller\commission;

class Apply extends \admin\api\YZ
{
    private $commission_apply_model;
    private $commission_model;
    private $set;
    private $member;
    private $apply_info;
    private $agent_level;
    private $commission_info;

    public function __construct()
    {
        parent::__construct();
        $para = $this->getPara();

        $this->commission_apply_model = new \admin\api\model\commissionApply($para['uniacid']);
        $this->commission_model = new \admin\api\model\commission();
        $this->set = $this->commission_model->getSet();

    }

    private function _Info()
    {
        $para = $this->getPara();
        $this->apply_info = $this->commission_apply_model->getInfo($para['commission_apply_id'], $para['uniacid']);
        $apply = $this->apply_info;

        $this->agent_level = $this->_getAgentLevel($apply['mid']);

        $this->member = $this->_getMemberInfo($apply['mid']);
        //dump($apply['order_list']);
        $this->commission_info = $this->_getTotalCommissionInfo($apply['order_list']);
    }


    private function _getTotalCommissionInfo($order_list)
    {
        $total = array_sum(array_column($order_list, 'commission'));
        $pay = array_sum(array_column($order_list, 'commission_pay'));
        $res = array(
            'total' => $total,
            'pay' => $pay
        );
        return $res;
    }

    public function pay()
    {
        $this->ca('commission.apply.pay');
        $this->_Info();
        $para = $this->getPara();
        $apply = $this->apply_info;
        if ($apply['status'] != 2) {
            $this->returnError('此操作与提现申请状态不符');
        }
        $order = $apply['order_list'];
        $member = $this->member;
        $now_time = time();
        $totalpay = $this->commission_info['total'];
        $totalcommission = $this->commission_info['pay'];
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
        $this->commission_model->sendMessage($member['openid'], array('commission' => $totalpay, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_PAY);
        $this->commission_model->upgradeLevelByCommissionOK($member['openid']);
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
        $order = $apply['order_list'];
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
        $this->returnSuccess(array(), '撤销审核处理成功!');
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
        $order = $apply['order_list'];

        $agent_level = $this->agent_level;

        $order_goods_list = $this->commission_apply_model->getCheckOrderGoods(array_column($order, 'order_id'), $para['status'], $agent_level['id']);
        //dump($order_goods_list);exit;
        $commission_info = $this->_getTotalCommissionInfo($order_goods_list);
        $paycommission = $commission_info['commission_pay'];
        $totalmoney = $commission_info['commission'];

        $time = time();
        if ($paycommission) {
            pdo_update('sz_yi_commission_apply', array('status' => -1, 'invalidtime' => $time), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
        } else {
            pdo_update('sz_yi_commission_apply', array('status' => 2, 'checktime' => $time), array('id' => $para['commission_apply_id'], 'uniacid' => $para['uniacid']));
            $this->commission_model->sendMessage($member['openid'], array('commission' => $paycommission, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_CHECK);
        }

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

        $order = $apply['order_list'];

        $totalcount = count($order);
        $totalmoney = array_sum(array_column($order, 'price'));
        $apply = array_part('type,type_name,status_name,applytime,checktime,paytime,invalidtime,status,total,commission', $apply)
            + array(
                'totalcount' => $totalcount,
                'totalmoney' => $totalmoney
            );

        $agent_level = $this->agent_level;
        $member['level_name'] = $agent_level['levelname'];
        $commission = $this->commission_info;
        $res = array(
            'order' => $order,
            'member' => $member,
            'apply' => $apply,
            'commission' => $commission
        );
        dump($res);
        $this->returnSuccess($res);
    }

    private function _getMemberInfo($member_id)
    {
        $member = $this->commission_model->getInfo($member_id, array('total', 'ok', 'apply', 'lock', 'check'));
        $member = array_part('nickname,avatar,realname,id,mobile,weixin,commission_total,commission_apply,commission_check,commission_lock,openid', $member);
        $member['member_id'] = $member['id'];
        unset($member['id']);
        //$agentLevel[‘levelname’];

        return $member;
    }

    private function _getAgentLevel($mid)
    {
        $agentLevel = $this->commission_model->getLevel($mid);
        return $agentLevel;
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

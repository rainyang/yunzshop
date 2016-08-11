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

    public function __construct()
    {
        parent::__construct();
        $this->commission_apply_model = new \model\api\commissionApply();
        //$this->validate('username','password');
    }

    /*    public function check(){
            if (checksubmit('submit_check') && $apply['status'] == 1) {
                ca('commission.apply.check');
                $paycommission = 0;
                $ogids = array();
                foreach ($list as $row) {
                    $goods = pdo_fetchall('SELECT id from ' . tablename('sz_yi_order_goods') . ' where uniacid = :uniacid and orderid=:orderid and nocommission=0', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
                    foreach ($goods as $g) {
                        $ogids[] = $g['id'];
                    }
                }
                if (!is_array($ogids)) {
                    message('数据出错，请重新设置!', '', 'error');
                }
                $time = time();
                $isAllUncheck = true;
                foreach ($ogids as $ogid) {
                    $g = pdo_fetch('SELECT total, commission1,commission2,commission3,commissions from ' . tablename('sz_yi_order_goods') . '  ' . 'where id=:id and uniacid = :uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $ogid));
                    if (empty($g)) {
                        continue;
                    }
                    $commissions = iunserializer($g['commissions']);
                    if ($this->set['level'] >= 1) {
                        $commission = iunserializer($g['commission1']);
                        if (empty($commissions)) {
                            $g['commission1'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
                        } else {
                            $g['commission1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
                        }
                    }
                    if ($this->set['level'] >= 2) {
                        $commission = iunserializer($g['commission2']);
                        if (empty($commissions)) {
                            $g['commission2'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
                        } else {
                            $g['commission2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
                        }
                    }
                    if ($this->set['level'] >= 3) {
                        $commission = iunserializer($g['commission3']);
                        if (empty($commissions)) {
                            $g['commission3'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
                        } else {
                            $g['commission3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
                        }
                    }
                    $update = array();
                    if (isset($para['status1'][$ogid])) {
                        if (intval($_GPC['status1'][$ogid]) == 2) {
                            $paycommission += $g['commission1'];
                            $isAllUncheck = false;
                        }
                        $update = array('checktime1' => $time, 'status1' => intval($_GPC['status1'][$ogid]), 'content1' => $_GPC['content1'][$ogid]);
                    } else if (isset($_GPC['status2'][$ogid])) {
                        if (intval($_GPC['status2'][$ogid]) == 2) {
                            $paycommission += $g['commission2'];
                            $isAllUncheck = false;
                        }
                        $update = array('checktime2' => $time, 'status2' => intval($_GPC['status2'][$ogid]), 'content2' => $_GPC['content2'][$ogid]);
                    } else if (isset($_GPC['status3'][$ogid])) {
                        if (intval($_GPC['status3'][$ogid]) == 2) {
                            $paycommission += $g['commission3'];
                            $isAllUncheck = false;
                        }
                        $update = array('checktime3' => $time, 'status3' => intval($_GPC['status3'][$ogid]), 'content3' => $_GPC['content3'][$ogid]);
                    }
                    if (!empty($update)) {
                        pdo_update('sz_yi_order_goods', $update, array('id' => $ogid));
                    }
                }
                if ($isAllUncheck) {
                    pdo_update('sz_yi_commission_apply', array('status' => -1, 'invalidtime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
                } else {
                    pdo_update('sz_yi_commission_apply', array('status' => 2, 'checktime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
                    $this->model->sendMessage($member['openid'], array('commission' => $paycommission, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_CHECK);
                }
                plog('commission.apply.check', "佣金审核 ID: {$id} 申请编号: {$apply['applyno']} 总佣金: {$totalmoney} 审核通过佣金: {$paycommission} ");
                message('申请处理成功!', $this->createPluginWebUrl('commission/apply', array('status' => $apply['status'])), 'success');
            }
        }*/
    public function getInfo()
    {
        $para = $this->getPara();

        $apply = $this->_getApplyInfo($para['commission_apply_id'], $para['uniacid']);

        if (empty($apply)) {
            $this->returnError('提现申请不存在!');
        }
        if ($apply['status'] == -1) {
            $this->ca('commission.apply.view_1');
        } else {
            $this->ca('commission.apply.view' . $apply['status']);
        }

        $member = $this->_getMemberInfo($apply['mid']);

        $agent_level = $this->_getAgentLevel($apply['mid']);
        $order = $this->_getOrderInfo($apply['orderids'], $para['uniacid'],$agent_level['id']);

        $totalcount = count($order);
        $totalmoney = 2000;//array_sum(array_column($order,'price'));
        $apply = array_part('commission,type,type_name,status_name,applytime,status,total,commission', $apply) + compact('totalcount','totalmoney');


        $member['level_name']=$agent_level['levelname'];

        $res = compact('order', 'member', 'apply');
        dump($res);
        $this->returnSuccess($res);
    }

    private function _getApplyInfo($commission_apply_id, $uniacid)
    {
        $id = intval($commission_apply_id);
        //dump($this->commission_apply_model);
        $apply = $this->commission_apply_model->getBaseInfo($uniacid,$id);

        return $apply;
    }

    private function _getMemberInfo($member_id)
    {
        $member = $this->commission_apply_model->getInfo($member_id, array('total', 'ok', 'apply', 'lock', 'check'));
        $member = array_part('nickname,avatar,realname,id,mobile,weixin,commission_total,commission_apply,commission_check,commission_lock', $member);
        $member['member_id'] = $member['id'];
        unset($member['id']);
        //$agentLevel[‘levelname’];

        return $member;
    }
    private function _getAgentLevel($mid){
        $agentLevel = $this->commission_apply_model->getLevel($mid);
        if (empty($agentLevel['id'])) {
            $agentLevel = array('levelname' => empty($this->set['levelname']) ? '普通等级' : $this->set['levelname'], 'commission1' => $this->set['commission1'], 'commission2' => $this->set['commission2'], 'commission3' => $this->set['commission3'],);
        }
        return $agentLevel;
    }
    private function _getOrderInfo( $orderids, $uniacid,$agent_level_id)
    {

        //dump($agentLevel);
        $orderids = iunserializer($orderids);
        if (!is_array($orderids) || count($orderids) <= 0) {
            $this->returnError('无任何订单，无法查看!');
        }
        $ids = array();
        foreach ($orderids as $o) {
            $ids[] = $o['orderid'];
        }
        $list = pdo_fetchall('select id as order_id, ordersn,price,createtime, paytype,dispatchprice from ' . tablename('sz_yi_order') . ' where  id in ( ' . implode(',', $ids) . ' );');
        $totalcommission = 0;
        $totalpay = 0;
        foreach ($list as &$row) {
            foreach ($orderids as $o) {
                if ($o['orderid'] == $row['order_id']) {
                    $row['level'] = $o['level'];
                    break;
                }
            }
            $goods = pdo_fetchall('SELECT og.id as order_goods_id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,og.optionname,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3,og.content1,og.content2,og.content3 
from ' . tablename('sz_yi_order_goods') . ' og' . ' 
left join ' . tablename('sz_yi_goods') . ' g on g.id=og.goodsid  ' . ' 
left join ' . tablename('sz_yi_order') . ' o on o.id=og.orderid  ' . ' 
where og.uniacid = :uniacid and og.orderid=:orderid and og.nocommission=0 
order by og.createtime  desc '
                , array(':uniacid' => $uniacid, ':orderid' => $row['order_id']));
            foreach ($goods as &$g) {
                $commissions = iunserializer($g['commissions']);
                if ($this->set['level'] >= 1) {
                    $commission = iunserializer($g['commission1']);
                    if (empty($commissions)) {
                        $g['commission1'] = isset($commission['level' . $agent_level_id]) ? $commission['level' . $agent_level_id] : $commission['default'];
                    } else {
                        $g['commission1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
                    }
                    if ($row['level'] == 1) {
                        $totalcommission += $g['commission1'];
                        if ($g['status1'] >= 2) {
                            $totalpay += $g['commission1'];
                        }
                    }
                }
                if ($this->set['level'] >= 2) {
                    $commission = iunserializer($g['commission2']);
                    if (empty($commissions)) {
                        $g['commission2'] = isset($commission['level' . $agent_level_id]) ? $commission['level' . $agent_level_id] : $commission['default'];
                    } else {
                        $g['commission2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
                    }
                    if ($row['level'] == 2) {
                        $totalcommission += $g['commission2'];
                        if ($g['status2'] >= 2) {
                            $totalpay += $g['commission2'];
                        }
                    }
                }
                if ($this->set['level'] >= 3) {
                    $commission = iunserializer($g['commission3']);
                    if (empty($commissions)) {
                        $g['commission3'] = isset($commission['level' . $agent_level_id]) ? $commission['level' . $agent_level_id] : $commission['default'];
                    } else {
                        $g['commission3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
                    }
                    if ($row['level'] == 3) {
                        $totalcommission += $g['commission3'];
                        if ($g['status3'] >= 2) {
                            $totalpay += $g['commission3'];
                        }
                    }
                }
                $g['level'] = $row['level'];
                $g = set_medias($g, "thumb");
                $g['total_price'] = $g['realprice']*$g['total'];
                $g = array_part('order_goods_id,thumb,title,realprice,total,total_price,optionname,commission1,commission2,commission3', $g);
            }
            $row['goods'] = $goods;
            $row['createtime'] = date('Y-m-d H:i',$row['createtime']);
            $order_model = new \model\api\order();
            $pay_type_name_mapping = $order_model->getPayTypeName();
            $row['pay_type_name'] = $pay_type_name_mapping[$row['paytype']];

        }
        return $list;
    }
    //private function _
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

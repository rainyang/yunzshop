<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$id=$_GPC['id']? $_GPC['id'] : '0';
$page1 = 'statistics';
if($operation == 'display' && $_W['isajax']){

    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 10;
    $condition = ' o.uniacid = :uniacid';
    $params    = array(':uniacid' => $_W['uniacid']);
    $condition .= ' and co.cashier_store_id = :id';
    $params[':id'] = $id;
    if (!empty($_GPC['time'])) {
        if ($_GPC['searchtime'] == '1') {
            $condition .= ' AND o.createtime >= :start AND o.createtime <= :end';
            $params[':start'] = $_GPC['time']['start'];
            $params[':end']   = $_GPC['time']['end'];
       
        }
    }

    $sql   = 'SELECT o.*,co.cashier_store_id,co.order_id FROM ' . tablename('sz_yi_order') . ' o left join '.tablename('sz_yi_cashier_order').' co on o.id = co.order_id '.' where 1 and '.$condition.' and o.status = 3 and cashier = 1 ORDER BY o.id DESC ';
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o left join '.tablename('sz_yi_cashier_order').' co on o.id = co.order_id '.' where 1 and o.cashier = 1 and  o.status = 3 and '.$condition, $params);
    $store = pdo_fetch(' select * from '.tablename('sz_yi_cashier_store').' where uniacid = '.$_W['uniacid'].' and id='.$id);
    $pager = pagination($total, $page, $pagesize);
    foreach ($list as &$row) {
        if($row['deredpack'] == 1 && $row['decommission'] == 1 && $row['decredits'] == 1){
            $row['text'] = '(已扣除佣金和红包费用以及奖励余额费用)';
        }
        else if ($row['deredpack'] == 1 && $row['decommission'] == 1 && $row['decredits'] != 1){
            $row['text'] = '(已扣除红包和佣金费用)';
        }
        else if ($row['decommission'] == 1 && $row['decredits'] == 1 && $row['deredpack'] != 1){
            $row['text'] = '(已扣除佣金和奖励余额费用)';
        }
        else if ($row['deredpack'] == 1 && $row['decredits'] == 1 && $row['decommission'] != 1){
            $row['text'] = '(已扣除红包和奖励余额费用)';
        }
        else if ($row['decredits'] == 1){
            $row['text'] = '(已扣除奖励余额费用)';
        }
        else if ($row['deredpack'] == 1){
            $row['text'] = '(已扣除红包费用)';
        }
        else if ($row['decommission'] == 1){
            $row['text'] = '(已扣除佣金费用)';
        }
        $totalmoney += $row['price'];
        $realtotalmoney += $row['realprice'];
        $commission=pdo_fetch('select commission1,commission2,commission3 from '.tablename('sz_yi_order_goods').' where orderid='.$row['id']);
        $row['commission1'] = iunserializer( $commission['commission1']);
        $row['commission2'] = iunserializer( $commission['commission2']);
        $row['commission3'] = iunserializer( $commission['commission3']);
        if($row['price'] >= $store['redpack_min']){
            $row['redpackmoney'] = $row['price']*($store['redpack']/100);
        }else{
            $row['redpackmoney'] = 0;
        }
        $row['creditpackmoney'] = $row['price']*($store['creditpack']/100);
        $row['platform_poundage'] = $row['price']*($store['settle_platform']/100);
        $row['credits'] = $this->model->setCredits($row['id'], true);
        $row['carrier'] = iunserializer($row['carrier']);
        $row['createtime'] = date('Y-m-d,H:i:s',$row['createtime']);
    }
    return show_json(1,array('list'=>$list,'total'=>$total,'totalmoney'=>$totalmoney,'realtotalmoney'=>$realtotalmoney ));

}
include $this->template('statistics');

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

// p('indiana')->createtime_winer('6','20160923159200837722');
// echo "<pre>";print_r(12);exit;
ca('indiana.period');

$set = $this->getSet();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == "display") {

    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $params    = array();

    $condition = array(
        ':uniacid'=>$_W['uniacid'],
        ':igid'=>$_GPC['id']
    );
    $sqls = "select COUNT(id) from ".tablename('sz_yi_indiana_period')."  where uniacid=:uniacid and ig_id=:igid ";
    $total = pdo_fetchcolumn($sqls, $condition);

	$sql = "select p.id as pid, p.period as pperiod, p.zong_codes, p.canyurenshu, p.status as pstatus, ig.*, g.thumb from ".tablename('sz_yi_indiana_period')." p 
	left join ".tablename('sz_yi_indiana_goods')." ig on (p.ig_id = ig.id) 
    left join ".tablename('sz_yi_goods')." g on (p.goodsid = g.id) 
	 where p.uniacid=:uniacid and p.ig_id=:igid order by p.id DESC LIMIT " . ($pindex - 1) * $psize . "," . $psize;

	$result = pdo_fetchall($sql,$condition);
    $pager = pagination($total, $pindex, $psize);
} elseif ($operation == "srecords") {

    $sql = "select * from ".tablename('sz_yi_indiana_period')." where uniacid=:uniacid and id=:id ";
    $condition = array(
        ':uniacid'=>$_W['uniacid'],
        ':id'=>$_GPC['id']
    );
    $period = pdo_fetch($sql, $condition);

    $goods = pdo_fetch("SELECT * FROM ".tablename('sz_yi_indiana_goods')." WHERE uniacid = {$_W['uniacid']} and good_id = {$period['goodsid']} and status <> 0");


    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $params    = array();

    $record_sql = "select * from ".tablename('sz_yi_indiana_record')." where uniacid=:uniacid and period_id=:period_id LIMIT " . ($pindex - 1) * $psize . "," . $psize ;
    $condition = array(
        ':uniacid'=>$_W['uniacid'],
        ':period_id'=>$_GPC['id']
    );
    $records = pdo_fetchall($record_sql, $condition);
    //购买人信息
    foreach($records as $key=>$value){
        $member = m('member')->getMember($value['openid']);
        $records[$key]['code'] = unserialize($value['codes']);
        $records[$key]['realname'] = $member['realname'];
        $records[$key]['mobile'] = $member['mobile'];
        $records[$key]['nickname'] = $member['nickname'];
        $records[$key]['allmoney'] = $value['count']*$goods['init_money'];
        $records[$key]['create_time'] = date('Y-m-d H:i', $value['create_time']);
    }
    $sqls = "select COUNT(id) from ".tablename('sz_yi_indiana_record')."  where uniacid=:uniacid and period_id=:period_id ";
    $total = pdo_fetchcolumn($sqls, $condition);

    $pager = pagination($total, $pindex, $psize);

} 

load()->func('tpl');
include $this->template('period');
exit;

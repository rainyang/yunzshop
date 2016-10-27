<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$period_num = $_GPC['periodnum']; // 期号
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));

$title = "计算详情";

//本期最后购买时间pdo_fetchcolumn
$lasttime = pdo_fetchcolumn("SELECT create_time FROM " . tablename('sz_yi_indiana_consumerecord') . " WHERE uniacid = :uniacid and period_num = :period_num order by create_time desc limit 1", array(
        ':uniacid'      => $_W['uniacid'],
        ':period_num'   => $period_num
    ));

$indiana = pdo_fetchall("SELECT ic.openid, ic.create_time, ic.microtime, m.nickname from " . tablename('sz_yi_indiana_consumerecord') . " ic 
    left join " . tablename('sz_yi_member') . " m on( ic.openid=m.openid )  
    where ic.uniacid = :uniacid  and ic.create_time <= :create_time order by ic.create_time desc limit 20 ",
    array(
        ':uniacid'      => $_W['uniacid'],
        ':create_time'   => $lasttime
    ));
    $numa = 0;
    foreach ($indiana as &$row) {
        $row['numa'] = date("His", $row['create_time']).$row['microtime'];
        $numa += date("His", $row['create_time']).$row['microtime'];
        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);

    }
    unset($row);    
    $totle = count($indiana)<=20?count($indiana):20;

    //开奖记录
    $lottery = pdo_fetch("SELECT ip.*, ic.numb, ic.periods, ic.wincode FROM " . tablename('sz_yi_indiana_period') . " ip 
        left join " . tablename('sz_yi_indiana_comcode') . " ic on (ip.id = ic.pid) 
     WHERE ip.uniacid = :uniacid and ip.period_num = :period_num order by ip.create_time desc limit 1", array(
            ':uniacid'      => $_W['uniacid'],
            ':period_num'   => $period_num
        ));
include $this->template('announced');

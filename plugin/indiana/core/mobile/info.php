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

$title = $operation == 'number'?"夺宝号码":"夺宝详情";

$indiana = pdo_fetch("select ir.*,ip.status, ip.goodsid, ip.code, ip.endtime, ig.title from" . tablename('sz_yi_indiana_record') . " ir 
    left join " . tablename('sz_yi_indiana_period') . "ip on( ir.period_num=ip.period_num ) 
    left join " . tablename('sz_yi_indiana_goods') . "ig on( ip.goodsid=ig.good_id and ig.status > 0 ) 
    where ir.uniacid = :uniacid and ir.openid = :openid and ir.period_num = :period_num order by ir.create_time desc  ",
    array(
        ':uniacid'      => $_W['uniacid'],
        ':openid'       => $openid,
        ':period_num'   => $period_num
    ));
    if ($indiana['status'] == '3') {
        $indiana['endtime'] = date("Y-m-d H:i:s", $indiana['endtime']);
    }

if ( $_W['isajax'] && $operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $list = pdo_fetchall("select * from" . tablename('sz_yi_indiana_consumerecord') . " where uniacid = :uniacid and openid = :openid and period_num = :period_num order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $openid,
            ':period_num' => $period_num
        ));
    foreach ($list as &$row) {
        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);
        $row['codes'] = unserialize($row['codes']);
    }
    unset($row);
    return show_json(1, array(
        'list' => $list,
        'pagesize' => $psize,
       
    ));
}

 

include $this->template('info');

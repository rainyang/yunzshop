<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));

$title = $operation == 'lucky'?"幸运记录":"夺宝记录";
// p('indiana')->createtime_winer('1','20161207833180275199','4');
// echo "<pre>";print_r(22);exit;

if ( $_W['isajax'] && $operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $condition = '';
    if ( $_GPC['status'] ) {
         $condition = " and ip.status = '".$_GPC['status']."'";
    }
    
    $list = pdo_fetchall("select ir.*,ip.status, ip.goodsid, ip.period, ip.nickname, ip.partakes, ip.shengyu_codes, zong_codes, canyurenshu from" . tablename('sz_yi_indiana_record') . " ir 
        left join " . tablename('sz_yi_indiana_period') . "ip on( ir.period_num=ip.period_num ) 
        where ir.uniacid = :uniacid and ir.openid = :openid  {$condition} order by ir.create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
        array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $openid
        ));
    foreach ($list as &$row) {
        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);
        $row['goods'] = set_medias(pdo_fetchall("select ig.title, g.thumb from " . tablename('sz_yi_goods') . " g 
            left join " . tablename('sz_yi_indiana_goods') . " ig on (g.id = ig.good_id) 
         where g.uniacid = :uniacid and g.id = :goodsid and ig.status > 0 ",array(
                ':uniacid' => $_W['uniacid'],
                ':goodsid' => $row['goodsid']
            )),'thumb');
        $row['shengyu'] = $row['shengyu_codes']/$row['zong_codes']*100;
        if ($row['status'] == 3) {
            //下一期
            $next = $row['period'] + 1;
            $row['next_phase'] = pdo_fetch("SELECT goodsid, period_num FROM " . tablename('sz_yi_indiana_period') . " where goodsid = '".$row['goodsid']."' and period = '" . $next . "'");

        }
    }
    unset($row);
    return show_json(1, array(
        //'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
       
    ));
} elseif ( $_W['isajax'] && $operation == 'lucky') {

    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $list = pdo_fetchall("select * from " . tablename('sz_yi_indiana_period') . " where uniacid = :uniacid and openid = :openid and status = :status order by endtime desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
        array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $openid,
            ':status' => 3
        ));
    foreach ($list as &$row) {
        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);
        $row['goods'] = set_medias(pdo_fetchall("select ig.title, g.thumb from " . tablename('sz_yi_goods') . " g 
            left join " . tablename('sz_yi_indiana_goods') . " ig on (g.id = ig.good_id) 
         where g.uniacid = :uniacid and g.id = :goodsid and ig.status > 0 ",array(
                ':uniacid' => $_W['uniacid'],
                ':goodsid' => $row['goodsid']
            )),'thumb');
            //下一期
            $next = $row['period'];
            $row['next_phase'] = pdo_fetch("SELECT goodsid, period_num FROM " . tablename('sz_yi_indiana_period') . " where goodsid = '".$row['goodsid']."' and period > '" . $next . "' ORDER BY period desc limit 1");

    }

    unset($row);
    return show_json(1, array(
        //'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
       
    ));
    return show_json(1,array());
}

 

include $this->template('order');

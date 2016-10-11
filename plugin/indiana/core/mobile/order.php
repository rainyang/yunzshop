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


if ( $_W['isajax'] && $operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $condition = '';
    if ( $_GPC['status'] ) {
         $condition = " and ip.status = '".$_GPC['status']."'";
    }
    
    $list = pdo_fetchall("select ir.*,ip.status, ip.goodsid, shengyu_codes, zong_codes from" . tablename('sz_yi_indiana_record') . " ir 
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
    }
    //set_medias(pdo_fetchall($sql), 'thumb');
//echo "<pre>";print_r($list);exit;
    unset($row);
    show_json(1, array(
        //'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
       
    ));
} elseif ( $_W['isajax'] && $operation == 'lucky') {
    show_json(1,array());
}

 

include $this->template('order');

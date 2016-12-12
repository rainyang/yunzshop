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

if ( $_W['isajax'] && $operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $condition = '';
    if ( $_GPC['status'] ) {
         $condition = " and o.status = '".$_GPC['status']."'";
    }
    $list = pdo_fetchall("select o.*, ip.period, ip.period_num from" . tablename('sz_yi_indiana_period') . " ip 
        left join " . tablename('sz_yi_indiana_record') . "ir on( ip.period_num=ir.period_num ) 
        left join " . tablename('sz_yi_order') . "o on( o.ordersn=ir.ordersn ) 
        where ip.uniacid = :uniacid and ip.openid = :openid {$condition} order by ip.jiexiao_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
        array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $openid
        ));
    foreach ($list as $k=>&$row) {

        switch ($row['status']) {
            case '-1':
                $status = '已取消';
                break;
            case "0":
                if ($row['paytype'] == 3) {
                    $status = '待发货';
                } else {
                    $status = '待付款';
                }
                break;
            case '1':
                if ($row['isverify'] == 1) {
                    $status = '待使用';
                } else if (empty($row['addressid'])) {
                    $status = '待取货';
                } else {
                    $status = '待发货';
                }
                break;
            case '2':
                $status = '待收货';
                break;
            case '3':
                $status = '交易完成';
                break;
        }
        $row['statusstr'] = $status;
        
        if (!empty($row['id'])) {
            $sql = 'SELECT og.goodsid,og.total,g.type,ig.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og 
            left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id 
            left join ' . tablename('sz_yi_indiana_goods') . ' ig on og.goodsid = ig.good_id 
            where og.orderid = '.$row['id'].'  order by og.id asc';
            $row['goods'] = set_medias(pdo_fetchall($sql), 'thumb');
        }
    }
    unset($row);
    show_json(1, array(
        //'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
    ));

}

 

include $this->template('order_list');

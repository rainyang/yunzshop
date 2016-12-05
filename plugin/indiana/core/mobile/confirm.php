<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
if(empty($openid)){
    $openid = m('user')->isLogin();
}
$uniacid    = $_W['uniacid'];
$period = intval($_GPC['period']);
$goodsid = intval($_GPC['id']);
$total = intval($_GPC['total']);
$initmoney = intval($_GPC['initmoney']);
//查询商品是否存在
$sql  = 'SELECT id as goodsid,costprice,supplier_uid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,goodssn,productsn,sales,istime,timestart,timeend,usermaxbuy,maxbuy,unit,buylevels,buygroups,deleted,status,deduct,manydeduct,virtual,discounts,discounts2,discountway,discounttype,deduct2,ednum,edmoney,edareas,diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,redprice, yunbi_deduct,bonusmoney FROM ' . tablename('sz_yi_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
$goods = pdo_fetch($sql, array(
    ':uniacid' => $uniacid,
    ':id' => $goodsid
));
if (empty($goods)) {
    show_json(-1, '未找到任何商品');
}

$indiana_period = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_period') . ' where uniacid=:uniacid and goodsid = :goodsid and period = :period and status = 1 ',array(
        ':uniacid'  => $uniacid,
        ':goodsid'  => $goodsid,
        ':period'   => $period
    ));
if ( $total > $indiana_period['shengyu_codes'] ) {
    show_json(-1, '剩余人次不足！');
}

$address      = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('sz_yi_member_address') . ' WHERE openid=:openid AND deleted=0 AND isdefault=1  AND uniacid=:uniacid limit 1', array(
    ':uniacid' => $uniacid,
    ':openid' => $openid
));
if (!$address) {
   show_json(-1, '请编辑默认收货地址！'); 
}
$ordersn    = m('common')->createNO('order', 'ordersn', 'SH');
//通用订单号，支付用
$ordersn_general    = m('common')->createNO('order', 'ordersn', 'SH');
$totalprice = $total * $initmoney;
//插入订单
$order   = array(
    'supplier_uid' => 0,
    'uniacid' => $uniacid,
    'openid' => $openid,
    'ordersn' => $ordersn,
    'ordersn_general' => $ordersn_general,
    'price' => $totalprice,
    'goodsprice' => $totalprice,
    'status' => 0,
    'paytype' => 0,
    'transid' => '',
    'remark' => '',
    'addressid' =>  $address['id'],
    'address' =>  iserializer($address),
    'createtime' => time(),
    'isverify' => 0,
    'carrier' => "a:0:{}",
    'isvirtual' => 0,
    'order_type' => 4,
    'period_num' => $indiana_period['period_num'],
    'oldprice' => $totalprice
);

pdo_insert('sz_yi_order',$order);
$orderid = pdo_insertid();
if ( $orderid ) {
    //插入订单商品
    $order_goods = array(
        'uniacid' => $uniacid,
        'orderid' => $orderid,
        'goodsid' => $goods['goodsid'],
        'price' => $totalprice,
        'total' => $total,
        'optionid' => 0,
        'createtime' => time(),
        'optionname' => '',
        'goodssn' => '',
        'productsn' => '',
        "realprice" => $totalprice,
        "oldprice" => $totalprice,
        "openid" => $openid,
        'goods_op_cost_price' => $goods['costprice']
    );
    pdo_insert('sz_yi_order_goods', $order_goods);
    
    $pluginc = p('commission');
    if ($pluginc) {
        $pluginc->checkOrderConfirm($orderid);
    }
    show_json(1, array(
        'orderid' => $orderid
    ));
}
show_json(0);

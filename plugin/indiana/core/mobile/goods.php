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
$member    = m('member')->getMember($openid);
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));

$pindex = max(1, intval($_GPC["page"]));
$psize = 12;
$condition = ' and ig.uniacid = :uniacid AND ig.status=2';
$params    = array(
    ':uniacid' => $_W['uniacid']
);
if ($_GPC["init_money"]) {
    $condition .= ' and ip.init_money = :init_money ';
    $params[':init_money']  = intval($_GPC["init_money"]);
}

$total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_indiana_goods') . " ig 
left join " . tablename('sz_yi_goods') . " g on (ig.good_id = g.id) 
left join " . tablename('sz_yi_indiana_period') . " ip on (ig.id = ip.ig_id) 
 where 1 {$condition} ", $params);

$goods = set_medias(pdo_fetchall("SELECT ig.*, g.thumb, ip.period, ip.shengyu_codes, ip.zong_codes, ip.period_num, ip.init_money as initmoney FROM " . tablename('sz_yi_indiana_goods') . " ig 
left join " . tablename('sz_yi_goods') . " g on (ig.good_id = g.id) 
left join " . tablename('sz_yi_indiana_period') . " ip on (ig.id = ip.ig_id)
 where 1 {$condition} AND ip.status = 1 LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params),'thumb');

foreach ($goods  as $key => &$value) {
   $value['shengyu'] = $value['shengyu_codes']/$value['zong_codes']*100;
}
unset($value);

$pager = pagination($total, $pindex, $psize);

$init_money = pdo_fetchall("SELECT init_money FROM " . tablename('sz_yi_indiana_period') . " where uniacid = :uniacid and status=1 group by init_money ",array(
     ':uniacid' => $_W['uniacid']
));
$_W['shopshare']['link'] = $this->createPluginMobileUrl('indiana/goods', array(
    'init_money' => $_GPC["init_money"],
    'mid' => $member['id']
));
if ($_W['isajax']) {
    return show_json(1, array(
        'goods' => $goods,
        'pagesize' => $psize,
    ));
}
include $this->template('goods');

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$commission = p('commission');
$shopset   = m('common')->getSysset('shop');
if ($operation == 'display') {
    $condition = ' and `uniacid` = :uniacid AND `deleted` = 0 and status=1';
    $params    = array(
         ':uniacid' => $_W['uniacid']
    );
    if (!empty($_GPC['tcate_area'])) {
        $condition .= " and tcate_area=:id";
        $params[':id'] = intval($_GPC['tcate_area']);
        $category = set_medias(pdo_fetch('select * from ' . tablename('sz_yi_category_area') . ' where id=:id 
            and uniacid=:uniacid order by displayorder DESC', $params),array('thumb', 'advimg'));
        
        $category['url'] = $this->createPluginMobileUrl('area/area_detail')."&tcate_area=";
    } else if (!empty($_GPC['ccate_area'])) {
        $condition .= " and ccate_area=:id";
        $params[':id'] = intval($_GPC['ccate_area']);
        $category = set_medias(pdo_fetch('select * from ' . tablename('sz_yi_category_area') . ' where id=:id 
            and uniacid=:uniacid order by displayorder DESC', $params),array('thumb', 'advimg'));
        
        $category['url'] = $this->createPluginMobileUrl('area/area_detail')."&ccate_area=";

    }
    if (!empty($_GPC['status'])) {
        if ($_GPC['status'] == 1) {
            $condition .= " ORDER BY createtime DESC";
        } else if ($_GPC['status'] == 2) {
            $condition .= " ORDER BY sales DESC";
        } else if ($_GPC['status'] == 3) {
            $condition .= " ORDER BY marketprice DESC";
        } else if ($_GPC['status'] == 4) {
            $condition .= " ORDER BY marketprice ASC";
        }
    }
    $goods = set_medias(pdo_fetchall("SELECT * FROM ".tablename('sz_yi_goods')." WHERE 1 {$condition} ", $params),'thumb');
    foreach ($goods as &$value) {
        if (!empty($value['pcate_area'])) {
            $pcate_name = pdo_fetchcolumn("SELECT name FROM ".tablename('sz_yi_category_area')." WHERE id=:id and uniacid=:uniacid", array(':id' => $value['pcate_area'], ':uniacid' => $_W['uniacid']));
            $value['pcate_name'] = $pcate_name;
        }
        if (!empty($value['ccate_area'])) {
            $ccate_name = pdo_fetchcolumn("SELECT name FROM ".tablename('sz_yi_category_area')." WHERE id=:id and uniacid=:uniacid", array(':id' => $value['ccate_area'], ':uniacid' => $_W['uniacid']));
            $value['ccate_name'] = $ccate_name;
        }
        if (!empty($value['tcate_area'])) {
            $tcate_name = pdo_fetchcolumn("SELECT name FROM ".tablename('sz_yi_category_area')." WHERE id=:id and uniacid=:uniacid", array(':id' => $value['tcate_area'], ':uniacid' => $_W['uniacid']));
            $value['tcate_name'] = $tcate_name;
        }
    }
    $total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('sz_yi_goods')." WHERE 1 {$condition} ", $params);
    $args['pagesize'] = 20;
    $pindex = max(1, intval($_GPC['page']));
    $pager = pagination($total, $pindex, $args['pagesize']);
}    
include $this->template('area/area');

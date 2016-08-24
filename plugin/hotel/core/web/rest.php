<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

ca('hotel.rest');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
load()->model('user');
if ($operation == 'display') {
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $condition = " and uniacid=:uniacid and type=:type";
    $params    = array(
        ':uniacid' => $_W['uniacid'],
        ':type' => '1',

    );
    $list  = pdo_fetchall("SELECT *  FROM " . tablename('sz_yi_book') . " WHERE 1 {$condition} ORDER BY create_time desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_book') . " WHERE 1 {$condition} ", $params);
    foreach ($list as $key => $value) {
        $sqlgoods = 'SELECT * FROM ' . tablename('sz_yi_goods') . ' WHERE `uniacid` = :uniacid and `id`=:id';
        $goods = pdo_fetch($sqlgoods, array(':uniacid' => $_W['uniacid'],':id' => $value['goods']));
        $list[$key]['goodtitle'] =  $goods['title'];
    }
        $status['0'] = '待确认';
        $status['1'] = '已取消';
        $status['2'] = '已确认';
    $pager = pagination($total, $pindex, $psize);
}elseif ($operation == 'do') {
    $id      = intval($_GPC['id']);
    $item    = pdo_fetch("SELECT * FROM " . tablename('sz_yi_book') . " WHERE id =:id and uniacid=:uniacid limit 1 ", array(
        ':id' => $id,
        ':uniacid' => $_W['uniacid']
    ));
    if($item){
         pdo_update("sz_yi_book", array(
          'status' =>2
            ) , array(
                "id" => $id 
            ));
       message('确认成功！', $this->createPluginWebUrl('hotel/rest', array(
        'op' => 'display'
        )), 'success');
    }
   

 
}
load()->func('tpl');
include $this->template('rest');
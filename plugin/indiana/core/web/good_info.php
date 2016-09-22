<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

ca('indiana.good_info');

$set = $this->getSet();
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == "display") {
    if ( empty($_GPC['gid']) ) {
        message('参数错误！',$this->createPluginWebUrl('indiana/goods'),'error');
    }
    $indiana_good = pdo_fetch('SELECT id FROM ' . tablename('sz_yi_indiana_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND good_id = '".$_GPC['gid']."'");
    if ( $indiana_good ) {
        message('商品已存在！',$this->createPluginWebUrl('indiana/goods'),'error');
    }
    $info = pdo_fetch('SELECT id, title, thumb FROM ' . tablename('sz_yi_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND id = '".$_GPC['gid']."'");

    $good['max_num'] = $good['max_num']?$good['max_num']:'0';

} elseif ($operation == "edit") {
    
    $good = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND id = '".$_GPC['id']."'");
    if (!$good) {
        message('夺宝商品已存在！',$this->createPluginWebUrl('indiana/goods'),'error');
    }
    $info = pdo_fetch('SELECT id, title, thumb FROM ' . tablename('sz_yi_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND id = '".$good['good_id']."'");

} elseif ($operation == "update") {

    $good = $_GPC['data'];

    if ($_GPC['id']) {
        $data = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND id = '".$_GPC['id']."'");
        if ($data['max_periods'] < $good['periods']) {
            $good['max_periods'] = $good['periods'];
        }

        pdo_update('sz_yi_indiana_goods', $good, array('id' => $_GPC['id'], 'uniacid' => $_W['uniacid']));
        p('indiana')->setPeriod($_GPC['id']); //设置本期数据
        message('保存成功!', $this->createPluginWebUrl('indiana/good_info',array('op'=>'edit','id'=>$_GPC['id'])), 'success');

    } else {
        $good['uniacid']             = $_W['uniacid'];
        $good['max_periods']         = 1;
        $good['participants_num']    = 0;
        $good['create_time']         = time();
        pdo_insert('sz_yi_indiana_goods',$good);
        $indianaid = pdo_insertid();
        //p('indiana')->setPeriod($_GPC['id']); //设置本期数据
        message('保存成功!', $this->createPluginWebUrl('indiana/good_info',array('op'=>'edit','id'=>$indianaid)), 'success');
    }


   
}


load()->func('tpl');
include $this->template('good_info');
exit;

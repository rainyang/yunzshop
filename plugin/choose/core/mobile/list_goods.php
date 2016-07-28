<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'moren';
$openid     = m('user')->getOpenid();
$uniacid    = $_W['uniacid'];
if($_W['isajax']){
    if (p('channel')) {
        $member = m('member')->getInfo($openid);
        $level  = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_level') . " WHERE uniacid={$_W['uniacid']} AND id={$member['channel_level']}");
        $ischannelpick = $_GPC['ischannelpick'];
    }
    $pageid = $_GPC['pageid'];
    $page   = pdo_fetch('select * from '.tablename('sz_yi_chooseagent'). ' where id=:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$pageid));
    if (!empty($page['isopenchannel'])) {
        $args = array(
            'isopenchannel' => $page['isopenchannel']
            );
        if (!empty($ischannelpick)) {
            $args = array(
            'isopenchannel' => $page['isopenchannel'],
            'ischannelpick' => $ischannelpick,
            'openid'        => $openid
            );
        }
    } else {
        if($page['isopen']!=0){
            $args=array(
            'pcate'         => $_GPC['pcate'],
            'ccate'         => $_GPC['ccate'],
            'tcate'         => $_GPC['tcate'],
            'supplier_uid'  => $page['uid']
            );
        }else{
            if($operation == 'moren'){
                    $args = array(
                    'pcate' => $_GPC['pcate']
                    ); 
            }else if($operation == 'second'){
                    $args=array(
                    'pcate' => $page['pcate'],
                    'ccate' => $_GPC['ccate']
                    );
            }else if ($operation == 'third') {
                $args = array(
                'tcate' => $_GPC['tcate']
                );
            }
        }
    }   
    $goods = m('goods')->getList($args);
    if (p('channel')) {
        foreach ($goods as $key => &$value) {
            if (empty($ischannelpick)) {
                $value['channel_price'] = number_format($value['marketprice'] * $level['purchase_discount']/100, 2);
                $value['channel_price'] = "/采购价：" . $value['channel_price'] . "元";
            } else {
                $value['channel_price'] = "";
            }
        }
        unset($value);
    }
    show_json(1,array('goods' => $goods));
}
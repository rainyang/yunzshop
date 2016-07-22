<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/3/22
 * Time: 下午12:09
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$uniacid   = $_W['uniacid'];
$action = $this->createMobileUrl('rest', array('op' => 'post'));
$display = $this->createMobileUrl('rest', array('op' => 'display'));
$list = $this->createMobileUrl('rest', array('op' => 'list'));
$edit = $this->createMobileUrl('rest', array('op' => 'edit'));
$delete = $this->createMobileUrl('rest', array('op' => 'delete'));

if ($operation == 'display') { 
   $sql = 'SELECT * FROM ' . tablename('sz_yi_goods') . ' WHERE `uniacid` = :uniacid and `type`=:type ORDER BY `id`';
   $rest = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'],':type' => '98'));
}else if($operation == 'post'){
    $data = array(
        'uniacid' => $_W['uniacid'],
        'uid' => $member['uid'],
        'mobile' => $_GPC['mobile'],
        'message' => $_GPC['message'],
        'contact' => $_GPC['contact'],
        'time' => $_GPC['time'],
        'goods' => $_GPC['goods'],
        'type' => 1,//餐厅
    );
    pdo_insert('sz_yi_book', $data);
    //商城信息
    $set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
    //商品信息
    $goods = pdo_fetch("select * from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$_GPC['goods']}");
    $data['title'] = $goods['title'];
    //打印机信息
    $print_detail = pdo_fetch("select * from " . tablename('sz_yi_print_list') . " where uniacid={$_W['uniacid']} and id={$goods['print_id']}");
    if(!empty($print_detail)){
       $member_code = $print_detail['member_code'];
       $device_no = $print_detail['print_no'];
       $key = $print_detail['key'];
       include IA_ROOT.'/addons/sz_yi/core/model/print.php';       
       $msgNo = testSendFreeMessagerest($data, $member_code, $device_no, $key,$set);
    } 
}else if($operation == 'list'){
    $sql = 'SELECT * FROM ' . tablename('sz_yi_book') . ' WHERE `uniacid` = :uniacid and `type`=:type and `uid`=:uid ORDER BY `id` desc';
    $myrest = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'],':type' => '1',':uid' =>  $member['uid']));
    foreach ($myrest as $key => $value) {
        $sqlgoods = 'SELECT * FROM ' . tablename('sz_yi_goods') . ' WHERE `uniacid` = :uniacid and `id`=:id';
        $goods = pdo_fetch($sqlgoods, array(':uniacid' => $_W['uniacid'],':id' => $value['goods']));
        $myrest[$key]['goodtitle'] =  $goods['title'];
    }
        $status['0'] = '待确认';
        $status['1'] = '已取消';
        $status['2'] = '已确认';

}else if($operation == 'edit') {
    $id = $_GPC['id'];
    $status = 1;
    pdo_update("sz_yi_book", array(
        'status' => $status
    ) , array(
        "id" => $id 
    ));
   $url =  $this->createMobileUrl('rest', array('op' => 'list',));
   header("location:". $url);
}else if($operation == 'delete') {
    $id = $_GPC['id'];
    $delete = 1;
    pdo_update("sz_yi_book", array(
        'delete' => $delete
    ) , array(
        "id" => $id 
    ));
   $url =  $this->createMobileUrl('rest', array('op' => 'list',));
   header("location:". $url);
}


include $this->template('rest/index');
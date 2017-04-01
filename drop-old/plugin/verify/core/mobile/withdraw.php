<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$page = 'withdraw';
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
$id=$_GPC['storeid']? $_GPC['storeid'] : '0';
$store = $this->model->getInfo($id);
// 累计支付金额
$totalprices = $this->model->getTotalPrice($id);
// 已经提现的金额
$totalwithdraws = $this->model->getWithdrawed($id);
//扣除平台提成的金额
$totalwithdrawprice = $this->model->getRealPrice($id);;

//未提现金额
$totalprices = $totalwithdrawprice - $totalwithdraws;
$totalpricess = number_format($totalprices,'2');
//echo  $totalpricess;exit;


if ($operation == 'display' && $_W['isajax']) {
    $store['totalprices'] = $totalpricess;
    return show_json(1, array(
        'store'  => $store,
        'noinfo' => empty($member['realname'])
    ));
} else if ($operation == 'submit' && $_W['ispost']) {
    $money = floatval($_GPC['money']);
    
    if (empty($money)) {
        return show_json(0, '申请金额为空!');
    }
    if ($money <= 0) {
        return show_json(0, '提现金额不能小于0元!');
    }
    if ($money > $totalprices) {
        return show_json(0, '提现金额过大!');
    }
   
    $withdraw_no = m('common')->createNO('store_withdraw', 'withdraw_no', 'SW');
    $data = array(
        'uniacid'           => $_W['uniacid'],
        'withdraw_no'       => $withdraw_no,
        'openid'            => $openid,
        'store_id'          => $id,
        'money'             => $money,
        'status'            => 0
    );
    pdo_insert('sz_yi_store_withdraw', $data);
    $message = array(
                            'keyword1' => array('value' => '门店提现成功通知', 'color' => '#73a68d'),
                            'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                            'remark' => array('value' => '恭喜,您的提现申请已经成功提交!')
                        );          
    m('message')->sendCustomNotice($openid, $message);
    return show_json(1);
}

include $this->template('withdraw');

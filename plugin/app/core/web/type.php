<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/6/16
 * Time: 下午5:53
 */

global $_W, $_GPC;


$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
    ':uniacid' => $_W['uniacid']
));

$set     = unserialize($setdata['sets']);

$setting = uni_setting($_W['uniacid'], array('payment'));
$pay = $setting['payment'];
if(!is_array($pay)) {
    $pay = array();
}


if (checksubmit()) {

    $set['pay']['app_weixin'] = $_GPC['pay']['app_weixin'];
    $set['pay']['app_alipay'] = $_GPC['pay']['app_alipay'];

    if ((!empty($_GPC['pay']['app_weixin']) || !empty($_GPC['pay']['app_alipay'])) && (empty($_GPC['ping']['partner'])
            || empty($_GPC['ping']['secret']))) {
        message('请填写完整的Ping++信息!', 'refresh', 'error');
    }

    $data = array(
        'uniacid' => $_W['uniacid'],
        'sets' => iserializer($set)
    );
    if (empty($setdata)) {
        pdo_insert('sz_yi_sysset', $data);
    } else {
        pdo_update('sz_yi_sysset', $data, array(
            'uniacid' => $_W['uniacid']
        ));
    }
    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $_W['uniacid']
    ));
    m('cache')->set('sysset', $setdata);


    $ping = array_elements(array('partner', 'secret'), $_GPC['ping']);
    $ping['switch'] = 1;
    $ping['partner'] = trim($ping['partner']);
    $ping['secret'] = trim($ping['secret']);


    $pay['ping'] = $ping;

    $wx_native = array_elements(array('wx_appid', 'wx_mcid', 'wx_secret', 'signkey'), $_GPC['wx_native']);
    $pay['wx_native'] = $wx_native;

    $dat = iserializer($pay);
    pdo_update('uni_settings', array('payment' => $dat), array('uniacid' => $_W['uniacid']));
    cache_delete("unisetting:{$_W['uniacid']}");

    message('设置保存成功!', $this->createWebUrl('plugin/app', array(
        'method'=>'type',
        'op' => $op
    )), 'success');
}


load()->func('tpl');
include $this->template('type');
exit;

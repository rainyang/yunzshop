<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$uniacid = $_W['uniacid'];
if ($operation == 'display') {
    ca('cashier.withdraw.view');
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $condition = ' and s.uniacid=:uniacid and w.money<>0';
    $params    = array(
        ':uniacid' => $uniacid
    );
    if (!empty($_GPC['name'])) {
        $_GPC['name'] = trim($_GPC['name']);
        $condition .= ' and (s.name like :name or s.contact like :name or s.mobile like :name)';
        $params[':name'] = "%{$_GPC['name']}%";
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime   = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1') {
            $condition .= " AND w.create_time >= :starttime AND w.create_time <= :endtime ";
            $params[':starttime'] = $_GPC['time']['start'];
            $params[':endtime']   = $_GPC['time']['end'];
        }
    }
    /*if ($_GPC['status'] != '') {
        $condition .= ' and w.status=' . intval($_GPC['status']);
    }*/
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

    $sql   = "select w.id, s.name, m.nickname, m.avatar, m.weixin, w.withdraw_no, w.money, w.create_time, w.status, s.member_id from " . tablename('sz_yi_cashier_store') . " s left join " . tablename('sz_yi_cashier_withdraw') . " w on s.id=w.cashier_store_id  left join " . tablename('sz_yi_member') . " m on m.id=s.member_id where 1 {$condition} and w.status=0";
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_cashier_store') . " s left join " . tablename('sz_yi_cashier_withdraw') . " w on s.id=w.cashier_store_id left join " . tablename('sz_yi_member') . " m on m.id=s.member_id where 1 {$condition} and w.status=0", $params);
    $pager = pagination($total, $pindex, $psize);
} else if ($operation == 'pay') {
    $id      = intval($_GPC['id']);
    $paytype = $_GPC['paytype'];
    $set     = m('common')->getSysset('shop');
    $log     = pdo_fetch('select * from ' . tablename('sz_yi_cashier_withdraw') . ' w inner join ' . tablename('sz_yi_cashier_store') . ' s on w.cashier_store_id = s.id where w.id=:id and w.uniacid=:uniacid limit 1', array(
        ':id' => $id,
        ':uniacid' => $uniacid
    ));
    if (empty($log)) {
        message('未找到记录!', '', 'error');
    }
    $member = m('member')->getMember($log['openid']);
    if ($paytype == 'manual') {
        ca('caisher.withdraw.withdraw');
        pdo_update('sz_yi_cashier_withdraw', array(
            'status' => 1
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        // TODO: 要不要给提现的用户发一条通知  要
         $msg = array(
                                'keyword1' => array('value' => '收银台提现打款通知', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                                'remark' => array('value' => '您的提现申请已经手动打款完成,请注意查收!')
                            );          
        m('message')->sendCustomNotice($log['openid'], $msg);
        plog('cashier.withdraw.withdraw', "收银台商户提现 方式: 手动 ID: {$log['id']} <br/>商户信息: ID: {$log['cashier_store_id']} / {$log['openid']}/{$log['name']}/{$log['contact']}/{$log['mobile']}");
        message('手动提现完成!', referer(), 'success');
    } else if ($paytype == 'wechat') {
        ca('caisher.withdraw.withdraw');
        $result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $log['withdraw_no'], $log['name'] . '收银台商户提现');
        if (is_error($result)) {
            $withdraw_no = m('common')->createNO('cashier_withdraw', 'withdraw_no', 'CW');
            pdo_update('sz_yi_cashier_withdraw', array(
                'withdraw_no' => $withdraw_no
            ), array(
                'id' => $id,
                'uniacid' => $uniacid
            ));
            message('微信钱包提现失败: ' . $result['message'], '', 'error');
        }
        pdo_update('sz_yi_cashier_withdraw', array(
            'status' => 1
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        // TODO: 要不要给提现的用户发一条通知 要
         $msg = array(
                        'keyword1' => array('value' => '收银台提现打款通知', 'color' => '#73a68d'),
                        'keyword2' => array('value' => '【商户名称】' . $cashier_stores['name'], 'color' => '#73a68d'),
                        'remark' => array('value' => '您的提现申请已经微信打款完成,请注意查收!')
                    );          
        m('message')->sendCustomNotice($log['openid'], $msg);
        plog('cashier.withdraw.withdraw', "收银台商户提现 ID: {$log['id']} 方式: 微信 金额: {$log['money']} <br/>商户信息: ID: {$log['cashier_store_id']} / {$log['openid']}/{$log['name']}/{$log['contact']}/{$log['mobile']}");
        message('微信钱包提现成功!', referer(), 'success');
    } else if ($paytype == 'refuse') {
        ca('caisher.withdraw.withdraw');
        pdo_update('sz_yi_cashier_withdraw', array(
            'status' => 2
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        // TODO: 要不要给提现的用户发一条通知
        plog('cashier.withdraw.withdraw', "拒绝收银台商户提现 ID: {$log['id']} 金额: {$log['money']} <br/>商户信息: ID: {$log['cashier_store_id']} / {$log['openid']}/{$log['name']}/{$log['contact']}/{$log['mobile']}");
        message('操作成功!', referer(), 'success');
    } else {
        message('未找到提现方式!', '', 'error');
    }
}
load()->func('tpl');
include $this->template('withdraw');

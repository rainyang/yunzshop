<?php
/*=============================================================================
#     FileName: log.php
#         Desc: 日志
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:36:13
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$op      = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups  = m('member')->getGroups();
$levels  = m('member')->getLevels();
$uniacid = $_W['uniacid'];
$template_flag = 0;
if ($op == 'display') {
    $diyform_plugin = p('diyform');
    if ($diyform_plugin) {
        $set_config        = $diyform_plugin->getSet();
        $user_diyform_open = $set_config['user_diyform_open'];
        if ($user_diyform_open == 1) {
            $template_flag = 1;
            $diyform_id    = $set_config['user_diyform'];
            if (!empty($diyform_id)) {
                $formInfo     = $diyform_plugin->getDiyformInfo($diyform_id);
                $fields       = $formInfo['fields'];
                $diyform_data = iunserializer($member['diymemberdata']);
                $f_data       = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
            }
        }
    }

    if($fields){

        foreach ($fields as $k => $key) {
            if ( explode($key['tp_name'], '身份证号') > 1  || explode($key['tp_name'], '城市') > 1 || explode($key['tp_name'], '地址') > 1  || explode($key['tp_name'], '区域') > 1  || explode($key['tp_name'], '位置') > 1 ) {
                $field[] = array('title' => $key['tp_name'] , 'field' => $k , 'width' => 24);
            } else {
                $field[] = array('title' => $key['tp_name'] , 'field' => $k , 'width' => 12);
            }

            
        }
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $type   = intval($_GPC['type']);
    if ($type == 1) {
        ca('finance.withdraw.view');
    } else {
        ca('finance.recharge.view');
    }
    $condition = ' and log.uniacid=:uniacid and log.type=:type and log.money<>0';
    $params    = array(
        ':uniacid' => $_W['uniacid'],
        ':type' => $type
    );
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and (m.realname LIKE :realname or m.nickname LIKE :realname or m.mobile LIKE :realname or m.membermobile LIKE :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }
    if (!empty($_GPC['logno'])) {
        $_GPC['logno'] = trim($_GPC['logno']);
        $condition .= ' and log.logno like :logno';
        $params[':logno'] = "%{$_GPC['logno']}%";
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime   = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1') {
            $condition .= " AND log.createtime >= :starttime AND log.createtime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime']   = $endtime;
        }
    }
    if (!empty($_GPC['level'])) {
        $condition .= ' and m.level=' . intval($_GPC['level']);
    }
    if (!empty($_GPC['groupid'])) {
        $condition .= ' and m.groupid=' . intval($_GPC['groupid']);
    }
    if (!empty($_GPC['rechargetype'])) {
        $_GPC['rechargetype'] = trim($_GPC['rechargetype']);
        $condition .= " AND log.rechargetype=:rechargetype";
        if ($_GPC['rechargetype'] == 'system1') {
            $_GPC['rechargetype'] = 'system';
            $condition .= " and log.money<0";
        }
        $params[':rechargetype'] = trim($_GPC['rechargetype']);
    }
    if ($_GPC['status'] != '') {
        $condition .= ' and log.status=' . intval($_GPC['status']);
    }

    //搜索充值内容
    if ($_GPC['paymethod'] !="") {
        $condition .= ' and log.paymethod=' . intval($_GPC['paymethod']);
    }

    $sql = "select log.id,log.aging_id,m.id as mid, m.realname,m.diymemberdata,m.avatar,m.weixin,log.logno,log.type,log.status,log.rechargetype,m.nickname,m.mobile,g.groupname,log.money,log.poundage,log.createtime,l.levelname from " . tablename('sz_yi_member_log') . " log " . " left join " . tablename('sz_yi_member') . " m on m.openid=log.openid and m.uniacid = log.uniacid " . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where 1 {$condition} ORDER BY log.createtime DESC ";
    if (empty($_GPC['export'])) {
        $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    } 
    $list = pdo_fetchall($sql, $params);
    if(p('love')){
        foreach ($list as $key => &$value) {
            $value['aging'] = pdo_fetch("select * from " . tablename('sz_yi_member_aging_rechange') . " where id=".$value['aging_id']);
        }
        unset($value);
    }
    if ($_GPC['export'] == 1) {
        if ($_GPC['type'] == 1) {
            ca('finance.withdraw.export');
            plog('finance.withdraw.export', '导出提现记录');
        } else {
            ca('finance.recharge.export');
            plog('finance.recharge.export', '导出充值记录');
        }
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            $row['groupname']  = empty($row['groupname']) ? '无分组' : $row['groupname'];
            $row['levelname']  = empty($row['levelname']) ? '普通会员' : $row['levelname'];
            if ($row['status'] == 0) {
                if ($row['type'] == 0) {
                    $row['status'] = "未充值";
                } else {
                    $row['status'] = "申请中";
                }
            } else if ($row['status'] == 1) {
                if ($row['type'] == 0) {
                    $row['status'] = "充值成功";
                } else {
                    $row['status'] = "完成";
                }
            } else if ($row['status'] == -1) {
                if ($row['type'] == 0) {
                    $row['status'] = "";
                } else {
                    $row['status'] = "失败";
                }
            }
            //自定义表单信息
            if($row['diymemberdata']){

                $row['diymemberdata'] = iunserializer($row['diymemberdata']);
                foreach ($row['diymemberdata'] as $key => $value) {
                    
                    if($key == 'diyshenfenzheng'){
                        $row[$key] = "'".$value."'";
                    }else if(is_array($value)){
                        $row[$key] = "'";

                        foreach ($value as $k => $v) {
                            $row[$key] .= $v;
                        }
                    }else{
                        $row[$key] = $value;
                    }
                }
            }
            if ($row['rechargetype'] == 'system') {
                $row['rechargetype'] = "后台";
            } else if ($row['rechargetype'] == 'wechat') {
                $row['rechargetype'] = "微信";
            } else if ($row['rechargetype'] == 'alipay') {
                $row['rechargetype'] = "支付宝";
            }
        }
        unset($row);
        $columns = array(
            array(
                'title' => '昵称',
                'field' => 'nickname',
                'width' => 12
            ),
            array(
                'title' => '姓名',
                'field' => 'realname',
                'width' => 12
            ),
            array(
                'title' => '手机号',
                'field' => 'mobile',
                'width' => 12
            ),
            array(
                'title' => '会员等级',
                'field' => 'levelname',
                'width' => 12
            ),
            array(
                'title' => '会员分组',
                'field' => 'groupname',
                'width' => 12
            ),
            array(
                'title' => (empty($type) ? "充值金额" : "提现金额"),
                'field' => 'money',
                'width' => 12
            ),
            array(
                'title' => (empty($type) ? "充值时间" : "提现申请时间"),
                'field' => 'createtime',
                'width' => 12
            )
        );
        if ($field) {
            $columns = array_merge($columns,$field);
        }
        if (empty($_GPC['type'])) {
            $columns[] = array(
                'title' => "充值方式",
                'field' => 'rechargetype',
                'width' => 12
            );
        }//echo "<pre>"; print_r($list);exit;
        m('excel')->export($list, array(
            "title" => (empty($type) ? "会员充值数据-" : "会员提现记录") . date('Y-m-d-H-i', time()),
            "columns" => $columns
        ));

    }
    $set           = m('common')->getSysset(array(
        'shop',
        'pay'
    ));
    $total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member_log') . " log " . " left join " . tablename('sz_yi_member') . " m on m.openid=log.openid and m.uniacid= log.uniacid" . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where 1 {$condition} ", $params);
    $pager = pagination($total, $pindex, $psize);
} else if ($op == 'pay') {
    $id      = intval($_GPC['id']);
    $paytype = $_GPC['paytype'];
    $set     = m('common')->getSysset('shop');
    $log     = pdo_fetch('select * from ' . tablename('sz_yi_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(
        ':id' => $id,
        ':uniacid' => $uniacid
    ));
    if (empty($log)) {
        message('未找到记录!', '', 'error');
    }
    $set           = m('common')->getSysset(array(
        'shop',
        'pay'
    ));
    $member = m('member')->getMember($log['openid']);
    if ($paytype == 'manual') {
        ca('finance.withdraw.withdraw');
        pdo_update('sz_yi_member_log', array(
            'status' => 1
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        m('notice')->sendMemberLogMessage($logid);
        plog('finance.withdraw.withdraw', "余额提现 方式: 手动 ID: {$log['id']} <br/>会员信息: ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('手动提现完成!', referer(), 'success');
    } else if ($paytype == 'wechat') {
        ca('finance.withdraw.withdraw');
        if($set['pay']['weixin']!='1'){
            message('您未开启微信支付功能!', '', 'error');
        }    
        $result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $log['logno'], $set['name'] . '余额提现');
        if (is_error($result)) {
            message('微信钱包提现失败: ' . $result['message'], '', 'error');
        }
        pdo_update('sz_yi_member_log', array(
            'status' => 1
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        m('notice')->sendMemberLogMessage($log['id']);
        plog('finance.withdraw.withdraw', "余额提现 ID: {$log['id']} 方式: 微信 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('微信钱包提现成功!', referer(), 'success');
    }else if ($paytype == 'alipay') {
        ca('finance.withdraw.withdraw');
        $member = m('member')->getInfo($log['openid']);
        if($set['pay']['alipay']!='1'){
            message('您未开启支付宝支付功能!', '', 'error');
        }
        if( $set['pay']['alipay_withdrawals']!='1'){
            message('您未开启支付宝提现功能!', '', 'error');
        }

        if(empty($member['alipay']) || empty($member['alipayname'])){
            message('该用户未填写完整的收款支付宝账号或姓名!', '', 'error');
        }     
        $result = m('finance')->alipay_finance($log['money'],$member['alipay'],$member['alipayname'],$log['id']);
       
        m('notice')->sendMemberLogMessage($log['id']);
        plog('finance.withdraw.withdraw', "余额提现 ID: {$log['id']} 方式: 支付宝 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('支付宝提现成功!', referer(), 'success');
    }else if ($paytype == 'refuse') {
        ca('finance.withdraw.withdraw');
        pdo_update('sz_yi_member_log', array(
            'status' => -1
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        $money = $log['poundage'] > 0 ? $log['money']+$log['poundage'] : $log['money'];
        m('member')->setCredit($log['openid'], 'credit2', $money, array(
            0,
            $set['name'] . '余额提现退回'
        ));
        m('notice')->sendMemberLogMessage($log['id']);
        plog('finance.withdraw.withdraw', "拒绝余额度提现 ID: {$log['id']} 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('操作成功!', referer(), 'success');
    } else if ($paytype == 'refund') {
        ca('finance.recharge.refund');
        if (!empty($log['type'])) {
            message('充值退款失败: 非充值记录!', '', 'error');
        }
        if ($log['rechargetype'] == 'system') {
            message('充值退款失败: 后台充值无法退款!', '', 'error');
        }
        $current_credit = m('member')->getCredit($log['openid'], 'credit2');
        if ($log['money'] > $current_credit) {
            message('充值退款失败: 会员账户余额不足，无法进行退款!', '', 'error');
        }
        $out_refund_no = 'RR' . substr($log['logno'], 2);
        if ($log['rechargetype'] == 'wechat') {
            $result = m('finance')->refund($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100);
        } else {
            $result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $out_refund_no, $set['name'] . '充值退款');
        }
        if (is_error($result)) {
            message('充值退款失败: ' . $result['message'], '', 'error');
        }
        pdo_update('sz_yi_member_log', array(
            'status' => 3
        ), array(
            'id' => $id,
            'uniacid' => $uniacid
        ));
        m('member')->setCredit($log['openid'], 'credit2', -$log['money'], array(
            0,
            $set['name'] . '充值退款'
        ));
        m('notice')->sendMemberLogMessage($log['id']);
        plog('finance.withdraw.withdraw', "充值退款 ID: {$log['id']} 金额: {$log['money']} <br/>会员信息:  ID: {$member['id']} / {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('充值退款成功!', referer(), 'success');
    } else {
        message('未找到提现方式!', '', 'error');
    }
}
load()->func('tpl');
include $this->template('web/finance/log');

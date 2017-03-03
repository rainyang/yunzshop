<?php
/*=============================================================================
#     FileName: ag.php
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
    $condition = ' and ag.uniacid=:uniacid and ag.num<>0';
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and (m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }

    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime   = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1') {
            $condition .= " AND ag.createtime >= :starttime AND ag.createtime <= :endtime ";
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
        $condition .= " AND ag.rechargetype=:rechargetype";
        if ($_GPC['rechargetype'] == 'system1') {
            $_GPC['rechargetype'] = 'system';
            $condition .= " and ag.money<0";
        }
        $params[':rechargetype'] = trim($_GPC['rechargetype']);
    }
    if ($_GPC['status'] != '') {
        $condition .= ' and ag.status=' . intval($_GPC['status']);
    }

    //搜索充值内容
    if ($_GPC['paymethod'] !="") {
        $condition .= ' and ag.paymethod=' . intval($_GPC['paymethod']);
    }

    $sql = "select ag.id,ag.qnum,ag.phase,ag.paymethod,m.id as mid, m.realname,m.diymemberdata,m.avatar,m.weixin,ag.status,m.nickname,m.mobile,g.groupname,ag.num,ag.createtime,l.levelname from " . tablename('sz_yi_member_aging_rechange') . " ag " . " left join " . tablename('sz_yi_member') . " m on m.openid=ag.openid" . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where 1 {$condition} ORDER BY ag.createtime DESC ";
    $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, $params);
    
    $total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member_aging_rechange') . " ag " . " left join " . tablename('sz_yi_member') . " m on m.openid=ag.openid" . " left join " . tablename('sz_yi_member_group') . " g on m.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on m.level =l.id" . " where 1 {$condition} ", $params);
    $pager = pagination($total, $pindex, $psize);
} else if ($op == 'delete') {
    $id      = intval($_GPC['id']);
    pdo_delete('sz_yi_member_aging_rechange', array(
        'id' => $_GPC['id'],
        'uniacid' => $_W['uniacid']
    ));
    message('删除成功！', $this->createWebUrl('finance/aging_log'), 'success');
}
load()->func('tpl');
include $this->template('web/finance/aging_log');

<?php

global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$channellevels = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_channel_level') . ' WHERE uniacid = :uniacid',array(':uniacid' => $_W['uniacid']));
//print_r($channellevels);exit;
if ($operation == 'display') {
    ca('channel.channel.view');
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $params    = array();
    $condition = '';
    if (!empty($_GPC['mid'])) {
        $condition .= ' and dm.id=:mid';
        $params[':mid'] = intval($_GPC['mid']);
    }
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname)';
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
            $condition .= " AND dm.channeltime >= :starttime AND dm.channeltime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime']   = $endtime;
        }
    }
    if (!empty($_GPC['channel_level'])) {
        $condition .= ' and dm.channel_level=' . intval($_GPC['channel_level']);
    }
    $sql = "select dm.*,dm.nickname,dm.avatar,l.level_name,p.nickname as parentname,p.avatar as parentavatar from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.channel_id " . " left join " . tablename('sz_yi_channel_level') . " l on l.id = dm.channel_level" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid and f.uniacid={$_W['uniacid']}" . " where dm.uniacid = " . $_W['uniacid'] . " and dm.ischannel =1  {$condition} ORDER BY dm.channeltime desc";
    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(dm.id) from" . tablename('sz_yi_member') . " dm  " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.channel_id " . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid" . " where dm.uniacid =" . $_W['uniacid'] . " and dm.ischannel =1 {$condition}", $params);
    
    
} 
load()->func('tpl');
include $this->template('channel');


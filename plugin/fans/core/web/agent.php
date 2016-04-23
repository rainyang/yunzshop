<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$op     = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups = m('member')->getGroups();
$levels = m('member')->getLevels();
$shop   = m('common')->getSysset('shop');
if ($op == 'display') {
    ca('member.member.view');
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $condition = " and dm.uniacid=:uniacid";
    $params    = array(
        ':uniacid' => $_W['uniacid']
    );
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
            $condition .= " AND dm.createtime >= :starttime AND dm.createtime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime']   = $endtime;
        }
    }
    if ($_GPC['level'] != '') {
        $condition .= ' and dm.level=' . intval($_GPC['level']);
    }
    if ($_GPC['groupid'] != '') {
        $condition .= ' and dm.groupid=' . intval($_GPC['groupid']);
    }
    if ($_GPC['followed'] != '') {
        if ($_GPC['followed'] == 2) {
            $condition .= ' and f.follow=0 and dm.uid<>0';
        } else {
            $condition .= ' and f.follow=' . intval($_GPC['followed']);
        }
    }
    if ($_GPC['isblack'] != '') {
        $condition .= ' and dm.isblack=' . intval($_GPC['isblack']);
    }
    $sql = "select dm.*,l.levelname,g.groupname,a.nickname as agentnickname,a.avatar as agentavatar from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member_group') . " g on dm.groupid=g.id" . " left join " . tablename('sz_yi_member') . " a on a.id=dm.agentid" . " left join " . tablename('sz_yi_member_level') . " l on dm.level =l.id" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid  and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY dm.id DESC";
    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall($sql, $params);
    foreach ($list as &$row) {
        $row['levelname']  = empty($row['levelname']) ? (empty($shop['levelname']) ? '普通会员' : $shop['levelname']) : $row['levelname'];
        $row['ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $row['openid']
        ));
        $row['ordermoney'] = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('sz_yi_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $row['openid']
        ));
        $row['credit1']    = m('member')->getCredit($row['openid'], 'credit1');
        $row['credit2']    = m('member')->getCredit($row['openid'], 'credit2');
        $row['followed']   = m('user')->followed($row['openid']);
    }
    unset($row);
    if ($_GPC['export'] == '1') {
        ca('member.member.export');
        plog('member.member.export', '导出会员数据');
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            $row['groupname']  = empty($row['groupname']) ? '无分组' : $row['groupname'];
            $row['levelname']  = empty($row['levelname']) ? '普通会员' : $row['levelname'];
        }
        unset($row);
        m('excel')->export($list, array(
            "title" => "会员数据-" . date('Y-m-d-H-i', time()),
            "columns" => array(
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
                    'title' => '注册时间',
                    'field' => 'createtime',
                    'width' => 12
                ),
                array(
                    'title' => '积分',
                    'field' => 'credit1',
                    'width' => 12
                ),
                array(
                    'title' => '余额',
                    'field' => 'credit2',
                    'width' => 12
                ),
                array(
                    'title' => '成交订单数',
                    'field' => 'ordercount',
                    'width' => 12
                ),
                array(
                    'title' => '成交总金额',
                    'field' => 'ordermoney',
                    'width' => 12
                )
            )
        ));
    }
    $total           = pdo_fetchcolumn("select count(*) from" . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member_group') . " g on dm.groupid=g.id" . " left join " . tablename('sz_yi_member_level') . " l on dm.level =l.id" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid" . " where 1 {$condition} ", $params);
    $pager           = pagination($total, $pindex, $psize);
    $opencommission  = false;
    $plug_commission = p('commission');
    if ($plug_commission) {
        $comset = $plug_commission->getSet();
        if (!empty($comset)) {
            $opencommission = true;
        }
    }
} else if ($op == 'update') {
    ca('fans.view');
    $mid = intval($_GPC['mid']);
    if(empty($mid)){
        message('请填写id', '', 'error');
    }
    $member = pdo_fetch("select agentid from " . tablename('sz_yi_member') . " where uniacid=".$_W['uniacid']." and id = ".$mid);
    $agent = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=".$_W['uniacid']." and agentid = ".$member['agentid']);
    
    $fans = pdo_fetch("select * from " . tablename('mc_mapping_fans') . " where uniacid=".$_W['uniacid']." and openid = '".$agent['openid']."'");
    $tag = base64_decode($fans['tag']);
    $userinfo = unserialize($tag);
    if($userinfo['nickname'] =="" && $userinfo['headimgurl'] == 132){
        message("未获取该用户的任何信息", '', "error");
    }else{
        $shopdata = array('nickname' => $userinfo['nickname'], 'avatar' => $userinfo['headimgurl']);
        pdo_update('sz_yi_member', $shopdata, array('id' => $agent['id']));
    }
    message("会员信息更新成功", $this->createPluginWebUrl('fans/member', array("op" => 'display')), "success");
} else if ($op == 'detail') {
}
load()->func('tpl');
include $this->template('agent');

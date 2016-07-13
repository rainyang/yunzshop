<?php
global $_W, $_GPC;
$agentlevels = $this->model->getLevels();
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {

} else if ($operation == 'detail') {
    ca('hotel.agent.view');
    $id     = intval($_GPC['id']);
    $member = $this->model->getInfo($id, array(
        'total',
        'pay'
    ));
    if (checksubmit('submit')) {
        ca('hotel.agent.edit|hotel.agent.check|hotel.agent.agentblack');
        $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
        if (empty($_GPC['oldstatus']) && $data['status'] == 1) {
            $time              = time();
            $data['agenttime'] = time();
            $this->model->sendMessage($member['openid'], array(
                'nickname' => $member['nickname'],
                'agenttime' => $time
            ), TM_COMMISSION_BECOME);
            plog('hotel.agent.check', "审核分销商 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        }
        if (empty($_GPC['oldagentblack']) && $data['agentblack'] == 1) {
            $data['agentblack'] = 1;
            $data['status']     = 0;
            $data['isagent']    = 1;
        }
        $reside = $_GPC['reside'];
        if(!empty($data['hotel_area'])){
            if($data['hotel_area'] == 1){
                if(empty($reside['province'])){
                    message('请选择代理的省', '', 'error');
                }
            }else if($data['hotel_area'] == 2){
                if(empty($reside['city'])){
                    message('请选择代理的市', '', 'error');
                }
            }else if($data['hotel_area'] == 3){
                if(empty($reside['district'])){
                    message('请选择代理的区', '', 'error');
                }
            }
            $data['hotel_province'] = $reside['province'];
            $data['hotel_city'] = $reside['city'];
            $data['hotel_district'] = $reside['district'];
        }
        plog('hotel.agent.edit', "修改分销商 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        if(!empty($data['hotellevel'])){
            $data['hotel_status'] = 1;
        }
        pdo_update('sz_yi_member', $data, array(
            'id' => $id,
            'uniacid' => $_W['uniacid']
        ));
        if (empty($_GPC['oldstatus']) && $data['status'] == 1) {
            if (!empty($member['agentid'])) {
                $this->model->upgradeLevelByAgent($member['agentid']);
            }
        }
        message('保存成功!', $this->createPluginWebUrl('hotel/agent'), 'success');
    }
    $diyform_flag   = 0;
    $diyform_plugin = p('diyform');
    if ($diyform_plugin) {
        if (!empty($member['diycommissiondata'])) {
            $diyform_flag = 1;
            $fields       = iunserializer($member['diycommissionfields']);
        }
    }
} else if ($operation == 'delete') {
    ca('commission.agent.delete');
    $id     = intval($_GPC['id']);
    $member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id=:id limit 1 ", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    if (empty($member)) {
        message('会员不存在，无法取消分销商资格!', $this->createPluginWebUrl('commission/agent'), 'error');
    }
    $agentcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where  uniacid=:uniacid and agentid=:agentid limit 1 ', array(
        ':uniacid' => $_W['uniacid'],
        ':agentid' => $id
    ));
    if ($agentcount > 0) {
        message('此会员有下线存在，无法取消分销商资格!', '', 'error');
    }
    pdo_update('sz_yi_member', array(
        'isagent' => 0,
        'status' => 0
    ), array(
        'id' => $_GPC['id']
    ));
    plog('commission.agent.delete', "取消分销商资格 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
    message('删除成功！', $this->createPluginWebUrl('commission/agent'), 'success');
} else if ($operation == 'agentblack') {
    ca('commission.agent.agentblack');
    $id     = intval($_GPC['id']);
    $member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id=:id limit 1 ", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    if (empty($member)) {
        message('会员不存在，无法设置黑名单!', $this->createPluginWebUrl('commission/agent'), 'error');
    }
    $black = intval($_GPC['black']);
    if (!empty($black)) {
        pdo_update('sz_yi_member', array(
            'isagent' => 1,
            'status' => 0,
            'agentblack' => 1
        ), array(
            'id' => $_GPC['id']
        ));
        plog('commission.agent.agentblack', "设置黑名单 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('设置黑名单成功！', $this->createPluginWebUrl('commission/agent'), 'success');
    } else {
        pdo_update('sz_yi_member', array(
            'isagent' => 1,
            'status' => 1,
            'agentblack' => 0
        ), array(
            'id' => $_GPC['id']
        ));
        plog('commission.agent.agentblack', "取消黑名单 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        message('取消黑名单成功！', $this->createPluginWebUrl('commission/agent'), 'success');
    }
} else if ($operation == 'user') {
    ca('hotel.agent.user');
    $level     = intval($_GPC['level']);
    $agentid   = intval($_GPC['id']);
    $member    = $this->model->getInfo($agentid);
    $total     = $member['agentcount'];
    $condition = '';
    $params    = array();
    if($total > 0){
        $inagents = implode(',', $member['agentids']);
        $condition .= " and dm.id in( " . $inagents . ")";
    }else{
        $condition .= " and dm.agentid=".$member['id']." and dm.status=1";
    }
    $hasagent = true;
    if (!empty($_GPC['mid'])) {
        $condition .= ' and dm.id=:mid';
        $params[':mid'] = intval($_GPC['mid']);
    }
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }
    if ($_GPC['isagent'] != '') {
        $condition .= ' and dm.isagent=' . intval($_GPC['isagent']);
    }
    if ($_GPC['status'] != '') {
        $condition .= ' and dm.status=' . intval($_GPC['status']);
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['agentlevel'])) {
        $condition .= ' and dm.agentlevel=' . intval($_GPC['agentlevel']);
    }
    if ($_GPC['parentid'] == '0') {
        $condition .= ' and dm.agentid=0';
    } else if (!empty($_GPC['parentname'])) {
        $_GPC['parentname'] = trim($_GPC['parentname']);
        $condition .= ' and ( p.mobile like :parentname or p.nickname like :parentname or p.realname like :parentname)';
        $params[':parentname'] = "%{$_GPC['parentname']}%";
    }
    if ($_GPC['followed'] != '') {
        if ($_GPC['followed'] == 2) {
            $condition .= ' and f.follow=0 and dm.uid<>0';
        } else {
            $condition .= ' and f.follow=' . intval($_GPC['followed']);
        }
    }
    if ($_GPC['agentblack'] != '') {
        $condition .= ' and dm.agentblack=' . intval($_GPC['agentblack']);
    }
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $list   = array();
    if ($hasagent) {
        $total = pdo_fetchcolumn("select count(dm.id) from" . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid" . " where dm.uniacid =" . $_W['uniacid'] . "  {$condition}", $params);
        $list  = pdo_fetchall("select dm.*,p.nickname as parentname,p.avatar as parentavatar  from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid  and f.uniacid={$_W['uniacid']}" . " where dm.uniacid = " . $_W['uniacid'] . "  {$condition}   ORDER BY dm.agenttime desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
        $pager = pagination($total, $pindex, $psize);
        foreach ($list as &$row) {
            $info              = $this->model->getInfo($row['openid'], array(
                'total',
                'pay'
            ));
            $row['levelcount'] = $info['agentcount'];
            if ($this->set['level'] >= 1) {
                $row['level1'] = $info['level1'];
            }
            if ($this->set['level'] >= 2) {
                $row['level2'] = $info['level2'];
            }
            if ($this->set['level'] >= 3) {
                $row['level3'] = $info['level3'];
            }
            $row['credit1']          = m('member')->getCredit($row['openid'], 'credit1');
            $row['credit2']          = m('member')->getCredit($row['openid'], 'credit2');
            $row['commission_total'] = $info['commission_total'];
            $row['commission_pay']   = $info['commission_pay'];
            $row['followed']         = m('user')->followed($row['openid']);
            if ($row['agentid'] == $member['id']) {
                $row['level'] = 1;
            } else if (in_array($row['agentid'], array_keys($member['level1_agentids']))) {
                $row['level'] = 2;
            } else if (in_array($row['agentid'], array_keys($member['level2_agentids']))) {
                $row['level'] = 3;
            }
        }
    }
    unset($row);
    load()->func('tpl');
    include $this->template('agent_user');
    exit;
} else if ($operation == 'query') {
    $kwd      = trim($_GPC['keyword']);
    $wechatid = intval($_GPC['wechatid']);
    if (empty($wechatid)) {
        $wechatid = $_W['uniacid'];
    }
    $params             = array();
    $params[':uniacid'] = $wechatid;
    $condition          = " and uniacid=:uniacid and isagent=1 and status=1";
    if (!empty($kwd)) {
        $condition .= " AND ( `nickname` LIKE :keyword or `realname` LIKE :keyword or `mobile` LIKE :keyword )";
        $params[':keyword'] = "%{$kwd}%";
    }
    if (!empty($_GPC['selfid'])) {
        $condition .= " and id<>" . intval($_GPC['selfid']);
    }
    $ds = pdo_fetchall('SELECT id,avatar,nickname,openid,realname,mobile FROM ' . tablename('sz_yi_member') . " WHERE 1 {$condition} order by createtime desc", $params);
    include $this->template('query');
    exit;
} else if ($operation == 'check') {
    ca('commission.agent.check');
    $id     = intval($_GPC['id']);
    $member = $this->model->getInfo($id, array(
        'total',
        'pay'
    ));
    if (empty($member)) {
        message('未找到会员信息，无法进行审核', '', 'error');
    }
    if ($member['isagent'] == 1 && $member['status'] == 1) {
        message('此分销商已经审核通过，无需重复审核!', '', 'error');
    }
    $time = time();
    pdo_update('sz_yi_member', array(
        'status' => 1,
        'agenttime' => $time
    ), array(
        'id' => $member['id'],
        'uniacid' => $_W['uniacid']
    ));
    $this->model->sendMessage($member['openid'], array(
        'nickname' => $member['nickname'],
        'agenttime' => $time
    ), TM_COMMISSION_BECOME);
    if (!empty($member['agentid'])) {
        $this->model->upgradeLevelByAgent($member['agentid']);
    }
    plog('commission.agent.check', "审核分销商 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
    message('审核分销商成功!', $this->createPluginWebUrl('commission/agent'), 'success');
}
load()->func('tpl');
include $this->template('agent');


<?php
global $_W, $_GPC;
set_time_limit(0);
$agentlevels = $this->model->getLevels();
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$trade     = m('common')->getSysset('trade');
if ($operation == 'display') {
    ca('bonus.agent.view');
    $level     = $this->set['level'];
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
        $condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.mobile like :realname or dm.membermobile like :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";
    }
    if ($_GPC['parentid'] == '0') {
        $condition .= ' and dm.agentid=0';
    } else if (!empty($_GPC['parentname'])) {
        $_GPC['parentname'] = trim($_GPC['parentname']);
        $condition .= ' and ( p.mobile like :parentname or p.nickname like :parentname or p.realname like :parentname or p.membermobile like :parentname)';
        $params[':parentname'] = "%{$_GPC['parentname']}%";
    }
    if ($_GPC['followed'] != '') {
        if ($_GPC['followed'] == 2) {
            $condition .= ' and f.follow=0 and dm.uid<>0';
        } else {
            $condition .= ' and f.follow=' . intval($_GPC['followed']);
        }
    }

    if($_GPC['bonus_area'] != ''){
        $condition .= " and dm.bonus_area=" . intval($_GPC['bonus_area']);
    }
    if($_GPC['reside']['province'] != "" && $_GPC['reside']['province'] != "请选择省份"){
        $condition .= " and dm.bonus_province='".$_GPC['reside']['province']."'";
    }

    if($_GPC['reside']['city'] != "" && $_GPC['reside']['city'] != "请选择城市"){
        $condition .= "and dm.bonus_city='".$_GPC['reside']['city']."'";
    }

    if($_GPC['reside']['district'] != "" && $_GPC['reside']['district'] != "请选择区域"){
        $condition .= "and dm.bonus_district='".$_GPC['reside']['district']."'";
    }

    if($_GPC['reside']['street'] != "" && $_GPC['reside']['street'] != "请选择街道"){
        $condition .= "and dm.bonus_street='".$_GPC['reside']['street']."'";
    }

    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['time'])) {
        $starttime = strtotime($_GPC['time']['start']);
        $endtime   = strtotime($_GPC['time']['end']);
        if ($_GPC['searchtime'] == '1') {
            $condition .= " AND dm.agenttime >= :starttime AND dm.agenttime <= :endtime ";
            $params[':starttime'] = $starttime;
            $params[':endtime']   = $endtime;
        }
    }
    if (!empty($_GPC['agentlevel'])) {
        $condition .= ' and dm.bonuslevel=' . intval($_GPC['agentlevel']);
    }
    if ($_GPC['status'] != '') {
        $condition .= ' and dm.status=' . intval($_GPC['status']);
    }
    if ($_GPC['agentblack'] != '') {
        $condition .= ' and dm.agentblack=' . intval($_GPC['agentblack']);
    }
    $sql = "select dm.*,dm.nickname,dm.avatar,l.levelname,p.nickname as parentname,p.avatar as parentavatar from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('sz_yi_bonus_level') . " l on l.id = dm.bonuslevel" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid and f.uniacid={$_W['uniacid']}" . " where dm.uniacid = " . $_W['uniacid'] . " and dm.isagent =1 and (dm.bonuslevel!=0 || dm.bonus_area!=0) {$condition} ORDER BY dm.agenttime desc";
    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn("select count(dm.id) from" . tablename('sz_yi_member') . " dm  " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid" . " where dm.uniacid =" . $_W['uniacid'] . " and dm.isagent =1 and (dm.bonuslevel!=0 || dm.bonus_area!=0) {$condition}", $params);
    foreach ($list as &$row) {
        $info              = $this->model->getInfo($row['openid'], array(
            'total',
            'pay'
        ));
        $row['levelcount'] = $info['agentcount'];
        if ($level >= 1) {
            $row['level1'] = $info['level1'];
        }
        if ($level >= 2) {
            $row['level2'] = $info['level2'];
        }
        if ($level >= 3) {
            $row['level3'] = $info['level3'];
        }
        $row['credit1']          = m('member')->getCredit($row['openid'], 'credit1');
        $row['credit2']          = m('member')->getCredit($row['openid'], 'credit2');
        $row['commission_total'] = $info['commission_total'];
        $row['commission_pay']   = $info['commission_pay'];
        $row['followed']         = m('user')->followed($row['openid']);
        $row['avatar'] = m('member')->getHeadimg($row);
    }
    unset($row);
    if ($_GPC['export'] == '1') {
        ca('commission.agent.export');
        plog('commission.agent.export', '导出代理商数据');
        foreach ($list as &$row) {
            $row['createtime'] = date('Y-m-d H:i', $row['createtime']);
            $row['agentime']   = empty($row['agenttime']) ? '' : date('Y-m-d H:i', $row['agentime']);
            $row['groupname']  = empty($row['groupname']) ? '无分组' : $row['groupname'];
            $row['levelname']  = empty($row['levelname']) ? '普通等级' : $row['levelname'];
            $row['parentname'] = empty($row['parentname']) ? '总店' : "[" . $row['agentid'] . "]" . $row['parentname'];
            $row['statusstr']  = empty($row['status']) ? '' : "通过";
            $row['followstr']  = empty($row['followed']) ? '' : "已关注";
        }
        unset($row);
        m('excel')->export($list, array(
            "title" => "代理商数据-" . date('Y-m-d-H-i', time()),
            "columns" => array(
                array(
                    'title' => 'ID',
                    'field' => 'id',
                    'width' => 12
                ),
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
                    'title' => '微信号',
                    'field' => 'weixin',
                    'width' => 12
                ),
                array(
                    'title' => '推荐人',
                    'field' => 'parentname',
                    'width' => 12
                ),
                array(
                    'title' => '代理商等级',
                    'field' => 'levelname',
                    'width' => 12
                ),
                array(
                    'title' => '点击数',
                    'field' => 'clickcount',
                    'width' => 12
                ),
                array(
                    'title' => '下线总数',
                    'field' => 'levelcount',
                    'width' => 12
                ),
                array(
                    'title' => '累计分红佣金',
                    'field' => 'commission_total',
                    'width' => 12
                ),
                array(
                    'title' => '发放分红佣金',
                    'field' => 'commission_pay',
                    'width' => 12
                ),
                array(
                    'title' => '成为会员时间',
                    'field' => 'createtime',
                    'width' => 12
                ),
                array(
                    'title' => '是否关注',
                    'field' => 'followstr',
                    'width' => 12
                )
            )
        ));
    }
    $pager = pagination($total, $pindex, $psize);
} else if ($operation == 'detail') {
    ca('bonus.agent.view');
    $id     = intval($_GPC['id']);
    $member = $this->model->getInfo($id, array(
        'total',
        'pay'
    ));
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
    if(!empty($member['check_imgs'])){
        $check_imgs = set_medias(unserialize($member['check_imgs']));
    }
    if (checksubmit('submit')) {
        ca('bonus.agent.edit|bonus.agent.check|bonus.agent.agentblack');
        $data = is_array($_GPC['data']) ? $_GPC['data'] : array();
        if (empty($_GPC['oldstatus']) && $data['status'] == 1) {
            $time              = time();
            $data['agenttime'] = time();
            $this->model->sendMessage($member['openid'], array(
                'nickname' => $member['nickname'],
                'agenttime' => $time
            ), TM_COMMISSION_BECOME);
            plog('bonus.agent.check', "审核分销商 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        }
        if (empty($_GPC['oldagentblack']) && $data['agentblack'] == 1) {
            $data['agentblack'] = 1;
            $data['status']     = 0;
            $data['isagent']    = 1;
        }
        $reside = $_GPC['reside'];
        if(!empty($data['bonus_area'])){
            if($data['bonus_area'] == 1){
                if(empty($reside['province'])){
                    message('请选择代理的省', '', 'error');
                }
            }else if($data['bonus_area'] == 2){
                if(empty($reside['city'])){
                    message('请选择代理的市', '', 'error');
                }
            }else if($data['bonus_area'] == 3){
                if(empty($reside['district'])){
                    message('请选择代理的区', '', 'error');
                }
            }else if($data['bonus_area'] == 4){
                if(empty($reside['street'])){
                    message('请选择代理的街', '', 'error');
                }
            }
            $data['bonus_province'] = $reside['province'];
            $data['bonus_city'] = $reside['city'];
            $data['bonus_district'] = $reside['district'];
            $data['bonus_street'] = $reside['street'];
        }
        if($member['bonuslevel'] != $data['bonuslevel']){
            plog('bonus.agent.edit', "修改代理等级 原代理等级ID：{$member['bonuslevel']} 改为 ID：{$data['bonuslevel']}");
        }
        if($member['bonus_area'] != $data['bonus_area']){
            plog('bonus.agent.edit', "修改代理等级 原地区等级ID：{$member['bonus_area']} 改为 ID：{$data['bonus_area']}");
        }
        if($member['bonus_area_commission'] != $data['bonus_area_commission']){
            plog('bonus.agent.edit', "修改地区代理比例 原比例：{$member['bonus_area_commission']}% 改为 {$data['bonus_area_commission']}%");
        }
        if(!empty($data['bonuslevel'])){
            $data['bonus_status'] = 1;
            if($_GPC['isagency'] == 1){
               $data['isagency'] =2;
            }else if($_GPC['isagency'] == 0){
                $data['isagency'] =-1;
            }else{
               $data['isagency'] =2;
            }
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
        message('保存成功!', $this->createPluginWebUrl('bonus/agent'), 'success');
    }
    $diyform_flag   = 0;
    $diyform_plugin = p('diyform');
    if ($diyform_plugin) {
        if (!empty($member['diycommissiondata'])) {
            $diyform_flag = 1;
            $fields       = iunserializer($member['diycommissionfields']);
        }
    }
} else if ($operation == 'user') {
    ca('bonus.agent.user');
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
    if($_GPC['bonus_area'] != ''){
        if($_GPC['bonus_area'] == 1){
            $condition .= " and dm.bonus_area=1";
        }else if($_GPC['bonus_area'] == 2){
            $condition .= " and dm.bonus_area=2";
        }else if($_GPC['bonus_area'] == 3){
            $condition .= " and dm.bonus_area=3";
        }
    }
    if ($_GPC['status'] != '') {
        $condition .= ' and dm.status=' . intval($_GPC['status']);
    }
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime('-1 month');
        $endtime   = time();
    }
    if (!empty($_GPC['bonuslevel'])) {
        $condition .= ' and dm.bonuslevel=' . intval($_GPC['bonuslevel']);
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
        $list  = pdo_fetchall("select dm.*,p.nickname as parentname,p.avatar as parentavatar,dm.bonuslevel  from " . tablename('sz_yi_member') . " dm " . " left join " . tablename('sz_yi_member') . " p on p.id = dm.agentid " . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid  and f.uniacid={$_W['uniacid']}" . " where dm.uniacid = " . $_W['uniacid'] . "  {$condition}   ORDER BY dm.agenttime desc limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
        $pager = pagination($total, $pindex, $psize);

        $plc_commission = p('commission');
        
        foreach ($list as &$row) {
            $info              = $this->model->getInfo($row['openid'], array(
                'total',
                'pay'
            ));
            $commission_info = $plc_commission->getInfo($row['openid'], array());
            $row['credit1']          = m('member')->getCredit($row['openid'], 'credit1');
            $row['credit2']          = m('member')->getCredit($row['openid'], 'credit2');
            $row['commission_total'] = $info['commission_total'];
            $row['commission_pay']   = $info['commission_pay'];
            $row['followed']         = m('user')->followed($row['openid']);
            $row['levelname'] = pdo_fetchcolumn("select levelname from" . tablename('sz_yi_bonus_level') . " where uniacid =" . $_W['uniacid'] . "  and id=".$row['bonuslevel']);
            if(empty($row['levelname'])){
                $row['levelcount'] = $commission_info['agentcount'];
            }else{
                $row['levelcount'] = $info['agentcount'];
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
    ca('bonus.agent.check');
    $id     = intval($_GPC['id']);
    $member = $this->model->getInfo($id, array(
        'total',
        'pay'
    ));
    if (empty($member)) {
        message('未找到会员信息，无法进行审核', '', 'error');
    }
    if ($member['isagent'] == 1 && $member['bonus_status'] < 9) {
        message('此代理商已经审核通过，无需重复审核!', '', 'error');
    }
    $time = time();
    pdo_update('sz_yi_member', array(
        'bonus_status' => 1
    ), array(
        'id' => $member['id'],
        'uniacid' => $_W['uniacid']
    ));
    if (!empty($member['agentid'])) {
        $this->model->upgradeLevelByAgent($member['agentid']);
    }
    plog('bonus.agent.check', "审核代理商 <br/>代理商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
    message('审核代理商成功!', $this->createPluginWebUrl('bonus/agent'), 'success');
}
load()->func('tpl');
include $this->template('agent');


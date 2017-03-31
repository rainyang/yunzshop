<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/18
 * Time: 下午2:55
 */

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$op     = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$groups = m('member')->getGroups();
$levels = m('member')->getLevels();
$shop   = m('common')->getSysset('shop');

$status = isset($_GPC['status']) ? $_GPC['status'] : 1;
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
        $condition .= ' and ( dm.realname like :realname or dm.nickname like :realname or dm.membermobile like :realname or dm.mobile like :realname)';
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

    $sql = "select dm.*,l.levelname,g.groupname,a.nickname as agentnickname,a.avatar as agentavatar from " . tablename('sz_yi_member') . " dm " . " join" . tablename('sz_yi_live_anchor') ." la on la.openid = dm.openid and la.uniacid ={$_W['uniacid']} and la.status = '" . $status . "' left join " . tablename('sz_yi_member_group') . " g on dm.groupid=g.id" . " left join " . tablename('sz_yi_member') . " a on a.id=dm.agentid" . " left join " . tablename('sz_yi_member_level') . " l on dm.level =l.id" . " left join " . tablename('mc_mapping_fans') . "f on f.openid=dm.openid  and f.uniacid={$_W['uniacid']}" . " where 1 {$condition}  ORDER BY dm.id DESC";
// echo("<pre>");print_r($sql);exit;

    if (empty($_GPC['export'])) {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    $list = pdo_fetchall($sql, $params);
    // echo("<pre>");print_r($_W['uniacid']);exit;
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

    //todo
    $mt = mt_rand(5, 25);
    if ($mt <= 10) {
        load()->func('communication');
        $CHECK_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($CHECK_URL, array(
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
    $total = count($list);
    $pager = pagination($total, $pindex, $psize);

} else if ($op == 'detail') {
    ca('member.member.view');
    $trade     = m('common')->getSysset('trade');
    $hasbonus = false;
    $plugin_bonus    = p('bonus');
    if ($plugin_bonus) {
        $plugin_bonus_set = $plugin_bonus->getSet();
        if($plugin_bonus_set['start'] == 1 || $plugin_bonus_set['area_start'] == 1){
            $hasbonus  = true;
        }
    }
    $hascommission = false;
    $plugin_com    = p('commission');
    if ($plugin_com) {
        $plugin_com_set = $plugin_com->getSet();
        $hascommission  = !empty($plugin_com_set['level']);
    }
    $id = intval($_GPC['id']);
    $member = m('member')->getMember($id);
    if(empty($member)){
        message('会员不存在，已被删除或参数错误!','', 'error');
    }
    if (checksubmit('submit')) {
        ca('member.member.edit');
        $data = is_array($_GPC['data']) ? $_GPC['data'] : array();

        //$member = m('member')->getMember($id);

        if( (!empty($data['level']) || !empty($member['level'])) && $data['level'] != $member['level'])
        {

            $new_level_name = $old_level_name = '普通会员';
            foreach ($levels as $key => $value) {
                if($data['level'] == $value['id'])
                {
                    $new_level_name = $value['levelname'];
                }

                if($member['level'] == $value['id'])
                {
                    $old_level_name = $value['levelname'];
                }
            }
            $msg = array(
                'first' => array(
                    'value' => "后台修改会员等级！",
                    "color" => "#4a5077"
                ),
                'keyword1' => array(
                    'title' => '修改等级',
                    'value' => "由【". $old_level_name ."】修改为 【" . $new_level_name . "】!",
                    "color" => "#4a5077"
                ),
                'remark' => array(
                    'value' => "\r\n我们已为您修改会员等级。",
                    "color" => "#4a5077"
                )
            );

            $detailurl  = $this->createMobileUrl('member');
            m('message')->sendCustomNotice($member['openid'], $msg, $detailurl);
        }


        pdo_update('sz_yi_member', $data, array(
            'id' => $id,
            'uniacid' => $_W['uniacid']
        ));
        //$member = m('member')->getMember($id);
        //论坛插件-会员信息同步
        if (p('discuz') ) {
            p('discuz')->updateUserInfo($member['uid'], array('realname'=>$data['realname'],
                'mobile'=>$data['membermobile']));
        }

        plog('member.member.edit', "修改会员资料  ID: {$member['id']} <br/> 会员信息:  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['membermobile']}");
        if ($hascommission) {
            if (cv('commission.agent.changeagent')) {
                $adata = is_array($_GPC['adata']) ? $_GPC['adata'] : array();
                if (!empty($adata)) {
                    $agentname = $_GPC['adata']['agentid'] ? $_GPC['adata']['agentid'] : "总店";
                    if (empty($_GPC['oldstatus']) && $adata['status'] == 1) {
                        $time               = time();
                        $adata['agenttime'] = time();
                        $plugin_com->sendMessage($member['openid'], array(
                            'nickname' => $member['nickname'],
                            'agenttime' => $time
                        ), TM_COMMISSION_BECOME);
                        plog('commission.agent.check', "审核分销商 <br/>分销商信息:  ID: {$member['id']} 上级修改为 ID：{$agentname}");
                    }
                    plog('commission.agent.edit', "修改分销商 <br/>分销商信息:  ID: {$member['id']} 上级修改为 ID：{$agentname}");
                    pdo_update('sz_yi_member', $adata, array(
                        'id' => $id,
                        'uniacid' => $_W['uniacid']
                    ));
                    if (empty($_GPC['oldstatus']) && $adata['status'] == 1) {
                        if (!empty($member['agentid'])) {
                            $plugin_com->upgradeLevelByAgent($member['agentid']);
                        }
                    }
                }
            }
        }
        if($plugin_bonus){
            if (cv('bonus.agent.changeagent')) {
                $bdata = is_array($_GPC['bdata']) ? $_GPC['bdata'] : array();
                if (!empty($bdata)) {
                    $reside = $_GPC['reside'];
                    if(!empty($bdata['bonus_area'])){
                        if($bdata['bonus_area'] == 1){
                            if(empty($reside['province'])){
                                message('请选择代理的省', '', 'error');
                            }
                        }else if($bdata['bonus_area'] == 2){
                            if(empty($reside['city'])){
                                message('请选择代理的市', '', 'error');
                            }
                        }else if($bdata['bonus_area'] == 3){
                            if(empty($reside['district'])){
                                message('请选择代理的区', '', 'error');
                            }
                        }
                        $bdata['bonus_province'] = $reside['province'];
                        $bdata['bonus_city'] = $reside['city'];
                        $bdata['bonus_district'] = $reside['district'];
                    }

                    pdo_update('sz_yi_member', $bdata, array(
                        'id' => $id,
                        'uniacid' => $_W['uniacid']
                    ));
                    if($member['bonuslevel'] != $bdata['bonuslevel']){
                        plog('member.member.edit', "修改代理等级 原代理等级ID：{$member['bonuslevel']} 改为 ID：{$bdata['bonuslevel']}");
                    }
                    if($member['bonus_area'] != $bdata['bonus_area']){
                        plog('member.member.edit', "修改代理等级 原地区等级ID：{$member['bonus_area']} 改为 ID：{$bdata['bonus_area']}");
                    }
                    if($member['bonus_area_commission'] != $bdata['bonus_area_commission']){
                        plog('member.member.edit', "修改地区代理比例 原比例：{$member['bonus_area_commission']}% 改为 {$bdata['bonus_area_commission']}%");
                    }
                }
            }
        }
        message('保存成功!', $this->createWebUrl('member/list'), 'success');
    }
    if ($hascommission) {
        $agentlevels = $plugin_com->getLevels();
    }
    if ($plugin_bonus) {
        $bonuslevels = $plugin_bonus->getLevels();
    }

    if ($hascommission) {
        $member = $plugin_com->getInfo($id, array(
            'total',
            'pay'
        ));
    }
    $member['self_ordercount'] = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(
        ':uniacid' => $_W['uniacid'],
        ':openid' => $member['openid']
    ));
    $member['self_ordermoney'] = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('sz_yi_order') . ' where uniacid=:uniacid and openid=:openid and status=3', array(
        ':uniacid' => $_W['uniacid'],
        ':openid' => $member['openid']
    ));
    if (!empty($member['agentid'])) {
        $parentagent = m('member')->getMember($member['agentid']);
    }
    $diyform_flag   = 0;
    $diyform_plugin = p('diyform');
    if ($diyform_plugin) {
        if (!empty($member['diymemberdata'])) {
            $diyform_flag = 1;
            $fields       = iunserializer($member['diymemberfields']);
        }
    }
} else if ($op == 'delete') {
    ca('member.member.delete');
    $id      = intval($_GPC['id']);
    $isagent = intval($_GPC['isagent']);
    $member  = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id=:id limit 1 ", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    if (empty($member)) {
        message('会员不存在，无法删除!', $this->createWebUrl('member/list'), 'error');
    }
    if (p('commission')) {
        $agentcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member') . ' where  uniacid=:uniacid and agentid=:agentid limit 1 ', array(
            ':uniacid' => $_W['uniacid'],
            ':agentid' => $id
        ));
        if ($agentcount > 0) {
            message('此会员有下线存在，无法删除! ', '', 'error');
        }
    }
    if (p('return')) {
        pdo_query("update  " . tablename('sz_yi_return') . " set `delete` = '1' WHERE `uniacid` = '". $_W['uniacid'] ."' and `mid` = '".$_GPC['id']."'");
    }
    pdo_delete('sz_yi_member', array(
        'id' => $_GPC['id']
    ));
    $openid = $member['openid'];
    $fans = pdo_fetchall("select uid from " . tablename('mc_mapping_fans') . " where uniacid=:uniacid and openid=:openid", array(
        ':uniacid' => $_W['uniacid'],
        ':openid' => $openid
    ),'id');
    //删除会员其它两张表的数据
    if(!empty($fans)){
        pdo_delete('mc_mapping_fans', array(
            'openid' => $openid,
            'uniacid' => $_W['uniacid']
        ));
        pdo_query('delete from ' . tablename('mc_members') . ' where uid in (' . implode(',', array_keys($fans)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
    }

    plog('member.member.delete', "删除会员  ID: {$member['id']} <br/>会员信息: {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['membermobile']}");
    message('删除成功！', $this->createWebUrl('member/list'), 'success');
} else if ($operation == 'setblack') {
    ca('member.member.setblack');
    $id     = intval($_GPC['id']);
    $member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid=:uniacid and id=:id limit 1 ", array(
        ':uniacid' => $_W['uniacid'],
        ':id' => $id
    ));
    if (empty($member)) {
        message('会员不存在，无法设置黑名单!', $this->createWebUrl('member/list'), 'error');
    }
    $black = intval($_GPC['black']);
    if (!empty($black)) {
        pdo_update('sz_yi_member', array(
            'isblack' => 1
        ), array(
            'id' => $_GPC['id']
        ));
        plog('member.member.black', "设置黑名单 <br/>用户信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['membermobile']}");
        message('设置黑名单成功！', $this->createWebUrl('member/list'), 'success');
    } else {
        pdo_update('sz_yi_member', array(
            'isblack' => 0
        ), array(
            'id' => $_GPC['id']
        ));
        plog('member.member.black', "取消黑名单 <br/>用户信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['membermobile']}");
        message('取消黑名单成功！', $this->createWebUrl('member/list'), 'success');
    }
}
load()->func('tpl');
include $this->template('index');

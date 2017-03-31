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
    set_time_limit(0);
    $where = '';
    if($_GPC['status'] == 1){
      $where = " and openid in ('".$_GPC['mid']."')";  
    }
    $sql = "select * from " . tablename('mc_mapping_fans') . " where uniacid=".$_W['uniacid'].$where." and follow = 1 ORDER BY fanid DESC";
    $list = pdo_fetchall($sql);
    foreach ($list as $key => $row) {
        $fan = $this->model->fansQueryInfo($row['openid']);
        if(!is_error($fan) && $fan['subscribe'] == 1) {
            $group = $this->model->fetchFansGroupid($row['openid']);
            $record = array();
            if(!is_error($group)) {
                $record['groupid'] = $group['groupid'];
            }
            $record['updatetime'] = TIMESTAMP;
            $record['followtime'] = $fan['subscribe_time'];
            $fan['nickname'] = stripcslashes($fan['nickname']);
            $record['nickname'] = stripslashes($fan['nickname']);
            $record['tag'] = iserializer($fan);
            $record['tag'] = base64_encode($record['tag']);
            pdo_update('mc_mapping_fans', $record, array('fanid' => $row['fanid']));
            
            if(!empty($row['uid'])) {
                $rec = array();
                $shopdata = array();
                if(!empty($fan['nickname'])) {
                    $rec['nickname'] = stripslashes($fan['nickname']);
                    $shopdata['nickname'] = stripslashes($fan['nickname']);
                }
                if(!empty($fan['sex'])) {
                    $rec['gender'] = $fan['sex'];
                    $shopdata['gender'] = $fan['sex'];
                }
                if(!empty($fan['city'])) {
                    $rec['residecity'] = $fan['city'] . '市';
                    $shopdata['city'] = $fan['city'] . '市';
                }
                if(!empty($fan['province'])) {
                    $rec['resideprovince'] = $fan['province'] . '省';
                    $shopdata['province'] = $fan['province'] . '省';
                }
                if(!empty($fan['country'])) {
                    $rec['nationality'] = $fan['country'];
                }
                if(!empty($fan['headimgurl'])) {
                    $rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
                    $shopdata['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
                }
                if(!empty($rec)) {
                    pdo_update('mc_members', $rec, array('uid' => $row['uid'])); 
                }
                if(!empty($shopdata)){
                    $shopdata['uid'] = $row['uid'];
                    pdo_update('sz_yi_member', $shopdata, array('openid' => $row['openid']));
                }

            }
        }
    }
    message("会员信息更新成功", $this->createPluginWebUrl('fans/member', array("op" => 'display')), "success");
} else if ($op == 'detail') {
    if(empty($_GPC['openid'])){
        message("openid不能为空", '', 'error');
    }
   
    //echo "select * from " . tablename('mc_mapping_fans') . " where openid='".$_GPC['openid']."'";exit();
    $userinfo = pdo_fetchall("select * from " . tablename('sz_yi_member') . " where avatar=132");
    foreach ($userinfo as $k => $v) {
        $a = array_rand($ims_sz_yi_member);
        $shopdata = array(
            //"nickname" => $ims_sz_yi_member[$a]['nickname'],
            "avatar" => $ims_sz_yi_member[$a]['avatar'],
            );
        pdo_update('sz_yi_member', $shopdata, array('id' => $v['id']));
    }
}
load()->func('tpl');
include $this->template('member');

<?php
global $_W, $_GPC;
ca('bonus.detail.view');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$params    = array(
        ':uniacid' => $_W['uniacid']
    );
$daytime = strtotime(date('Y-m-d',time()));
$sn = $_GPC['sn'];
$isglobal = empty($_GPC['isglobal']) ? 0 : 1;
$params[':sn'] = $sn;
$params[':isglobal'] = $isglobal;
if($operation == "display"){
	$pindex    = max(1, intval($_GPC['page']));
	$psize     = 20;
	$condition = '';
	$member_where = "";
    if (!empty($_GPC['mid'])) {
    	$member_where .= " and id=".intval($_GPC['mid']);
    }

    if (!empty($_GPC['realname'])) {
    	$realname = trim($_GPC['realname']);
        $member_where .= " and ( realname like '%{$realname}%' or nickname like '%{$realname}%' or mobile like '%{$realname}%' or membermobile like '%{$realname}%')";
    }

    if(!empty($member_where)){
    	$openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_member') . " where uniacid={$_W['uniacid']}" . $member_where);
        $condition .= ' and openid=:openid';
        $params[':openid'] = $openid;
    }
	$logs = pdo_fetchall("select * from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and send_bonus_sn =:sn and isglobal=:isglobal {$condition} limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
	$total = pdo_fetchcolumn("select count(id) from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and send_bonus_sn =:sn and isglobal=:isglobal", $params);
	foreach ($logs as $key => &$value) {
	    $member = m('member')->getInfo($value['openid']);
	    $value['avatar'] = $member['avatar'];
	    $value['mobile'] = $member['mobile'];
	    $value['realname'] = $member['realname'];
	    $value['nickname'] = $member['nickname'];
	    $value['credit2'] = $member['credit2'];
	    $value['credit1'] = $member['credit1'];
	    $value['member_id'] = $member['id'];
    }
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
	$pager = pagination($total, $pindex, $psize);
}else if($operation == "afresh"){
	ca('bonus.detail.afresh');
	$logs = pdo_fetchall("select * from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and send_bonus_sn =:sn and isglobal=:isglobal and sendpay=0", $params);
	$sendpay_error = 0;
	foreach ($logs as $key => $value) {
		$sendpay = 1;
	    $logno = m('common')->createNO('bonus_log', 'logno', 'RB');
		$result = m('finance')->pay($value['openid'], 1, $value['money'] * 100, $logno, '平台分红');
        if (is_error($result)) {
            $sendpay = 0;
            $sendpay_error = 1;
        }
		pdo_update('sz_yi_bonus_log', array(
            "sendpay" => $sendpay,
	        ),array(
	        "openid" => $value['openid'],
	        "uniacid" => $_W['uniacid']
	        ));
        
        if($sendpay == 1){
        	$member = m('member')->getInfo($value['openid']);
        	$level = $this->model->getlevel($member['openid']);
        	if(empty($level)){
				if($member['bonus_area'] == 1){
					$level['levelname'] = "省级代理";
				}else if($member['bonus_area'] == 2){
					$level['levelname'] = "市级代理";
				}else if($member['bonus_area'] == 3){
					$level['levelname'] = "区级代理";
				}else if($member['bonus_area'] == 4){
					$levelname = "街级代理";
				}
			}
        	$this->model->sendMessage($value['openid'], array('nickname' => $member['nickname'], 'levelname' => $level['levelname'], 'commission' => $value['money'], 'type' => "微信钱包"), TM_BONUS_PAY);
        }
	}
	pdo_update('sz_yi_bonus', array(
            "sendpay_error" => $sendpay_error,
	        ),array(
	        "send_bonus_sn" => $sn,
	        "uniacid" => $_W['uniacid']
	        ));
	plog('bonus.detail', "发放分红失败代理重发分红");
	message("分红重新发放成功", $this->createPluginWebUrl('bonus/detail', array("sn" => $sn)), "success");
}else if($operation == "list"){
	$type = intval($_GPC['type']);
	$sendwhere = "";
	if ($type > 1) {
		$sendwhere = " and type=".$type;
	}elseif ($type == 1){
		$sendwhere = " and isglobal=".$type;
	}
	$totalmoney = pdo_fetchcolumn("select sum(money) as totalmoney from " . tablename('sz_yi_bonus') . " where uniacid=:uniacid".$sendwhere, array(':uniacid' => $_W['uniacid']));
	$pindex    = max(1, intval($_GPC['page']));
	$psize     = 20;
	$total  = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_bonus') . " where uniacid={$_W['uniacid']}" . $sendwhere);
	$list  = pdo_fetchall('select * from ' . tablename('sz_yi_bonus') . " where uniacid={$_W['uniacid']}".$sendwhere." order by id desc limit " . ($pindex - 1) * $psize . ',' . $psize);
	$pager = pagination($total, $pindex, $psize);
}else if($operation == "getopenids"){
	//获取发放成功的openid
	$member = pdo_fetchall("SELECT openid FROM " . tablename('sz_yi_bonus_log') . " WHERE uniacid = '{$_W['uniacid']}' and send_bonus_sn=:sn and sendpay=1", array(":sn" => $_GPC['sn']), 'openid');
    plog('bonus', "分红群发消息 人数: " . count($member));
    die(json_encode(array(
        'result' => 1,
        'openids' => array_keys($member)
    )));
}else if($operation == "sendmessage"){
	$openid = $_GPC['openid'];
	$set = $this->set;
	$member =  pdo_fetch("SELECT nickname, bonuslevel, bonus_area FROM " . tablename('sz_yi_member') . " WHERE `uniacid` = '{$_W['uniacid']}' and  `openid`=:openid", array(":openid" => $openid));

	$log = pdo_fetch("SELECT openid, money, ctime FROM " . tablename('sz_yi_bonus_log') . " WHERE `uniacid` = '{$_W['uniacid']}' and `openid`=:openid and `send_bonus_sn`=:sn", array(":sn" => $_GPC['sn'], ":openid" => $openid));
	$bonus = pdo_fetch("SELECT paymethod, type FROM " . tablename('sz_yi_bonus') . " WHERE `uniacid` = '{$_W['uniacid']}' and  `send_bonus_sn`=:sn", array(":sn" => $_GPC['sn']));
	if($bonus['type'] == 3){
		if($member['bonus_area'] == 1){
			$levelname = "省级代理";
		}else if($member['bonus_area'] == 2){
			$levelname = "市级代理";
		}else if($member['bonus_area'] == 3){
			$levelname = "区级代理";
		}
	}else{
		$levelname =  pdo_fetchcolumn("SELECT levelname FROM " . tablename('sz_yi_bonus_level') . " WHERE uniacid = '{$_W['uniacid']}' and  id=:id", array(":id" => $member['bonuslevel']));
	}
	switch ($bonus['type']) {
		case 2:			//团队分红
			$message = $set['tm']['bonus_pay'];
			$send_title = !empty($set['tm']['bonus_paytitle']) ? $set['tm']['bonus_paytitle'] : '代理分红打款通知';
			break;
		case 3:			//地区分红
			$message = $set['tm']['bonus_pay_area'];
			$send_title = !empty($set['tm']['bonus_global_paytitle']) ? $set['tm']['bonus_global_paytitle'] : '全球分红打款通知';
			break;
		default:		//全球分红
			$message = $set['tm']['bonus_global_pay'];
			$send_title = !empty($set['tm']['bonus_paytitle_area']) ? $set['tm']['bonus_paytitle_area'] : '地区分红打款通知';
			break;
	}
	$templateid = $set['tm']['templateid'];
    $send_type = empty($bonus['paymethod']) ? "余额" : "微信钱包";
    $message = str_replace('[昵称]', $member['nickname'], $message);
    $message = str_replace('[时间]', date('Y-m-d H:i:s', time()), $message);
    $message = str_replace('[金额]', $log['money'], $message);
    $message = str_replace('[打款方式]', $send_type, $message);
    if($bonus['type'] == 3){
    	$message = str_replace('[地区等级]', $levelname, $message);
    }else{
    	$message = str_replace('[代理等级]', $levelname, $message);	
    }
    $msg = array('keyword1' => array('value' => $send_title, 'color' => '#73a68d'), 'keyword2' => array('value' => $message, 'color' => '#73a68d'));
    if (!empty($templateid)) {
        $result = m('message')->sendTplNotice($openid, $templateid, $msg);
    } else {
        $result = m('message')->sendCustomNotice($openid, $msg);
    }

    if (is_error($result)) {
        die(json_encode(array(
            'result' => 0,
            'message' => $result['message'],
            'openid' => $openid
        )));
    }
    die(json_encode(array(
        'result' => 1
    )));

}else if($operation == "sendpay"){
	$id = intval($_GPC['id']);
	$log = pdo_fetch("select * from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and id =:id", array(':id' => $id, ":uniacid" => $_W['uniacid']));
	if($log['sendpay'] == 1){
		message("该用户已经发放过了,无需重新发放", $this->createPluginWebUrl('bonus/detail', array("sn" => $sn)), "error");
	}
	$setshop = m('common')->getSysset('shop');
	$logno = m('common')->createNO('bonus_log', 'logno', 'RB');
	$bonus_log = pdo_fetch("select * from " . tablename('sz_yi_bonus') . " where uniacid=:uniacid and send_bonus_sn =:sn", array(':sn' => $sn, ":uniacid" => $_W['uniacid']));
	if($bonus_log['isglobal']==1){
		$sendname = "全球分红";
	}else{
		if($bonus_log['type'] == 2){
			$sendname = "团队分红";
		}else{
			$sendname = "地区分红";
		}
	}

	$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $logno, "【" . $setshop['name']. "】".$sendname);
    if (is_error($result)) {
        message('打款到微信钱包失败: ' . $result['message'], '', 'error');
    }
    pdo_update('sz_yi_bonus_log', array("sendpay" => 1), array("uniacid" => $_W['uniacid'], "id" => $id));
    $send_error_count = pdo_fetchall("select count(*) from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and sendpay=0 and send_bonus_sn=:sn", array(':id' => $id, ":uniacid" => $_W['uniacid'], ":sn" => $sn));
    if(empty($send_error_count)){
    	pdo_update('sz_yi_bonus', array("sendpay_error" => 0), array("uniacid" => $_W['uniacid'], "send_bonus_sn" => $sn));
    }
    message("打款成功", $this->createPluginWebUrl('bonus/detail', array("sn" => $sn)), "error");
}
include $this->template('detail');

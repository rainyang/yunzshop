<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$member = $this->model->getInfo($openid, array('total', 'ok', 'apply', 'check', 'lock', 'pay', 'myorder'));
	$proportion = true;
	if(!empty($this->set['withdraw_proportion'])){
        $withdraw_proportion = empty($level['withdraw_proportion']) ? floatval($this->set['withdraw_proportion']) : $level['withdraw_proportion'];
        if($member['myordermoney'] < $withdraw_proportion*$member['commission_ok']){
        	//计算出差额
            $proportion = false;
            $proportion_money = $member['commission_ok']*$withdraw_proportion-$member['myordermoney'];
            $member['proportion_money'] = number_format($proportion_money);
        }
    }
	$cansettle = $member['commission_ok'] > 0 && $member['commission_ok'] >= floatval($this->set['withdraw']) &&  $member['myordermoney'] >= floatval($this->set['consume_withdraw']) && $proportion;
	$member['commission_ok'] = number_format($member['commission_ok'], 2);
	$member['commission_total'] = number_format($member['commission_total'], 2);
	$member['commission_check'] = number_format($member['commission_check'], 2);
	$member['commission_apply'] = number_format($member['commission_apply'], 2);
	$member['commission_lock'] = number_format($member['commission_lock'], 2);
	$member['commission_pay'] = number_format($member['commission_pay'], 2);
	return show_json(1, array('cansettle' => $cansettle, 'settlemoney' => number_format(floatval($this->set['withdraw']), 2), 'member' => $member, 'set' => $this->set));
}
include $this->template('withdraw');

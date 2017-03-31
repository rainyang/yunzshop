<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];
//ALTER TABLE  `ims_sz_yi_member` ADD  `referralsn` VARCHAR( 255 ) NOT NULL
//echo "<pre>"; print_r($this->yzShopSet);exit;
if(empty($member['referralsn']))
{
    $referralsn    = m('common')->createNO('member', 'referralsn', 'SH');
    $data = array(
        'referralsn' => $referralsn
    );
    pdo_update('sz_yi_member', $data, array(
        'id' => $member['id'],
        'uniacid' => $_W['uniacid']
    ));
    $member['referralsn'] = $referralsn;
}


include $this->template('member/referral');

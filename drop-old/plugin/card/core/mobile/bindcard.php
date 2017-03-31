<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$set = $this->model->getSet();
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $cdkey = $_GPC['cdkey'];
        $cdkey = strtoupper($cdkey);
        $result = $this->model->verifyCDkey($cdkey);
        if (empty($result)) {
            show_json(0);
        } else {
            $data = array(
                'openid'    => $openid,
                'isbind'    => 1,
                'bindtime'  => time()
            );
            pdo_update('sz_yi_card_data', $data, array('cdkey' => $cdkey));
            show_json(1);
        }
    }
}
include $this->template('bindcard');

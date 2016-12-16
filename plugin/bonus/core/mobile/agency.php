<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid = m('user')->getOpenid();
$member = $this->model->getInfo($openid, array());
$af_supplier = pdo_fetch("select * from " . tablename("sz_yi_member") . " where openid='{$openid}' and uniacid={$_W['uniacid']} and isagency = 1");
$plc_bonus = p('bonus');
$level = $plc_bonus->getLevels();
foreach ($level as $key => $value) {
    $is_member_level = $this->model->getParentAgents_level($member['agentid'], $value['id']);
    if($is_member_level){
        unset($level[$key]);
    }else{
        $is_member_level = $this->model->getChildAgents_level($member['id'], $value['id']);
        if($is_member_level){
            unset($level[$key]);
        }  
    } 
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $data = $_GPC['memberdata'];
        $mdata = array(
            "realname" => trim($data['realname']),
            "mobile" => intval($data['mobile']),
            "weixin" => trim($data['weixin']),
            "bonuslevel" => intval($data['bonus_level']),
            "isagency" => 1
        );
        pdo_update('sz_yi_member', $mdata, array('openid' => $openid, 'uniacid' => $_W['uniacid']));
        return show_json(1);
    }
}
include $this->template('agency');

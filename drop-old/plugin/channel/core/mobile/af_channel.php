<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid         = m('user')->getOpenid();
$member         = m('member')->getInfo($openid);
$af_channel     = pdo_fetch("SELECT * FROM " . tablename("sz_yi_af_channel") . " WHERE openid='{$openid}' AND uniacid={$_W['uniacid']}");
$template_flag  = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) {
    $set_config             = $diyform_plugin->getSet();
    $channel_diyform_open   = $set_config['channel_diyform_open'];
    if ($channel_diyform_open == 1) {
        $template_flag      = 1;
        $diyform_id         = $set_config['channel_diyform'];
        if (!empty($diyform_id)) {
            $formInfo       = $diyform_plugin->getDiyformInfo($diyform_id);
            $fields         = $formInfo['fields'];
            $diyform_data   = iunserializer($af_channel['diychanneldata']);
            $f_data         = $diyform_plugin->getDiyformData($diyform_data, $fields, $af_channel);
        }
    }
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        if ($template_flag == 1) {
            $channeldata                = $_GPC['channeldata'];
            $data                       = array();
            $m_data                     = array();
            $mc_data                    = array();
            $insert_data                = $diyform_plugin->getInsertData($fields, $channeldata);
            $data                       = $insert_data['data'];
            $m_data                     = $insert_data['m_data'];
            $mc_data                    = $insert_data['mc_data'];
            $m_data['diychannelid']     = $diyform_id;
            $m_data['diychannelfields'] = iserializer($fields);
            $m_data['diychanneldata']   = $data;
            $m_data['status']           = 0;
            $m_data['openid']           = $openid;
            $m_data['uniacid']          = $_W['uniacid'];
            $m_data['mid']              = $member['id'];
            pdo_insert('sz_yi_af_channel',$m_data);
        } else {
            $channeldata = array(
            'realname'      => $_GPC['channeldata']['realname'],
            'mobile'        => $_GPC['channeldata']['mobile'],
            'openid'        => $openid,
            'uniacid'       => $_W['uniacid'],
            'status'        => 0,
            'mid'           => $member['id']
            );
            pdo_insert('sz_yi_af_channel',$channeldata);
        }
        return show_json(1);
    }
    if (!empty($af_channel)) {
        $is_channel = true;
    } else {
        $is_channel = false;
    }
    return show_json(1, array(
        'member' => $af_channel,
        'is_channel' => $is_channel
    ));
}
if ($template_flag == 1) {
    include $this->template('diyform/af_channel');
} else {
    include $this->template('af_channel');
}
<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/30
 * Time: 上午7:11
 */

global $_GPC, $_W;

$operation   = !empty($_GPC['op']) ? $_GPC['op'] : 'display' ;

$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);

//查询成为主播条件
$anchor_limit = $this->model->getAnchorConditions();
// echo '<pre>';print_r($anchor_limit);exit;

$anchor_info = $this->model->getAnchorInfo($openid);

if ($operation == 'display') {
    //无条件
    if ($anchor_limit['conditions'] == 0) {
        if(empty($anchor_info)){
            //本地数据库存储
            $anchor_record_id = $this->model->saveLocalAnchor($openid, $member, 0);
        }

        //审核
        if ($anchor_limit['is_check'] == 0) {
            $data = array(
                'aid' => $anchor_record_id,
                'mobile' => '',
                'auth_img0' => '',
                'auth_img1' => ''
            );
            $this->model->saveAnchorRemindInfo($data);
        } else {
            //创建主播默认房间
            $room_result = $this->model->createRoom($member);
            if($room_result){
                $this->model->updateAnchorCloudData($openid, $room_result['cloud_anchor_id'], $room_result['cloud_room_id']);
            } else {
                $this->model->updateStatusAnchor($member['uid'], 0);
                show_json(-1);
            }
        }

    }
} elseif ($operation == 'post') {
    if ($_W['ispost']) {
        if (!preg_match("/^1[34578]\d{9}$/", $_GPC['memberdata']['membermobile'])) {
            show_json(-1);
        }

        if (empty($_GPC['memberdata']['auth_img0']) || empty($_GPC['memberdata']['auth_img1'])) {
            show_json(-1);
        }

        //本地数据库存储
        if (empty($anchor_info)) {
            $anchor_record_id = $this->model->saveLocalAnchor($openid, $member, 0);
        } else {
            $uid = $this->model->getUid($anchor_info['id']);
            $this->model->updateStatusAnchor($uid, 0);
            $anchor_record_id = $anchor_info['id'];
        }

        //审核材料
        $data = array(
            'aid' => $anchor_record_id,
            'mobile' => $_GPC['memberdata']['membermobile'],
            'auth_img0' => $_GPC['memberdata']['auth_img0'],
            'auth_img1' => $_GPC['memberdata']['auth_img1']
        );
        $this->model->saveAnchorRemindInfo($data);

        show_json(1);

    }
}

$template_flag  = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) {
    $set_config        = $diyform_plugin->getSet();
    $user_diyform_open = $set_config['user_diyform_open'];
    if ($user_diyform_open == 1) {
        $template_flag = 1;
        $diyform_id    = $set_config['user_diyform'];
        if (!empty($diyform_id)) {
            $formInfo     = $diyform_plugin->getDiyformInfo($diyform_id);
            $fields       = $formInfo['fields'];
            $diyform_data = iunserializer($member['diymemberdata']);
            $f_data       = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
        }
    }
}
$anchor_info = $this->model->getAnchorInfo($openid);
//如果主播之前被禁播, 如果再次能够申请, 将其状态更改为0
if ($anchor_info['status'] == 3) {
    pdo_update('sz_yi_live_anchor', array('status'=>0), array('openid'=>$openid, 'uid'=>$uid));
}
if (!empty($anchor_info) && ($anchor_info['status'] == 0)) {
    //审核中或者被禁播
    include $this->template('reminder');
    exit;
} else if (!empty($anchor_info) && $anchor_info['status'] == 2 && empty($_GPC['applyAgain'])) {
    //审核被拒
    include $this->template('reminder');
    exit;
} else if (!empty($anchor_info) && $anchor_info['status'] == 2 && !empty($_GPC['applyAgain'])) {
    //审核被拒, 再次提交申请 (如果再次提交申请sz_yi/plugin/live/template/mobile/default/reminder.html会传参applyAgain)
    include $this->template('index');
    exit;
}

//申请
if ($anchor_limit['conditions'] == 1) {
    include $this->template('index');
    /*if ($template_flag == 1 && !$_GPC['withdraw']) {
        include $this->template('diyform/anchor');
    } else {
        include $this->template('index');
    }*/
}




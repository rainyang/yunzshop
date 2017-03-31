<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/19
 * Time: 上午9:47
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = $_GPC['op'] ? $_GPC['op'] : 'display';

if ($operation == 'display') {
    $uid = $_GPC['id'];

    if(!empty($uid)) {
        $info = $this->model->getAnchorApplyInfo($uid);

        if ($_W['ispost']) {
            $status = $_GPC['status'];

            if ($status == 1) {
                if ($this->model->updateStatusAnchor($uid, $status)) {
                    $openid = $this->model->getAnchorOpenid($uid);
                    //创建房间
                    if (!empty($openid)) {
                        $member = m('member')->getMember($openid);

                        $room_result = $this->model->createRoom($member);
                        if(!empty($room_result)){
                            $this->model->updateAnchorCloudData($openid, $room_result['cloud_anchor_id'], $room_result['cloud_room_id']);
                        } else{
                            $this->model->updateStatusAnchor($uid, 0);
                            message('主播审核更新失败！', $this->createPluginWebUrl('live/index', array('status'=>0)), 'error');
                        }
                    }

                } else {
                    message('主播审核更新失败！', $this->createPluginWebUrl('live/index', array('status'=>0)), 'error');
                }
            } else if ($status == 2){
                if (!$this->model->updateStatusAnchor($uid, $status)) {
                    message('主播审核更新失败！', $this->createPluginWebUrl('live/index', array('status'=>0)), 'error');
                }
            }

            message('主播审核更新成功！', $this->createPluginWebUrl('live/index', array('status'=>0)), 'success');
        }
    } else {
        message('主播审核更新失败！', $this->createPluginWebUrl('live/index', array('status'=>0)), 'error');
    }

} elseif ($operation == 'ajax') {
    $openid = $_GPC['openid'];

    if (!empty($openid)) {
        $member = m('member')->getMember($openid);

        $this->model->saveLocalAnchor($openid, $member, 1);

        if (!$this->model->createRoom($member)) {
            $this->model->updateStatusAnchor($member['uid'], 0);
            show_json(0);
        }

        show_json(1);
    } else {
        show_json(0);
    }


}



load()->func('tpl');
include $this->template('apply');
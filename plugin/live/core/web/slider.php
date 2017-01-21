<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/8
 * Time: 下午3:46
 */
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

if ($operation == 'display') {
    ca('live.view');
    if (!empty($_GPC['displayorder'])) {
        ca('live.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update('sz_yi_live_banner', array(
                'displayorder' => $displayorder
            ), array(
                'id' => $id
            ));
        }
        plog('live.edit', '批量修改幻灯片的排序');
        message('分类排序更新成功！', $this->createWebUrl('plugin/live', array(
            'method'=>'slider',
            'op' => 'display'
        )), 'success');
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_live_banner') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('live.add');
    } else {
        ca('live.edit|live.view');
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'advname' => trim($_GPC['advname']),
            'link' => trim($_GPC['link']),
            'enabled' => intval($_GPC['enabled']),
            'displayorder' => intval($_GPC['displayorder']),
            'thumb' => $_GPC['thumb']
        );
        if (!empty($id)) {
            if (pdo_update('sz_yi_live_banner', $data, array('id' => $id)) === FALSE) {
                message('抱歉,幻灯片上传失败！', $this->createWebUrl('plugin/live', array(
                    'method'=>'slider',
                    'op' => 'display'
                )), 'error');
            }

            plog('live.edit', "修改幻灯片 ID: {$id}");
        } else {
            if (pdo_insert('sz_yi_live_banner', $data) === FALSE) {
                message('抱歉,幻灯片上传失败！', $this->createWebUrl('plugin/live', array(
                    'method'=>'slider',
                    'op' => 'display'
                )), 'error');
            }

            $id = pdo_insertid();
            plog('live.add', "添加幻灯片 ID: {$id}");
        }
        

        message('更新幻灯片成功！', $this->createWebUrl('plugin/live', array(
                'method'=>'slider',
                'op' => 'display'
        )), 'success');


    }
    $item = pdo_fetch("select * from " . tablename('sz_yi_live_banner') . " where id=:id and uniacid=:uniacid limit 1", array(
        ":id" => $id,
        ":uniacid" => $_W['uniacid']
    ));
} elseif ($operation == 'delete') {
    ca('live.delete');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,advname FROM " . tablename('sz_yi_live_banner') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)) {
        message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('plugin/live', array(
            'method' => 'slider',
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('sz_yi_live_banner', array(
        'id' => $id
    ));
    plog('live.delete', "删除幻灯片 ID: {$id} 标题: {$item['advname']} ");
    message('幻灯片删除成功！', $this->createWebUrl('plugin/live', array(
        'method'=>'slider',
        'op' => 'display'
    )), 'success');
}

load()->func('tpl');
include $this->template('slider');

<?php
/*=============================================================================
#     FileName: adv.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:39:14
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    ca('recharge.adv');
    if (!empty($_GPC['displayorder'])) {
        ca('recharge.adv');
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update('sz_yi_recharge_adv', array(
                'displayorder' => $displayorder
            ), array(
                'id' => $id
            ));
        }
        plog('recharge.adv', '批量修改幻灯片的排序');
        message('分类排序更新成功！', $this->createPluginWebUrl('recharge/adv', array(
            'op' => 'display'
        )), 'success');
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_recharge_adv') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('recharge.adv');
    } else {
        ca('recharge.adv');
    }
    if (checksubmit('submit')) {
        //print_r($_GPC);exit;
        $data = array(
            'uniacid' => $_W['uniacid'],
            'advname' => trim($_GPC['advname']),
            'link' => trim($_GPC['link']),
            'isshow' => intval($_GPC['isshow']),
            'displayorder' => intval($_GPC['displayorder']),
            'thumb' => save_media($_GPC['thumb']),
            'createtime' => time()
        );
        if (!empty($id)) {
            pdo_update('sz_yi_recharge_adv', $data, array(
                'id' => $id
            ));
            plog('recharge.adv', "修改幻灯片 ID: {$id}");
        } else {
            pdo_insert('sz_yi_recharge_adv', $data);
            $id = pdo_insertid();
            plog('recharge.adv', "添加幻灯片 ID: {$id}");
        }
        message('更新幻灯片成功！', $this->createPluginWebUrl('recharge/adv', array(
            'op' => 'display'
        )), 'success');
    }
    $item = pdo_fetch("select * from " . tablename('sz_yi_recharge_adv') . " where id=:id and uniacid=:uniacid limit 1", array(
        ":id" => $id,
        ":uniacid" => $_W['uniacid']
    ));
} elseif ($operation == 'delete') {
    ca('shop.adv');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,advname FROM " . tablename('sz_yi_recharge_adv') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)) {
        message('抱歉，幻灯片不存在或是已经被删除！', $this->createPluginWebUrl('recharge/adv', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('sz_yi_recharge_adv', array(
        'id' => $id
    ));
    plog('shop.adv', "删除幻灯片 ID: {$id} 标题: {$item['advname']} ");
    message('幻灯片删除成功！', $this->createPluginWebUrl('recharge/adv', array(
        'op' => 'display'
    )), 'success');
}
load()->func('tpl');
include $this->template('adv');

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
    ca('shop.banner.view');
    if (!empty($_GPC['displayorder'])) {
        ca('shop.banner.edit');
        foreach ($_GPC['displayorder'] as $id => $displayorder) {
            pdo_update('sz_yi_banner', array(
                'displayorder' => $displayorder
            ), array(
                'id' => $id
            ));
        }
        plog('shop.banner.edit', '批量修改广告的排序');
        message('分类排序更新成功！', $this->createWebUrl('shop/banner', array(
            'op' => 'display'
        )), 'success');
    }
    $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_banner') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
} elseif ($operation == 'post') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        ca('shop.banner.add');
    } else {
        ca('shop.banner.edit|shop.banner.view');
    }
    if (checksubmit('submit')) {
        $data = array(
            'uniacid' => $_W['uniacid'],
            'advname' => trim($_GPC['advname']),
            'link' => trim($_GPC['link']),
            'enabled' => intval($_GPC['enabled']),
            'displayorder' => intval($_GPC['displayorder']),
            'thumb' => save_media($_GPC['thumb'])
        );
        if (!empty($id)) {
            pdo_update('sz_yi_banner', $data, array(
                'id' => $id
            ));
            plog('shop.banner.edit', "修改广告 ID: {$id}");
        } else {
            pdo_insert('sz_yi_banner', $data);
            $id = pdo_insertid();
            plog('shop.banner.add', "添加广告 ID: {$id}");
        }
        message('更新广告成功！', $this->createWebUrl('shop/banner', array(
            'op' => 'display'
        )), 'success');
    }
    $item = pdo_fetch("select * from " . tablename('sz_yi_banner') . " where id=:id and uniacid=:uniacid limit 1", array(
        ":id" => $id,
        ":uniacid" => $_W['uniacid']
    ));
} elseif ($operation == 'delete') {
    ca('shop.banner.delete');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id,advname FROM " . tablename('sz_yi_banner') . " WHERE id = '$id' AND uniacid=" . $_W['uniacid'] . "");
    if (empty($item)) {
        message('抱歉，广告不存在或是已经被删除！', $this->createWebUrl('shop/banner', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('sz_yi_banner', array(
        'id' => $id
    ));
    plog('shop.banner.delete', "删除广告 ID: {$id} 标题: {$item['advname']} ");
    message('广告删除成功！', $this->createWebUrl('shop/banner', array(
        'op' => 'display'
    )), 'success');
}
load()->func('tpl');
include $this->template('web/shop/banner');

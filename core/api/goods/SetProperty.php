<?php
/**
 * 管理后台APP API商品状态设置接口
 *
 * PHP version 5.6.15
 *
 * @package   商品模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
ca('shop.goods.edit');
$id   = intval($_GPC['id']);
$type = $_GPC['type'];
$data = intval($_GPC['data']);
if (in_array($type, array(
    'new',
    'hot',
    'recommand',
    'discount',
    'time',
    'sendfree',
    'nodiscount'
))) {
    $data = ($data == 1 ? '0' : '1');
    pdo_update('sz_yi_goods', array(
        'is' . $type => $data
    ), array(
        "id" => $id,
        "uniacid" => $_W['uniacid']
    ));
    if ($type == 'new') {
        $typestr = "新品";
    } else if ($type == 'hot') {
        $typestr = "热卖";
    } else if ($type == 'recommand') {
        $typestr = "推荐";
    } else if ($type == 'discount') {
        $typestr = "促销";
    } else if ($type == 'time') {
        $typestr = "限时卖";
    } else if ($type == 'sendfree') {
        $typestr = "包邮";
    } else if ($type == 'nodiscount') {
        $typestr = "不参与折扣状态";
    }
    plog('shop.goods.edit', "修改商品{$typestr}状态   ID: {$id}");
    $_YZ->returnSuccess($data);
}
if (in_array($type, array(
    'status'
))) {
    $data = ($data == 1 ? '0' : '1');
    pdo_update('sz_yi_goods', array(
        $type => $data
    ), array(
        "id" => $id,
        "uniacid" => $_W['uniacid']
    ));
    plog('shop.goods.edit', "修改商品上下架状态   ID: {$id}");
    $_YZ->returnSuccess($data);
}
if (in_array($type, array(
    'type'
))) {
    $data = ($data == 1 ? '2' : '1');
    pdo_update('sz_yi_goods', array(
        $type => $data
    ), array(
        "id" => $id,
        "uniacid" => $_W['uniacid']
    ));
    plog('shop.goods.edit', "修改商品类型   ID: {$id}");
    $_YZ->returnSuccess($data);
}
$_YZ->returnError();
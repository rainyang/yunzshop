<?php
global $_W, $_GPC;
//QQ:834633039
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
if ($operation == "display") {
    ca("shop.refundaddress.view");
    $list = pdo_fetchall("SELECT * FROM " . tablename("sz_yi_refund_address") . " WHERE uniacid = '{$_W["uniacid"]}' and deleted = 0 ORDER BY id asc");
} elseif ($operation == "post") {
    $id = intval($_GPC["id"]);
    if (empty($id)) {
        ca("shop.refundaddress.add");
    } else {
        ca("shop.refundaddress.edit|shop.refundaddress.view");
    }
    if (checksubmit("submit")) {
        $data = array();
        $data["uniacid"] = $_W["uniacid"];
        $data["title"] = trim($_GPC["title"]);
        $data["name"] = trim($_GPC["name"]);
        $data["tel"] = trim($_GPC["tel"]);
        $data["mobile"] = trim($_GPC["mobile"]);
        $data["zipcode"] = trim($_GPC["zipcode"]);
        $data["province"] = trim($_GPC["province"]);
        $data["city"] = trim($_GPC["city"]);
        $data["area"] = trim($_GPC["area"]);
        $data["address"] = trim($_GPC["address"]);
        $data["isdefault"] = $_GPC["isdefault"];
        if ($data["isdefault"]) {
            pdo_update("sz_yi_refund_address", array("isdefault" => 0), array("uniacid" => $_W["uniacid"]));
        }
        if (!empty($id)) {
            plog("shop.refundaddress.edit", "修改退货地址 ID: {$id}");
            pdo_update("sz_yi_refund_address", $data, array("id" => $id));
        } else {
            pdo_insert("sz_yi_refund_address", $data);
            $id = pdo_insertid();
            plog("shop.refundaddress.add", "添加退货地址 ID: {$id}");
        }
        message("更新退货地址成功！", $this->createWebUrl("shop/refundaddress", array("op" => "display")), "success");
    }
    if (!empty($id)) {
        $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_refund_address") . " WHERE id = '{$id}' and uniacid = '{$_W["uniacid"]}'");
        if (!empty($item)) {
        }
    }
} elseif ($operation == "delete") {
    ca("shop.refundaddress.delete");
    $id = intval($_GPC["id"]);
    $item = pdo_fetch("SELECT id,title FROM " . tablename("sz_yi_refund_address") . " WHERE id = '{$id}' AND uniacid=" . $_W["uniacid"] . "");
    if (empty($item)) {
        message("抱歉，退货地址不存在或是已经被删除！", $this->createWebUrl("shop/refundaddress", array("op" => "display")), "error");
    }
    pdo_delete("sz_yi_refund_address", array("id" => $id));
    plog("shop.refundaddress.delete", "删除退货地址 ID: {$id} 名称: {$item["title"]} ");
    message("退货地址删除成功！", $this->createWebUrl("shop/refundaddress", array("op" => "display")), "success");
}
include $this->template("web/shop/refundaddress");
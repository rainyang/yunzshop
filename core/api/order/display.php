<?php

//$api->validate('username','password');
$_YZ->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
//END
$pindex = max(1, intval($_GPC["page"]));
$psize = 20;
$status = $_GPC["status"];
$sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
$paras1 = array(
    ":uniacid" => $_W["uniacid"]
);

$order_list = $_YZ->m('order')->getList(
    array(
        'status'=>$status,
        'paytype'=>$_GPC["paytype"],
        'is_supplier_uid'=>$_YZ->isSupplier()
    )
);



dump($order_list);
$_YZ->returnSuccess($order_list);
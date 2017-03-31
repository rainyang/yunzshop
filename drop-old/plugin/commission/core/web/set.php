<?php
global $_W, $_GPC;

ca('commission.set');
$set = $this->getSet();
if (checksubmit('submit')) {
    $data          = is_array($_GPC['setdata']) ? array_merge($set, $_GPC['setdata']) : array();
    $data['texts'] = is_array($_GPC['texts']) ? $_GPC['texts'] : array();
    //Author:ymg Date:2017-01-16 Content:开启提现免审核功能之前的数据问题调整
    if (empty($set['credit_avoid_audit']) && $data['credit_avoid_audit'] == 1) {
        //查询所有提现免审核的订单数据错误的数据
        $apply = pdo_fetchall("select * from " . tablename("sz_yi_commission_apply") . " where status=3 and checktime=0 and uniacid=:uniacid", array(":uniacid" => $_W['uniacid']));

        foreach ($apply as $row) {
            //提现表中审核时间判断及添加
            if ($row['checktime'] == 0) {
                pdo_update('sz_yi_commission_apply', array('checktime' => $row['paytime'], 'payauto' => 1), array('id' => $row['id'], 'uniacid' => $_W['uniacid']));
                $row['checktime'] = $row['paytime'];
            }
            //反序列化提现表中提现订单数据进行处理
            $orderids = unserialize($row['orderids']);
            foreach ($orderids as $value) {
                $status = pdo_fetchcolumn("select status" . $value['level'] . " from " . tablename("sz_yi_order_goods") . " where orderid=:orderid", array("orderid" => $value['orderid'], 'uniacid' => $_W['uniacid']));
                if ($status < 2) {
                    pdo_update("sz_yi_order_goods", array('status' . $value['level'] => 3, 'checktime' . $value['level'] => $row['checktime'], 'paytime' . $value['level'] =>  $row['paytime']), array('orderid' => $value['orderid'], 'uniacid' => $_W['uniacid']));
                }
            }
        }
    }
    $this->updateSet($data);
    m('cache')->set('template_' . $this->pluginname, $data['style']);
    plog('commission.set', '修改基本设置');
    message('设置保存成功!', referer(), 'success');
}
$styles = array();
$dir    = IA_ROOT . "/addons/sz_yi/plugin/" . $this->pluginname . "/template/mobile/";
if ($handle = opendir($dir)) {
    while (($file = readdir($handle)) !== false) {
        if ($file != ".." && $file != ".") {
            if (is_dir($dir . "/" . $file)) {
                $styles[] = $file;
            }
        }
    }
    closedir($handle);
}
//Author:Y.yang Date:2016-04-08 Content:成为分销商条件（购买条件）
$goods = false;
if (!empty($set['become_goodsid'])) {
    $goods = pdo_fetch('select id,title from ' . tablename('sz_yi_goods') . ' where id=:id and uniacid=:uniacid limit 1 ', array(
        ':id' => $set['become_goodsid'],
        ':uniacid' => $_W['uniacid']
    ));
}
// END



// END
load()->func('tpl');
include $this->template('set');

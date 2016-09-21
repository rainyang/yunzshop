<?php
//金额不能用int, apply表少uniacid字段
global $_W;
$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_af_supplier') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8 NOT NULL,
  `uniacid` int(11) NOT NULL,
  `realname` varchar(55) CHARACTER SET utf8 NOT NULL,
  `mobile` varchar(255) CHARACTER SET utf8 NOT NULL,
  `weixin` varchar(255) CHARACTER SET utf8 NOT NULL,
  `productname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(3) NOT NULL COMMENT '1审核成功2驳回',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_supplier_apply') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '供应商id',
  `uniacid` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1手动2微信',
  `applysn` varchar(255) NOT NULL COMMENT '提现单号',
  `apply_money` int(11) NOT NULL COMMENT '申请金额',
  `apply_time` int(11) NOT NULL COMMENT '申请时间',
  `status` tinyint(3) NOT NULL COMMENT '0为申请状态1为完成状态',
  `finish_time` int(11) NOT NULL COMMENT '完成时间',
  `apply_ordergoods_ids` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_supplier_order') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `isopenbonus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
pdo_query($sql);
if(!pdo_fieldexists('sz_yi_perm_user', 'banknumber')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `banknumber` varchar(255) NOT NULL COMMENT '银行卡号';");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'accountname')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `accountname` varchar(255) NOT NULL COMMENT '开户名';");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'accountbank')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `accountbank` varchar(255) NOT NULL COMMENT '开户行';");
}

if(!pdo_fieldexists('sz_yi_goods', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_goods')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'supplier_uid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `supplier_uid` INT NOT NULL COMMENT '供应商ID';");
}
if(!pdo_fieldexists('sz_yi_order_goods', 'supplier_apply_status')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_order_goods')." ADD `supplier_apply_status` tinyint(4) NOT NULL COMMENT '1为供应商已提现';");
}
if(!pdo_fieldexists('sz_yi_af_supplier', 'id')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD PRIMARY KEY (`id`);");
}
if(!pdo_fieldexists('sz_yi_supplier_apply', 'id')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD PRIMARY KEY (`id`);");
}
if (!pdo_fieldexists('sz_yi_supplier_apply', 'apply_ordergoods_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD  `apply_ordergoods_ids` text;");
}
if(!pdo_fieldexists('sz_yi_af_supplier', 'id')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_supplier')." MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;");
}
if(!pdo_fieldexists('sz_yi_supplier_apply', 'id')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_supplier_apply')." MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('sz_yi_supplier_apply', 'uniacid')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD `uniacid` int(11) NOT NULL DEFAULT '0';");
}
//供应商分账号uniacid
$suppliers = pdo_fetchall("select uniacid,uid from " . tablename('sz_yi_perm_user') . " where status=1 and roleid=(select id from " . tablename('sz_yi_perm_role') . " where status=1 and status1=1 )");
if (!empty($suppliers)) {
  foreach ($suppliers as $value) {
    $now_sup_apply_ids = pdo_fetchall("select id from " . tablename('sz_yi_supplier_apply') . " where uid={$value['uid']}");
    if (!empty($now_sup_apply_ids)) {
      foreach ($now_sup_apply_ids as $val) {
        pdo_update('sz_yi_supplier_apply', array('uniacid' => $value['uniacid']), array('id' => $val['id']));
      }
    }
  }
}
if(!pdo_fieldexists('sz_yi_perm_role', 'status1')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_role')." ADD `status1` tinyint(3) NOT NULL COMMENT '1：供应商开启';");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'openid')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `openid` VARCHAR( 255 ) NOT NULL;");
}
if(!pdo_fieldexists('sz_yi_perm_user', 'username')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
} 
if(!pdo_fieldexists('sz_yi_perm_user', 'password')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_perm_user')." ADD `username` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "supplier"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'supplier', '供应商', '1.0', '官方', 1, 'biz');";
    pdo_query($sql);
}

if(!pdo_fieldexists('sz_yi_af_supplier', 'status')) {
  pdo_query("ALTER TABLE ".tablename('sz_yi_af_supplier')." ADD `status` TINYINT( 3 ) NOT NULL COMMENT '0申请1驳回2通过' AFTER `productname`;");
}

if (!pdo_fieldexists('sz_yi_supplier_apply', 'apply_ordergoods_ids')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_supplier_apply')." ADD  `apply_ordergoods_ids` text;");
}

$result = pdo_fetch('select * from ' . tablename('sz_yi_perm_role') . ' where status1=1');
if(empty($result)){
    $sql = "
INSERT INTO " . tablename('sz_yi_perm_role') . " (`rolename`, `status`, `status1`, `perms`, `deleted`) VALUES
('供应商', 1, 1, 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,shop.dispatch,shop.dispatch.view,shop.dispatch.add,shop.dispatch.edit,shop.dispatch.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosend,taobao,taobao.fetch,coupon,coupon.coupon,coupon.coupon.add,coupon.coupon.edit,coupon.coupon.view,coupon.coupon.delete,coupon.coupon.send,coupon.category,coupon.category.view,coupon.category.add,coupon.category.edit,coupon.category.delete,coupon.log,coupon.log.view,coupon.log.export', 0);";
    pdo_query($sql);
}else{
    $gysdata = array("perms" => 'shop,shop.goods,shop.goods.view,shop.goods.add,shop.goods.edit,shop.goods.delete,shop.dispatch,shop.dispatch.view,shop.dispatch.add,shop.dispatch.edit,shop.dispatch.delete,order,order.view,order.view.status_1,order.view.status0,order.view.status1,order.view.status2,order.view.status3,order.view.status4,order.view.status5,order.view.status9,order.op,order.op.pay,order.op.send,order.op.sendcancel,order.op.finish,order.op.verify,order.op.fetch,order.op.close,order.op.refund,order.op.export,order.op.changeprice,exhelper,exhelper.print,exhelper.print.single,exhelper.print.more,exhelper.exptemp1,exhelper.exptemp1.view,exhelper.exptemp1.add,exhelper.exptemp1.edit,exhelper.exptemp1.delete,exhelper.exptemp1.setdefault,exhelper.exptemp2,exhelper.exptemp2.view,exhelper.exptemp2.add,exhelper.exptemp2.edit,exhelper.exptemp2.delete,exhelper.exptemp2.setdefault,exhelper.senduser,exhelper.senduser.view,exhelper.senduser.add,exhelper.senduser.edit,exhelper.senduser.delete,exhelper.senduser.setdefault,exhelper.short,exhelper.short.view,exhelper.short.save,exhelper.printset,exhelper.printset.view,exhelper.printset.save,exhelper.dosend,taobao,taobao.fetch,coupon,coupon.coupon,coupon.coupon.add,coupon.coupon.edit,coupon.coupon.view,coupon.coupon.delete,coupon.coupon.send,coupon.category,coupon.category.view,coupon.category.add,coupon.category.edit,coupon.category.delete,coupon.log,coupon.log.view,coupon.log.export');
    pdo_update('sz_yi_perm_role', $gysdata, array('rolename' => "供应商", 'status1' => 1));
}
$roleid = pdo_fetchcolumn("SELECT id FROM " . tablename('sz_yi_perm_role') . " WHERE status1=1");
if (!empty($roleid)) {
    $uids = pdo_fetchall("SELECT uid,uniacid FROM " . tablename('sz_yi_perm_user') . " WHERE roleid=:roleid AND status=:status", array(':roleid' => $roleid, 'status' => 1));
    if (!empty($uids)) {
        foreach ($uids as $value) {
            $permission = pdo_fetch("SELECT * FROM " . tablename('users_permission') . " WHERE uid=:uid AND uniacid=:uniacid", array(':uid' => $value['uid'], 'uniacid' => $value['uniacid']));
            if (empty($permission)) {
                $data = array(
                  'uniacid'     => $value['uniacid'],
                  'uid'         => $value['uid'],
                  'type'        => 'sz_yi',
                  'permission'  => 'sz_yi_menu_shop|sz_yi_menu_order|sz_yi_menu_plugins'
                  );
                pdo_insert('users_permission', $data);
            }
        }
    }
}
message('供应商插件安装成功', $this->createPluginWebUrl('supplier/supplier'), 'success');


<?php
/*=============================================================================
#     FileName: list.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:39:02
#      History:
=============================================================================*/
global $_W, $_GPC;
$cond = '';
if (p('supplier')) {
    $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
    if($perm_role != 0){
        $cond = " and identity in ('exhelper','taobao','coupon') ";
    }
}
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$category = m('plugin')->getCategory();
foreach ($category as $ck => &$cv) {
	$cv['plugins'] = pdo_fetchall('select * from ' . tablename('sz_yi_plugin') . " where category=:category $cond order by displayorder asc", array(':category' => $ck));
}
unset($cv);
//公众号权限设置查询
$acid_plugins = pdo_fetchcolumn('select plugins from ' . tablename('sz_yi_perm_plugin') . ' where acid=:uniacid',array(":uniacid" => $_W['uniacid']));
if(!empty($acid_plugins)){
	$plugins_data = explode(',', $acid_plugins);
}
include $this->template('web/plugins/list');
exit;

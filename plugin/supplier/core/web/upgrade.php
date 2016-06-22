<?php
global $_W;
$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "merchant"  order by id desc limit 1');
if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'merchant', '招商员', '1.0', '官方', 1, 'biz');";
    pdo_query($sql);
}
message('招商员插件安装成功', $this->createPluginWebUrl('merchant/merchant'), 'success');

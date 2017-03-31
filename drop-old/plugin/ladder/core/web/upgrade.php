<?php
global $_W;
load()->func('file');

$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'ladder'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'ladder','阶梯价格','1.0','官方','1','help');";
  pdo_fetchall($sql);
}


pdo_fetchall("CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_goods_ladder')." (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `goodsid` int(11) NOT NULL,
  `ladders` text NOT NULL COMMENT '阶梯价格',
  `times` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE ".tablename('sz_yi_goods_ladder')."
  ADD PRIMARY KEY (`id`);
ALTER TABLE ".tablename('sz_yi_goods_ladder')."
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");




@rmdirs(IA_ROOT. "/data/tpl/app/sz_yi");

message('阶梯价格插件安装成功', $this->createPluginWebUrl('ladder/set'), 'success');

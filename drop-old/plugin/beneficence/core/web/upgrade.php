<?php
global $_W;

$result = pdo_fetchcolumn('select id from ' . tablename('sz_yi_plugin') . ' where identity=:identity', array(':identity' => 'beneficence'));
if(empty($result)){
    $displayorder_max = pdo_fetchcolumn('select max(displayorder) from ' . tablename('sz_yi_plugin'));
    $displayorder = $displayorder_max + 1;
    $sql = "INSERT INTO " . tablename('sz_yi_plugin') . " (`displayorder`,`identity`,`name`,`version`,`author`,`status`,`category`) VALUES(". $displayorder .",'beneficence','行善池','1.0','官方','1','help');";
  pdo_fetchall($sql);
}

$sql = "CREATE TABLE IF NOT EXISTS " . tablename('sz_yi_beneficence') . " (
  `id` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `create_time` int(11) NOT NULL,
  `money` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE " . tablename('sz_yi_beneficence') . " ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('sz_yi_beneficence') . " MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE " . tablename('sz_yi_beneficence') . " ADD UNIQUE KEY `id` (`id`);
";
pdo_query($sql);


message('行善池插件安装成功', $this->createPluginWebUrl('beneficence/set'), 'success');


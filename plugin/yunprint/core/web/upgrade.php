<?php

$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "yunprint"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'yunprint', '云打印', '1.0', '官方', 1, 'tool');";
    pdo_query($sql);
}

message('云打印插件安装成功', $this->createPluginWebUrl('yunprint/set'), 'success');
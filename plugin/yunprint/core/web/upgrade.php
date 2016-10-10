<?php
$sql = "
CREATE TABLE IF NOT EXISTS ".tablename('sz_yi_yunprint_list') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `print_no` varchar(30) CHARACTER SET utf8 NOT NULL,
  `key` varchar(30) CHARACTER SET utf8 NOT NULL,
  `print_nums` int(3) NOT NULL,
  `status` int(3) NOT NULL,
  `mode` int(11) NOT NULL,
  `member_code` varchar(50) CHARACTER SET utf8 NOT NULL,
  `qrcode_link` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
";
pdo_query($sql);
$info = pdo_fetch('select * from ' . tablename('sz_yi_plugin') . ' where identity= "yunprint"  order by id desc limit 1');

if(!$info){
    $sql = "INSERT INTO " . tablename('sz_yi_plugin'). " (`displayorder`, `identity`, `name`, `version`, `author`, `status`, `category`) VALUES(0, 'yunprint', '云打印', '1.0', '官方', 1, 'tool');";
    pdo_query($sql);
}

message('云打印插件安装成功', $this->createPluginWebUrl('yunprint/set'), 'success');
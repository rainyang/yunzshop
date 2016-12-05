<?php
if (!pdo_fieldexists('sz_yi_member', 'membermobile')) {
    pdo_query("ALTER TABLE ".tablename('sz_yi_member')." ADD `membermobile` VARCHAR(11) DEFAULT '' COMMENT '会员资料手机号';");

    $sql = "UPDATE ".tablename(sz_yi_member)." SET membermobile = mobile, mobile = '' WHERE mobile <> '' AND PWD IS NULL ;";
    pdo_query($sql);
}
echo  "运行成功";

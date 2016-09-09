<?php
if(!pdo_fieldexists('sz_yi_bonus_log', 'type')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_log')." ADD `type` tinyint(1) DEFAULT '0';");
}
if(!pdo_fieldexists('sz_yi_bonus', 'bonus_area')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus')." ADD `bonus_area` tinyint(1) DEFAULT '0';");
}
echo 1;
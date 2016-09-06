<?php
if(!pdo_fieldexists('sz_yi_bonus_log', 'type')) {
  pdo_fetchall("ALTER TABLE ".tablename('sz_yi_bonus_log')." ADD `type` tinyint(1) DEFAULT '0';");
}
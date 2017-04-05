<?php

global $_W, $_GPC;
session_start();
$_SESSION['helper'] = true;
message('', $this->createPluginWebUrl('article', array('is_helper' => 1)), 'success');

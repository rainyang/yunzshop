<?php
namespace Api;
define("__API_ROOT__",__DIR__);
define("__BASE_ROOT__",__DIR__."/../../../..");

require_once __BASE_ROOT__.'/framework/bootstrap.inc.php';
require_once __BASE_ROOT__.'/addons/sz_yi/defines.php';
require_once __BASE_ROOT__.'/addons/sz_yi/core/inc/functions.php';
require_once __BASE_ROOT__.'/addons/sz_yi/core/inc/plugin/plugin_model.php';
require_once __BASE_ROOT__.'/addons/sz_yi/core/inc/aes.php';
global $_W, $_GPC, $_YZ;
require_once __API_ROOT__.'/YZ.class.php';
$_YZ = new YZ($_W);
require __API_ROOT__."/{$_GET['api']}.php";
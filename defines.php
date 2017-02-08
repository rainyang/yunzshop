<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}


define('SZ_YI_DEBUG', false);//false
!defined('SZ_YI_PATH') && define('SZ_YI_PATH', IA_ROOT . '/addons/sz_yi/');
!defined('SZ_YI_CORE') && define('SZ_YI_CORE', SZ_YI_PATH . 'core/');
!defined('SZ_YI_PLUGIN') && define('SZ_YI_PLUGIN', SZ_YI_PATH . 'plugin/');
!defined('SZ_YI_INC') && define('SZ_YI_INC', SZ_YI_CORE . 'inc/');
!defined('SZ_YI_URL') && define('SZ_YI_URL', $_W['siteroot'] . 'addons/sz_yi/');
!defined('SZ_YI_STATIC') && define('SZ_YI_STATIC', SZ_YI_URL . 'static/');
!defined('SZ_YI_PREFIX') && define('SZ_YI_PREFIX', 'sz_yi_');
!defined('SZ_YI_INTEGRAL') &&define("SZ_YI_INTEGRAL", "积分");
!defined('SZ_YI_EXPORT') &&define("SZ_YI_EXPORT", "1000");
!defined('SZ_YI_PSIZE') &&define("SZ_YI_PSIZE", 20);
!defined('SZ_YI_EXPRESS_URL') &&define("SZ_YI_EXPRESS_URL", "https://m.kuaidi100.com/query?type=%s&postid=%s&id=1&valicode=&temp=%s");
!defined('SZ_YI_LIVE_CLOUD_URL') &&define("SZ_YI_LIVE_CLOUD_URL", "http://sy.yunzshop.com"); //直播云端的API
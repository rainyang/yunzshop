<?php
/**
 * 芸众商城模块微站定义
 *
 * @url http://bbs.yunzshop.com/
 */
defined('IN_IA') or exit('Access Denied');

require_once IA_ROOT . '/addons/sz_yi/version.php';
require_once IA_ROOT . '/addons/sz_yi/defines.php';
require_once SZ_YI_INC . 'functions.php';
require_once SZ_YI_INC . 'core.php';
require_once SZ_YI_INC . 'plugin/plugin.php';
require_once SZ_YI_INC . 'plugin/plugin_model.php';

include __DIR__ . '/app/laravel.php';
include __DIR__ . '/app/yunshop.php';
exit;


class Sz_yiModuleSite extends Core
{

    public function __construct()
    {
        parent::__construct();
    }

    //插件web入口  
    public function doWebPlugin()
    {
        global $_W, $_GPC;
        require_once SZ_YI_INC . "plugin/plugin.php";
        $plugins = m('plugin')->getAll();
        $p = $_GPC['p'];
        $file = SZ_YI_PLUGIN . $p . "/web.php";
        if (!is_file($file)) {
            message('未找到插件 ' . $plugins[$p] . ' 入口方法');
        }
        require $file;
        $pluginClass = ucfirst($p) . "Web";
        $plug = new $pluginClass($p);
        $method = strtolower($_GPC['method']);
        if (empty($method)) {
            $plug->index();
            exit;
        }
        if (method_exists($plug, $method)) {
            $plug->$method();
            exit;
        }
        trigger_error('Plugin Web Method ' . $method . ' not Found!');
    }

    //插件app入口
    public function doMobilePlugin()
    {
        global $_W, $_GPC;
        require_once SZ_YI_INC . "plugin/plugin.php";
        $plugins = m('plugin')->getAll();
        $p = $_GPC['p'];
        $file = SZ_YI_PLUGIN . $p . "/mobile.php";

        if (!is_file($file)) {
            message('未找到插件 ' . $plugins[$p] . ' 入口方法');
        }
        require $file;
        $pluginClass = ucfirst($p) . "Mobile";
        $plug = new $pluginClass($p);
        $method = strtolower($_GPC['method']);
        if (empty($method)) {
            return $plug->index();
        } elseif (method_exists($plug, $method)) {

            return $plug->$method();
        } else {
            trigger_error('Plugin Mobile Method ' . $method . ' not Found!');
            exit;
        }
    }

    //购物车入口
    public function doMobileCart()
    {
        return $this->_exec('doMobileShop', 'cart', false);
    }

    //我的收藏入口
    public function doMobileFavorite()
    {
        return $this->_exec('doMobileShop', 'favorite', false);
    }

    //工具
    public function doMobileUtil()
    {
        return $this->_exec(__FUNCTION__, '', false);
    }

    //会员
    public function doMobileMember()
    {
        return $this->_exec(__FUNCTION__, 'center', false);
    }

    //商城
    public function doMobileShop()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //订单
    public function doMobileOrder()
    {
        return $this->_exec(__FUNCTION__, 'list', false);
    }

    //会议
    public function doMobileMeet()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //餐饮
    public function doMobileRest()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //接口
    public function doMobileApi()
    {
        return $this->_exec(__FUNCTION__, 'index', false);
    }

    //直播
    public function doMobileLive()
    {
        return $this->_exec(__FUNCTION__, 'list', false);
    }
    //订单
    //支付成功
    public function payResult($params)
    {
        return m('order')->payResult($params);
    }

    public function getAuthSet()
    {
        global $_W;
        $set = pdo_fetch('select sets from ' . tablename('sz_yi_sysset') . ' order by id asc  limit 1');
        $sets = iunserializer($set['sets']);
        if (is_array($sets)) {
            return is_array($sets['auth']) ? $sets['auth'] : array();
        }
        return array();
    }

    public function doWebAuth()
    {
        return $this->_exec('doWebSysset', 'auth', true);
    }

    public function doWebUpgrade()
    {
        return $this->_exec('doWebSysset', 'upgrade', true);
    }

    //微信管理订单
    public function doMobileWechatOrder()
    {
        return $this->_execFront('doWebOrder', 'list', false);
    }

}

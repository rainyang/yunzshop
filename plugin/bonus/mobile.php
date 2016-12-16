<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
function sortByCreateTime($a, $b)
{
    if ($a['createtime'] == $b['createtime']) {
        return 0;
    } else {
        return ($a['createtime'] < $b['createtime']) ? 1 : -1;
    }
}
class BonusMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('bonus');
        global $_GPC;
        $this->set = $this->getSet();
        $openid = m('user')->getOpenid();
        $isbonus = $this->model->isLevel($openid);
        if($isbonus == false && $_GPC['method'] != 'register'){
            if($_GPC['method'] == 'agent_info' || $_GPC['method'] == 'agency'){
                
            }else{
                redirect($this->createPluginMobileUrl('bonus/register'));
            }
        }
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function team()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function customer()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order_area()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function withdraw()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function apply()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function shares()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function register()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function myshop()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function log()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function agent_info()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function agency()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}

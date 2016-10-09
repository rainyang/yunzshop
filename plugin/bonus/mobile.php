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
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function team()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function customer()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order_area()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function withdraw()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function apply()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function shares()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function register()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function myshop()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function log()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function agent_info()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function agency()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}

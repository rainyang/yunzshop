<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class IndianaMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('indiana');
        $this->set = $this->getSet();
        global $_GPC;
    }
    public function index()
    {
        header('location: ' . $this->createPluginMobileUrl('indiana/goods'));

    }

    public function goods()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function confirm()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order_list()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function info()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function announced()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

}
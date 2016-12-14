<?php
//ܿ���̳� QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class CreditshopMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('creditshop');
        $this->set = $this->getSet();
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function lists()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function detail()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function log()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function creditlog()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function exchange()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
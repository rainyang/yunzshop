<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class ReturnMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('return');
        $this->set = $this->getSet();
        global $_GPC;
    }


    public function task()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function return_queue()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    } 
    public function return_log()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
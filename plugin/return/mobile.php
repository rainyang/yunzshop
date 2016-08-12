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
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function return_queue()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    } 
    public function return_log()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
}
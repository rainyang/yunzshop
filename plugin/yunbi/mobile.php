<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class YunbiMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('yunbi');
        $this->set = $this->getSet();
        global $_GPC;
    }


    public function task()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function yunbi_log()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function yunbi_trading()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
    
}
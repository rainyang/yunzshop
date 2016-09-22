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

    public function indiana_order()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
}
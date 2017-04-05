<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class LadderMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('ladder');
        $this->set = $this->getSet();
        global $_GPC;
    }

    public function index()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
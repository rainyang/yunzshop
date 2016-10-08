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
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function confirm()
    {    
        $this->_exec_plugin(__FUNCTION__, false);
    }
}
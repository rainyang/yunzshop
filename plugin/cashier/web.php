<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class CashierWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('cashier');
    }

    public function index()
    {
        global $_W;
        if (cv('cashier')) {
            header('location: ' . $this->createPluginWebUrl('cashier/store'));
            exit;
        }
    }

    public function store()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function statistics()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function withdraw()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function withdraws()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}


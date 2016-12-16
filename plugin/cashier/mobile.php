<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class CashierMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('cashier');
    }

    public function order_confirm()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function order_pay()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function store_set()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function withdraw()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function create_qrcode()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function qrcode_list()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function statistics()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}

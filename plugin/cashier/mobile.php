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
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function order_pay()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function store_set()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function withdraw()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function create_qrcode()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function qrcode_list()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function statistics()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}

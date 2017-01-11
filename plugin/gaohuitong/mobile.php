<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class gaohuitongMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('gaohuitong');
    }

    public function index()
    {
        return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function returnpay()
    {
        return $this->_exec_plugin(__FUNCTION__, false);
    }
}
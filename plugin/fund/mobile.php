<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class FundMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('fund');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function detail()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}

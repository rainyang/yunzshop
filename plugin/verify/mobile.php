<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class VerifyMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('verify');
    }
    public function check()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function complete()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function qrcode()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function detail()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function mystore()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function add()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function order()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function withdraw()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function log()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function my_pocket()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function ranking()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function select_category()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function select_goods()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function store_index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function store_list()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function store_detail()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
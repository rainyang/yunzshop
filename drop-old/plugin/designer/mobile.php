<?php
//ܿ���̳� QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class DesignerMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('designer');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function api()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function date()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
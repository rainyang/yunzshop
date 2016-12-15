<?php
//Ü¿ÖÚÉÌ³Ç QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class ChooseMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('choose');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function list_category()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function list_goods()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function cart()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    // public function api()
    // {
    // 	return $this->_exec_plugin(__FUNCTION__, false);
    // }
}
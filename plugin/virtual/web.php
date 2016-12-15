<?php
//ܿ���̳� QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class VirtualWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('virtual');
    }
    public function index()
    {
        if (cv('virtual.temp')) {
            header('location: ' . $this->createPluginWebUrl('virtual/temp'));
            exit;
        } else if (cv('virtual.category')) {
            header('location: ' . $this->createPluginWebUrl('virtual/category'));
            exit;
        }
    }
    public function temp()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function data()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function category()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function import()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function export()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
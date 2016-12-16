<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class TaobaoWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('taobao');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function fetch()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function jingdong()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function one688()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function kumei()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function taobaocsv()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
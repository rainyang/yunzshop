<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class AppWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('app');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function fetch()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function slider()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function push()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function type()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);

    }
}


<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require_once 'model.php';

class AreaWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('area');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function statistics()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

//测试

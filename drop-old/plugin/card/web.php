<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require_once 'model.php';

class CardWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('card');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function log()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}
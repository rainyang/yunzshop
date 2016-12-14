<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require_once 'model.php';

class PosteraWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('postera');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function manage()
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
}

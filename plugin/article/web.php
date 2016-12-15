<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require_once 'model.php';

class ArticleWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('article');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function api()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

//测试

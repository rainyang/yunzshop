<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class ArticleMobile extends Plugin
{
	public function __construct()
	{
		parent::__construct('article');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function api()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function article()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function report()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}
	public function article_pc()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}
}
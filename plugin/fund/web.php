<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class FundWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('fund');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('fund.shop')) {
			header('location: ' . $this->createWebUrl('shop/goods', array('plugin' => "fund")));
			exit;
		} else if (cv('fund.cover')) {
			header('location: ' . $this->createWebUrl('fund/cover'));
			exit;
		}
		
	}

	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function cover()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function order()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	
}

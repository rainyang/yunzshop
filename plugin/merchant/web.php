<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class MerchantWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('merchant');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('merchant.center')) {
			header('location: ' . $this->createPluginWebUrl('merchant/center'));
			exit;
		}
	}

	public function upgrade()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function merchants()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_order()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function set()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_apply()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_apply_finish()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function center()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function level()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
}

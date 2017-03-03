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
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function merchants()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_order()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_apply()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function merchant_apply_finish()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function center()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function level()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

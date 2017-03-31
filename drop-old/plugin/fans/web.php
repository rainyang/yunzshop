<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class FansWeb extends Plugin
{
	//protected $set = null;

	public function __construct()
	{
		parent::__construct('fans');
		//$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('fans.member')) {
			header('location: ' . $this->createPluginWebUrl('fans/member'));
			exit;
		}
		if (cv('fans.agent')) {
			header('location: ' . $this->createPluginWebUrl('fans/agent'));
			exit;
		}
	}

	public function member()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function agent()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

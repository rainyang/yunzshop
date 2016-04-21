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
	}

	public function member()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function upgrade()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
}

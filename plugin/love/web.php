<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class LoveWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('love');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('love.log')) {
			header('location: ' . $this->createPluginWebUrl('love/log'));
			exit;
		} else if (cv('love.notice')) {
			header('location: ' . $this->createPluginWebUrl('love/notice'));
			exit;
		} else if (cv('love.set')) {
			header('location: ' . $this->createPluginWebUrl('love/set'));
			exit;
		}
	}

	public function upgrade()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function log()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	
	public function notice()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function set()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
}

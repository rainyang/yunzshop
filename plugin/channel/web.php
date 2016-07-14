<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class ChannelWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('channel');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		header('location: ' . $this->createPluginWebUrl('channel/manage'));
		exit;
	}
	public function level()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function upgrade()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function set()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function manage()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function af()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

}

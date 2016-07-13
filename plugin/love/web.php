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
		if (cv('love.agent')) {
			header('location: ' . $this->createPluginWebUrl('love/agent'));
			exit;
		} else if (cv('love.notice')) {
			header('location: ' . $this->createPluginWebUrl('love/notice'));
			exit;
		} else if (cv('love.set')) {
			header('location: ' . $this->createPluginWebUrl('love/set'));
			exit;
		} else if (cv('love.level')) {
			header('location: ' . $this->createPluginWebUrl('love/level'));
			exit;
		} else if (cv('love.cover')) {
			header('location: ' . $this->createPluginWebUrl('love/cover'));
			exit;
		} else if (cv('love.send')) {
			header('location: ' . $this->createPluginWebUrl('love/send'));
			exit;
		} else if (cv('love.sendall')) {
			header('location: ' . $this->createPluginWebUrl('love/sendall'));
			exit;
		} else if (cv('love.order')) {
			header('location: ' . $this->createPluginWebUrl('love/order'));
			exit;
		}
	}

	public function upgrade()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function agent()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function level()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function send()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function sendall()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function notice()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function cover()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

	public function set()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function detail()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function order()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
}

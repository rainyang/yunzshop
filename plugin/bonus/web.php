<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class BonusWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('bonus');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('bonus.agent')) {
			header('location: ' . $this->createPluginWebUrl('bonus/agent'));
			exit;
		} else if (cv('bonus.notice')) {
			header('location: ' . $this->createPluginWebUrl('bonus/notice'));
			exit;
		} else if (cv('bonus.set')) {
			header('location: ' . $this->createPluginWebUrl('bonus/set'));
			exit;
		} else if (cv('bonus.level')) {
			header('location: ' . $this->createPluginWebUrl('bonus/level'));
			exit;
		} else if (cv('bonus.cover')) {
			header('location: ' . $this->createPluginWebUrl('bonus/cover'));
			exit;
		} else if (cv('bonus.send')) {
			header('location: ' . $this->createPluginWebUrl('bonus/send'));
			exit;
		} else if (cv('bonus.sendarea')) {
			header('location: ' . $this->createPluginWebUrl('bonus/sendarea'));
			exit;
		} else if (cv('bonus.sendall')) {
			header('location: ' . $this->createPluginWebUrl('bonus/sendall'));
			exit;
		} else if (cv('bonus.order')) {
			header('location: ' . $this->createPluginWebUrl('bonus/order'));
			exit;
		} else if (cv('bonus.goods_rank')) {
			header('location: ' . $this->createPluginWebUrl('bonus/goods_rank'));
			exit;
		} else if (cv('bonus.apply')) {
			header('location: ' . $this->createPluginWebUrl('bonus/apply'));
			exit;
		}
		
	}

	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function agent()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function level()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function send()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function sendarea()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function sendall()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function notice()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function cover()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function detail()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function order()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function goods_rank()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function apply()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	
}

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class HotelWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('hotel');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('hotel.room_status')) {
			header('location: ' . $this->createPluginWebUrl('hotel/room_status'));
			exit;
		}else if (cv('hotel.room_price')) {
			header('location: ' . $this->createPluginWebUrl('hotel/room_price'));
			exit;
		}

	}

	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function room_status()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function room_price()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function meet()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function rest()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	public function prints()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

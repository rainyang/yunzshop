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
		$this->_exec_plugin(__FUNCTION__);
	}

	public function room_status()
	{
		$this->_exec_plugin(__FUNCTION__);
	}
	public function room_price()
	{
		$this->_exec_plugin(__FUNCTION__);
	}

}

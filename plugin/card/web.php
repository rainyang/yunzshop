<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require_once 'model.php';

class CardWeb extends Plugin
{
	public function __construct()
	{
		parent::__construct('card');
	}

	public function index()
	{
		if (cv('coupon.coupon.view')) {
			header('location: ' . $this->createPluginWebUrl('card/coupon'));
			exit;
		} else if (cv('coupon.category.view')) {
			header('location: ' . $this->createPluginWebUrl('card/category'));
			exit;
		} else if (cv('coupon.center.view')) {
			header('location: ' . $this->createPluginWebUrl('card/center'));
			exit;
		} else if (cv('coupon.set.view')) {
			header('location: ' . $this->createPluginWebUrl('card/set'));
			exit;
		}
	}

	public function coupon()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function center()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function category()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function send()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function log()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}
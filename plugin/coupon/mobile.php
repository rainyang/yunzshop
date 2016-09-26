<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class CouponMobile extends Plugin
{
	public function __construct()
	{
		parent::__construct('coupon');
	}

	public function index()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function detail()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function my()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function mydetail()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function util()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}
}
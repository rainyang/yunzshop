<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class CardMobile extends Plugin
{
	public function __construct()
	{
		parent::__construct('card');
	}

	public function center()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

	public function bindcard()
	{
		return $this->_exec_plugin(__FUNCTION__, false);
	}

    public function util()
    {
        return $this->_exec_plugin(__FUNCTION__, false);
    }
}
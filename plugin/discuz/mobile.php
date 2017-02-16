<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/29
 * Time: 下午12:28
 */
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class DiscuzMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('discuz');
    }

    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function uc()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function login()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }

    public function synlogin()
    {
        return $this->_exec_plugin(__FUNCTION__, false);
    }
}
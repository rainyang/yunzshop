<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class VerifyWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('verify');
    }
    public function index()
    {
        global $_W;
        if (cv('verify.keyword')) {
            header('location: ' . $this->createPluginWebUrl('verify/keyword'));
            exit;
        } else if (cv('verify.saler')) {
            header('location: ' . $this->createPluginWebUrl('verify/saler'));
            exit;
        } else if (cv('verify.store')) {
            header('location: ' . $this->createPluginWebUrl('verify/store'));
            exit;
        }
    }
    public function keyword()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function saler()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function store()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function withdraw()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function stock()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function category()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
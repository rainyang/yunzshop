<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class WxappWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('wxapp');
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);

    }
}


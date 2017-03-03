<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/29
 * Time: 下午12:23
 */

//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class DiscuzWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('discuz');
    }

    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function syn()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function login()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
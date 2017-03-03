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
class LiveWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('live');
    }

    public function index()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function upgrade()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function base()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function slider()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function enterpage()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function apply()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function room()
    {
        $this->_exec_plugin(__FUNCTION__);
    }    
    public function flowstatistics()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}
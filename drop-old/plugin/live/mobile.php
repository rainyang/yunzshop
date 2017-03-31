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
class LiveMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('live');
    }

    public function index()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function room()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function live()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function detail()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function single()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

}
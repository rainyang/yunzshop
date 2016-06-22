<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class AppWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('app');
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function fetch()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function slider()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function push()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function type()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function upgrade()
    {
        $this->_exec_plugin(__FUNCTION__);

    }
}


<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class HelperWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('helper');
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}
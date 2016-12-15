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
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
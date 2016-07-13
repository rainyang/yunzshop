<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class LoveMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('love');
        global $_GPC;
        $this->set = $this->getSet();
        $openid = m('user')->getOpenid();
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function log()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class RechargeMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('recharge');
        $this->set = $this->getSet();
    }
    public function detail()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function mobile_check()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function mobile_data_back()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

}

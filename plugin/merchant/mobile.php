<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
function sortByCreateTime($a, $b)
{
    if ($a['createtime'] == $b['createtime']) {
        return 0;
    } else {
        return ($a['createtime'] < $b['createtime']) ? 1 : -1;
    }
}
class MerchantMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('merchant');
        $this->set = $this->getSet();
        global $_GPC;
    }
    public function logg()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function applyg()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function orderj()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function team()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}
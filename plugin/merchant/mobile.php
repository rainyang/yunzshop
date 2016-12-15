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
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function applyg()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function orderj()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function team()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function teamc()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
    public function index()
    {
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
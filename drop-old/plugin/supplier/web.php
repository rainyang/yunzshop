<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class SupplierWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('supplier');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
		if (cv('supplier')) {
			header('location: ' . $this->createPluginWebUrl('supplier/supplier'));
			exit;
		} else if (cv('supplier')) {
			header('location: ' . $this->createPluginWebUrl('supplier/supplier_apply'));
			exit;
		} else if (cv('supplier')) {
			header('location: ' . $this->createPluginWebUrl('supplier/supplier_finish'));
			exit;
		}
	}
    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_apply()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_finish()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_for()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_list()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_add()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function notice()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_for_resu()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function supplier_detail()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}

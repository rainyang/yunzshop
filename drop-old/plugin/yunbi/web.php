<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class YunbiWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('yunbi');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;
        header('location: ' . $this->createPluginWebUrl('yunbi/set'));
    }
	public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function deduct()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function yunbi_log()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    } 
}

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class ReturnWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('return');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;

        header('location: ' . $this->createPluginWebUrl('return/set'));

    }

	public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function level()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function notice()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function return_tj()
    {

    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function queue()
    {

    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function return_log()
    {

    	return $this->_exec_plugin(__FUNCTION__);
    }
  
}

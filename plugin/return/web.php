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
        $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function level()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function notice()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function return_tj()
    {

        $this->_exec_plugin(__FUNCTION__);
    }
    public function queue()
    {

        $this->_exec_plugin(__FUNCTION__);
    }
    public function return_log()
    {

        $this->_exec_plugin(__FUNCTION__);
    }
  
}

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
        $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function deduct()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function yunbi_log()
    {
        $this->_exec_plugin(__FUNCTION__);
    } 
}

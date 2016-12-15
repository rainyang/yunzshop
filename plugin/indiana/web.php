<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class IndianaWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('indiana');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;
        header('location: ' . $this->createPluginWebUrl('indiana/set'));

    }

	public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function goods()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function good_info()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function period()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function cover()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}

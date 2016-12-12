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
        $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function goods()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function good_info()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function period()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function cover()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}

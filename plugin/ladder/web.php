<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class LadderWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('ladder');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;
        header('location: ' . $this->createPluginWebUrl('ladder/set'));
    }
	public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
 
}

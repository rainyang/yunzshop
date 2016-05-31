<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class RankingWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('ranking');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;

        header('location: ' . $this->createPluginWebUrl('ranking/set'));

    }

	public function upgrade()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

  
}

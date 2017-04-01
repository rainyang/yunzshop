<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class YunprintWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('yunprint');
		$this->set = $this->getSet();
	}

    public function index()
    {
        header('location: ' . $this->createPluginWebUrl('yunprint/print_list'));
        exit;
    }

    public function upgrade()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function print_list()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }

    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}

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

    public function upgrade()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function print_list()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}

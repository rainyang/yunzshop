<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class BeneficenceWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('beneficence');
		$this->set = $this->getSet();
	}

    public function index()
    {
        global $_W;
        header('location: ' . $this->createPluginWebUrl('beneficence/set'));

    }

	public function upgrade()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function beneficence()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class BeneficenceMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('beneficence');
        $this->set = $this->getSet();
        global $_GPC;
    }

    public function beneficence()
    {    
    	return $this->_exec_plugin(__FUNCTION__, false);
    }
}
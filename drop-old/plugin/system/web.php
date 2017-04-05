<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require_once 'model.php';
class SystemWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('system');
    }
    public function index()
    {
        global $_W;
        if (cv('system.clear')) {
            header('location: ' . $this->createPluginWebUrl('system/clear'));
            exit;
        } else if (cv('system.transfer')) {
            header('location: ' . $this->createPluginWebUrl('system/transfer'));
            exit;
        } else if (cv('system.copyright')) {
            header('location: ' . $this->createPluginWebUrl('system/copyright'));
            exit;
        } else if (cv('system.backup')) {
            header('location: ' . $this->createPluginWebUrl('system/backup'));
            exit;
        } else if (cv('system.commission')) {
            header('location: ' . $this->createPluginWebUrl('system/commission'));
            exit;
        }
		  else if (cv('system.replacedomain')) {
           header('location: ' . $this->createPluginWebUrl('system/replacedomain'));
           exit;
        }
        
    }
    
    public function clear()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function transfer()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function copyright()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function backup()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function commission()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
	public function replacedomain()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function uploadqiniu()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}
<?php
//ܿ���̳� QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class CreditshopWeb extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('creditshop');
        $this->set = $this->getSet();
    }
    public function index()
    {
        global $_W;
        if (cv('creditshop.cover')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/cover'));
            exit;
        } else if (cv('creditshop.goods')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/goods'));
            exit;
        } else if (cv('creditshop.category')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/category'));
            exit;
        } else if (cv('creditshop.adv')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/adv'));
            exit;
        } else if (cv('creditshop.log.view0')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/log', array(
                'type' => 0
            )));
            exit;
        } else if (cv('creditshop.log.view1')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/log', array(
                'type' => 1
            )));
            exit;
        } else if (cv('creditshop.notice')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/notice'));
            exit;
        } else if (cv('creditshop.set')) {
            header('location: ' . $this->createPluginWebUrl('creditshop/set'));
            exit;
        }
    }
    public function cover()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function category()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function goods()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function adv()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function log()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function notice()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function set()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
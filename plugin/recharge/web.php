<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
/**
 * * recharge插件web方法类
 * * @package   流量话费充值插件web公共方法
 * * @author    LuckyStar_D<duanfuxing@yunzshop.com>
 * * @version   v1.0
 * */
class RechargeWeb extends Plugin
{
	protected $set = null;
	/**
 * 	  * 初始化获取充值插件基础设置
 * 	    	  * @return array $this->set
 * 	    	  	  */
	public function __construct()
	{
		parent::__construct('recharge');
		$this->set = $this->getSet();
	}

	/**
 * 	  * 插件首页地址跳转
 * 	    	  * @return array $url
 * 	    	  	  */
	public function index()
	{
		global $_W;
		header('location: ' . $this->createPluginWebUrl('recharge/set'));
		exit;
	}

	 
	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
 * 	  * 流量话费充值基础配置方法
 * 	  	  */
	 
	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
 * 	 * 流量话费充值通知配置方法
 * 	 	 */
	public function notice()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

}


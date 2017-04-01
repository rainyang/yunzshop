<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
/**
* channel插件web方法类
* @package   渠道商插件web公共方法
* @author    LuckyStar_D<duanfuxing@yunzshop.com>
* @version   v1.0
*/
class ChannelWeb extends Plugin
{
	protected $set = null;
	/**
	  * 初始化获取渠道商基础设置
  	  * @return array $this->set
	  */
	public function __construct()
	{
		parent::__construct('channel');
		$this->set = $this->getSet();
	}

	/**
	  * 插件首页地址跳转
  	  * @return array $url
	  */
	public function index()
	{
		global $_W;
		header('location: ' . $this->createPluginWebUrl('channel/manage'));
		exit;
	}

	/**
	  * 渠道商等级方法
	  */
	 
	public function level()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
	  * 渠道商插件安装方法
	  */
	 
	public function upgrade()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
	  * 渠道商基础配置方法
	  */
	 
	public function set()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
	  * 渠道商管理方法
	  */
	 
	public function manage()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
	  * 渠道商申请方法
	  */
	 
	public function apply()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	/**
	  * 渠道商提现方法
	  */
	 
	public function withdraw()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}

	public function notice()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
	/**
	  * 渠道商库存管理
	  */
	public function inventory()
	{
		return $this->_exec_plugin(__FUNCTION__);
	}
}

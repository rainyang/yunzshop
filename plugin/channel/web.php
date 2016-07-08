<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class ChannelWeb extends Plugin
{
	protected $set = null;

	public function __construct()
	{
		parent::__construct('channel');
		$this->set = $this->getSet();
	}

	public function index()
	{
		global $_W;
	}
}

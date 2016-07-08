<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!class_exists('ChannelModel')) {
	class ChannelModel extends PluginModel
	{
	}
}
<?php
if (!defined("IN_IA")) {
    print("Access Denied");
}
class ExhelperWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct("exhelper");
    }
    public function index()
    {
        header("location: " . $this->createPluginWebUrl("exhelper/express", array(
            "op" => "list",
            "cate" => 1
        )));
        exit;
    }
    public function api()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function express()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function doprint()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function print_tpl()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function senduser()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function short()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
    public function printset()
    {
    	return $this->_exec_plugin(__FUNCTION__);
    }
}
?>

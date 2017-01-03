<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class TencentimageWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('tencentimage');
    }
    public function check($config)
    {
        return p('tencentimage')->save('http://www.baidu.com/img/bdlogo.png', $config);
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}

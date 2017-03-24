<?php
/*=============================================================================
#     FileName: processor.php
#         Desc: 
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:08:51
#      History:
=============================================================================*/

if (!defined('IN_IA')) {
    exit('Access Denied');
}
define('IS_API', true);

class Sz_yiModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $rule = pdo_fetch('select * from ' . tablename('rule') . ' where id=:id limit 1', array(
            ':id' => $this->rule
        ));
        if (empty($rule)) {
            return false;
        }
        $names  = explode(':', $rule['name']);
        $plugin = isset($names[1]) ? $names[1] : '';
        if (!empty($plugin)) {

            include_once __DIR__ . '/app/laravel.php';
            include_once __DIR__ . '/app/yunshop.php';

            //微信接口事件
            $response = '';
            event(new \app\common\events\WechatProcessor($this,$plugin,$response));

            return $response;

        }
    }
}

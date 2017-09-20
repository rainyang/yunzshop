<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/16 下午4:58
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\password;


class PasswordService
{
    private $auth_key;


    public function __construct()
    {
        $this->auth_key = \YunShop::app()->config['setting']['authkey'];
    }


    /**
     * 生成哈希加密密码
     * @param $password
     * @param $salt
     * @return string
     */
    public function make($password, $salt)
    {
        $password = "{$password}-{$salt}-{$this->auth_key}";
        return sha1($password);
    }


    /**
     * 密码验证
     * @param $password
     * @param $sha1_value
     * @param $salt
     * @return bool
     */
    public function check($password, $sha1_value, $salt)
    {
        return $sha1_value == $this-> make($password,$salt) ? true : false;
    }

}

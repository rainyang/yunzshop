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
     * 生成哈希加密密码值
     * @param $password
     * @param $salt
     * @return string
     */
    public function make($password, $salt)
    {
        $password = "{$password}-{$salt}-{$this->auth_key}";
        return sha1($password);
    }

    public function create($password)
    {
        $salt = $this->randNum(8);
        return ['password' => $this->make($password,$salt),'salt' => $salt];
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


    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    public function randNum($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;


class BaseMember
{
    protected $id;
    protected $openid;
    protected $mobile;
    protected $nickname;
    protected $avatar;
    protected $unionid;

    public function __construct()
    {
    }

    public function login()
    {}

    public function logout()
    {}

    public function isLogged()
    {}

    public function getId()
    {}

    public function getOpenId()
    {}

    public function getMobile()
    {}

    public function getNickName()
    {}

    public function getAvatar()
    {}

    public function getUnionId()
    {}
}
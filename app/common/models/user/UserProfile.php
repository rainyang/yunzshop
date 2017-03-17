<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:25
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class UserProfile extends BaseModel
{
    public $table = 'users_profile';

    public $timestamps = false;

    public $attributes =[
        'nickname'      => '',
        'avatar'        => '',
        'qq'            => '',
        'fakeid'        => '',
        'vip'           => 0,
        'gender'        => 0,
        'birthyear'     => 0,
        'birthmonth'    => 0,
        'birthday'      => 0,
        'constellation' => '',
        'zodiac'        => '',
        'telephone'     => '',
        'idcard'        => '',
        'studentid'     => '',
        'grade'         => '',
        'address'       => '',
        'zipcode'       => '',
        'nationality'   => '',
        'resideprovince' => '',
        'residecity'    => '',
        'residedist'    => '',
        'graduateschool' => '',
        'company'       => '',
        'education'     => '',
        'occupation'    => '',
        'position'      => '',
        'revenue'       => '',
        'affectivestatus' => '',
        'lookingfor'    => '',
        'bloodtype'     => '',
        'height'        => '',
        'weight'        => '',
        'alipay'        => '',
        'msn'           => '',
        'email'         => '',
        'taobao'        => '',
        'site'          => '',
        'bio'           => '',
        'interest'      => '',
        'workerid'      => ''
    ];

    public function relationValidator($data)
    {
        return self::validator($data);
    }

    public function relationSave($data)
    {

    }

}
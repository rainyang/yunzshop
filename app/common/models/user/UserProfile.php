<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:25
 */

namespace app\common\models\user;


use app\common\models\BaseModel;
use Illuminate\Validation\Rule;

class UserProfile extends BaseModel
{
    public $table = 'users_profile';

    public $timestamps = false;

    protected $guarded = [''];

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
        $userProfile = new static();
        $userProfile->fill($data);
        return $userProfile->validator();

    }
    /*
     *  @parms array $data
     *  @parms boject $model
     *
     *  @return bool
     * */
    public function addUserProfile($data, $model)
    {
        static::fill($data);
        $this->uid = $model->id;
        $this->createtime = $model->starttime;

        return $this->save();
    }

    public function relationSave($data)
    {

    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'realname'=> "姓名",
            'moblie' => "电话"
        ];
    }
    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'realname' => ['required', Rule::unique($this->table)->ignore($this->id)],
            'mobile' => 'required'
        ];
    }

}
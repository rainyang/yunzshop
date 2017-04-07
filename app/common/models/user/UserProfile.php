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

    //protected $guarded = [''];

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


    /*
     * 通过uid获取单条数据
     *
     * @params int $uid
     *
     * @return object */
    public static function getProfileByUid($uid)
    {
        return static::where('uid', $uid)->first();
    }

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


    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'realname'=> "姓名不能为空",
            'moblie' => "请输入正确的手机号码"
        ];
    }
    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'realname' => 'required',
            'mobile' => 'required|regex:/^1[34578]\d{9}$/'
        ];
    }

}
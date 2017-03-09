<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:53
 */

/**
 * 会员表
 */
namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class MemberModel extends BackendModel
{
    public $table = 'mc_members';

    protected $guarded = ['credit1', 'credit2', 'credit3', 'credit4' , 'credit5'];

    protected $fillable = ['email'=>'xxx@xx.com'];

    public $timestamps = false;

    /**
     * 主从表1:1
     *
     * @return mixed
     */
    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo','member_id','uid');
    }

    /**
     * 会员－粉丝一对一关系
     *
     * @return mixed
     */
    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans','uid','uid');
    }


    /**
     * 获取用户uid
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function getId($uniacid, $mobile)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->get()
            ->toArray();
    }

    /**
     * 添加数据并返回id
     *
     * @param $data
     * @return mixed
     */
    public static function insertData($data)
    {
        return self::insertGetId($data);
    }

    /**
     * 检查手机号是否存在
     *
     * @param $uniacid
     * @param $mobile
     * @return mixed
     */
    public static function checkMobile($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->first()
            ->toArray();
    }

    /**
     * 获取用户信息
     *
     * @param $uniacid
     * @param $mobile
     * @param $password
     * @return mixed
     */
    public static function getUserInfo($uniacid, $mobile, $password)
    {
        return self::where('uniacid', $uniacid)
            ->where('mobile', $mobile)
            ->where('password', $password);
    }

    /**
     * 获取用户信息
     *
     * @param $member_id
     * @return mixed
     */
    public static function getUserInfos($member_id)
    {
        return self::select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime',
            'credit1', 'credit2'])
            ->uniacid()
            ->where('uid', $member_id)
            ->with(['yzMember'=>function($query){
                return $query->select(['member_id','agent_id', 'is_agent', 'group_id','level_id', 'is_black', 'alipayname', 'alipay', 'content']);
            }, 'hasOneFans' => function($query2) {
                return $query2->select(['uid', 'follow as followed']);
            }
            ]);
    }

    /**
     * 更新数据
     *
     * @param $uid
     * @param $data
     * @return mixed
     */
    public static function updataData($uid, $data)
    {
        return self::uniacid()
            ->where('uid', $uid)
            ->update($data);
    }
}
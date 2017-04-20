<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午10:53
 */

/**
 * 公众号登录表
 */
namespace app\frontend\modules\member\models;

use app\common\models\McMappingFans;

class McMappingFansModel extends McMappingFans
{
    public $timestamps = false;

    protected $guarded = [];

    protected $fillable = ['openid','uid','acid','uniacid', 'salt', 'updatetime', 'nickname', 'follow', 'followtime', 'unfollowtime', 'tag'];

    protected $attributes = ['unionid'=>'', 'groupid'=>0];


    public function getOauthUserInfo()
    {
        return mc_oauth_userinfo();
    }

    /**
     * 获取粉丝uid
     *
     * @param $openid
     * @return mixed
     */
    public static function getUId($openid)
    {
        return self::select('uid')
            ->uniacid()
            ->where('openid', $openid)
            ->first();
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public static function insertData($data)
    {
        self::insert($data);
    }

    /**
     * 更新数据
     *
     * @param $uid
     * @param $data
     */
    public static function updateData($uid, $data)
    {
        self::uniacid()
            ->where('uid', $uid)
            ->update($data);
    }

    public static function getMemberInfo($oppenid)
    {

    }
}
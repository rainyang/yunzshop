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

use app\backend\models\BackendModel;

class McMappingFansModel extends BackendModel
{
    public $table = 'mc_mapping_fans';
    public $timestamps = false;

    public function getOauthUserInfo()
    {
        return mc_oauth_userinfo();
    }

    /**
     * 获取
     *
     * @param $uniacid
     * @return mixed
     */
    public function getMemberId($uniacid)
    {
        $user_info = $this->getOauthUserInfo();

        return self::where('uniacid', $uniacid)
            ->where('openid', $user_info['openid'])
            ->first()
            ->toArray();
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
            ->first()
            ->toArray();
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
}
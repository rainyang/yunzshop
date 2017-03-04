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

use Illuminate\Database\Eloquent\Model;

class McMappingFansModel extends Model
{
    public $table = 'mc_mapping_fans';

    public function getOauthUserInfo()
    {
        return mc_oauth_userinfo();
    }

    public function getMemberId($uniacid)
    {
        $user_info = $this->getOauthUserInfo();

        return self::where('uniacid', $uniacid)
            ->where('openid', $user_info['openid'])
            ->first()
            ->toArray();
    }

    public static function getUId($uniacid, $openid)
    {
        return self::select('uid')
            ->where('uniacid', $uniacid)
            ->where('openid', $openid)
            ->first()
            ->toArray();
    }
}
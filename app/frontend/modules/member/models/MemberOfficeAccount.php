<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/26
 * Time: 下午7:52
 */

namespace app\frontend\modules\member\models;

use Illuminate\Database\Eloquent\Model;

class MemberOfficeAccount extends Model
{
    public $table = 'yz_member_offic_account';

    /*public function getOauthUserInfo()
    {
        return mc_oauth_userinfo();
    }

    public function getMemberId($uniacid)
    {
        $user_info = $this->getOauthUserInfo();

        MemberOfficeAccount::wherr('uniacid', $uniacid)
            ->where('openid', $user_info['openid'])
            ->get('member_id');
    }*/
}
<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/29
 * Time: 11:40
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class MemberInvitationCodeLog extends BaseModel
{
    public $table = 'yz_member_invitation_log';
    public $search_fields = ['member_id', 'invitation_code', 'mid'];

    public static function searchMembers(Builder $query, $params)
    {
        if (isset($params['member_id'])) {
            $query->where('member_id', trim($params['member_id']));
        }
        if (isset($params['mid'])) {
            $query->where('mid', trim($params['mid']));
        }
        if (isset($params['invitation_code'])) {
            $query->where('invitation_code', trim($params['invitation_code']));
        }
        return $query;
    }

    public function yzMember()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'm_id', 'member_id');
    }

    public function yzMembers()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'm_id', 'mid');
    }
}
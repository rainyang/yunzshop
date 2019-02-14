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
use app\common\models\MemberShopInfo;

class MemberInvitationCodeLog extends BaseModel
{
    public $table = 'yz_member_invitation_log';
    public $search_fields = ['member_id', 'invitation_code', 'mid'];

    public static function searchLog($params)
    {
    	$res = self::uniacid();

        if (isset($params['mid']) && $params['mid'] > 0) {
            $res = self::where('mid', trim($params['mid']))
            	->orWhere('member_id', trim($params['mid']));
        }
        
        if (isset($params['code'])) {
            $res = self::where('invitation_code', trim($params['code']));
        }

        if ($params['searchtime']) {
        	$res = self::where('created_at', '>=', strtotime($params['times']['starttime']))->where('created_at', '<=', strtotime($params['times']['endtime']));
        }
        
        $res = $res->with(['yzMember'=>function($query) {
            
            $query->with('hasOneMember');

        }]);

        // $res->leftJoin('yz_member', 'yz_member_invitation_log.mid', '=', 'yz_member.m_id');
        // $res->leftJoin('yz_member', 'mc_members.uid', '=', 'yz_member.member_id');

        return $res;
    }

    public function yzMember()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'm_id', 'member_id');
    }


    public function yzMembers()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'm_id', 'mid');
    }

    public static function getInvitedInfo($member_id) 
    {
    	return MemberShopInfo::getMemberShopInfo($member_id);
    }
}
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

        if ($params['mid'] && $params['mid'] > 0) {
            $res = self::where('mid', trim($params['mid']))
            	->orWhere('member_id', trim($params['mid']));
        }
        
        if ($params['code']) {
            $res = self::where('invitation_code', trim($params['code']));
        }

        if ($params['searchtime']) {
        	$res = self::where('created_at', '>=', strtotime($params['times']['starttime']))->where('created_at', '<=', strtotime($params['times']['endtime']));
        }
        
        
        $res = $res->with(['yzMember'=>function($query) {
            $query->with('hasOneMember');
        }])->with(['hasOneMcMember'=> function($query) {
            $query->with('hasOneMember');
        }]);

        return $res;
    }

    public function yzMember()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'member_id', 'member_id');
    }

    public function hasOneMcMember()
    {
    	return $this->hasOne('\app\common\models\MemberShopInfo', 'member_id', 'mid');
    }
}
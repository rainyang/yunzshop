<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午4:16
 */

namespace app\backend\modules\member\models;



class MemberShopInfo extends \app\common\models\MemberShopInfo
{
    public static function getAllMemberShopInfo()
    {
        //会员列表分页使用，需要改建
        return static::where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
    }

    public function group()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberGroup');
    }

    public function level()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberLevel');
    }

    public function agent()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'agentid', 'uid');
    }

}

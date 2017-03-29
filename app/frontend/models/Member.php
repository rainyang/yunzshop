<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 下午5:40
 */

namespace app\frontend\models;


use app\common\models\Coupon;
use app\common\models\MemberCoupon;

class Member extends \app\common\models\Member
{
    /**
     * 会员－会员优惠券1:多关系
     * @param null $backType
     * @return mixed
     */
    public function hasManyMemberCoupon($backType = null)
    {
        return $this->hasMany(MemberCoupon::class, 'uid', 'uid')->where('used',0)->whereHas('belongsToCoupon',function($query) use($backType){
            if(isset($backType)){
                $query->where('back_type',$backType);
            }
        });
    }
}
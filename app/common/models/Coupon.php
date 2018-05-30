<?php

namespace app\common\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Coupon
 * @package app\common\models
 * @property int coupon_method
 * @property int use_type
 * @property int time_limit
 * @property string name
 * @property int is_complex
 * @property int id
 * @property int total
 * @property int time_days
 * @property Carbon time_start
 * @property Carbon time_end
 */
class  Coupon extends BaseModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at','time_start','time_end'];

    const COUPON_SHOP_USE = 0; //适用范围 - 商城通用

    const COUPON_CATEGORY_USE = 1; //适用范围 - 指定分类

    const COUPON_GOODS_USE = 2; //适用范围 - 指定商品

    const COUPON_MONEY_OFF = 1; //优惠方式- 立减

    const COUPON_DISCOUNT = 2; //优惠方式- 折扣

    const COUPON_DATE_TIME_RANGE = 1;//有效期 - 时间范围

    const COUPON_SINCE_RECEIVE = 0;//有效期 - 领取后n天


    public $table = 'yz_coupon';

    protected $guarded = [];

    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
    ];
    public $Surplus;
    protected $appends = ['surplus'];


    public function getSurplusAttribute()
    {
        $issued = MemberCoupon::getCouponBycouponId($this->id)->count('id');
        $this->Surplus = $this->total - $issued;
        return $this->Surplus;
    }


    public static function getMemberCoupon($used = 0) { //todo 这张表没有used这个字段, 应该放在member_coupon表?
        return static::uniacid()->where('used', $used);
    }

    public function hasManyMemberCoupon()
    {
        return $this->hasMany('app\common\models\MemberCoupon');
    }

    public static function getValidCoupon($MemberModel)
    {
        return MemberCoupon::getMemberCoupon($MemberModel);
    }

    public static function getUsageCount($couponId)
    {
        return static::uniacid()
                    ->select(['id'])
                    ->where('id', '=', $couponId)
                    ->withCount(['hasManyMemberCoupon' => function($query){
                        return $query->where('used', '=', 0);
                    }]);
    }

    public static function getCouponById($couponId)
    {
        return static::uniacid()
                    ->where('id', '=', $couponId)
                    ->first();
    }

    //getter
    public static function getter($couponId, $attribute)
    {
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value($attribute);
    }

    //获取优惠券优惠方式
    public static  function getPromotionMethod($couponId) {
        $useType = static::uniacid()->where('id', '=', $couponId)->value('coupon_method');
        switch ($useType){
            case self::COUPON_MONEY_OFF:
                return [
                    'type' =>  self::COUPON_MONEY_OFF,
                    'mode' => static::uniacid()->where('id', '=', $couponId)->value('deduct'),
                ];
                break;
            case self::COUPON_DISCOUNT:
                return [
                    'type' => self::COUPON_DISCOUNT,
                    'mode' => static::uniacid()->where('id', '=', $couponId)->value('discount'),
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }
    //获取优惠券适用期限
    public static function getTimeLimit ($couponId) {
        $time_limit = static::uniacid()->where('id', '=', $couponId)->value('time_limit');
        switch ($time_limit){
            case self::COUPON_SINCE_RECEIVE:
                return [
                    'type' =>  self::COUPON_SINCE_RECEIVE,
                    'time_end' => static::uniacid()->where('id', '=', $couponId)->value('time_days'),
                ];
                break;
            case self::COUPON_DATE_TIME_RANGE:
                return [
                    'type' => self::COUPON_DATE_TIME_RANGE,
                    'time_end' => static::uniacid()->where('id', '=', $couponId)->value('time_end'),
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }

    //获取优惠券的适用范围
    public static function getApplicableScope($couponId){
        $useType = static::uniacid()
            ->where('id', '=', $couponId)
            ->value('use_type');
        switch ($useType){
            case self::COUPON_GOODS_USE:
                $goodIds = self::getApplicalbeGoodIds($couponId);
                return [
                    'type' =>  self::COUPON_GOODS_USE,
                    'scope' => $goodIds,
                ];
                break;
            case self::COUPON_CATEGORY_USE:
                $categoryIds = self::getApplicalbeCategoryIds($couponId);
                return [
                    'type' => self::COUPON_CATEGORY_USE,
                    'scope' => $categoryIds,
                ];
                break;
            default:
                return [
                    'type' => self::COUPON_SHOP_USE,
                ];
                break;
        }
    }

    //获取优惠券的适用商品ID
    public static function getApplicalbeGoodIds($couponId){
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value('goods_ids');
    }

    //获取优惠券的适用商品分类ID
    public static function getApplicalbeCategoryIds($couponId){
        return static::uniacid()
            ->where('id', '=', $couponId)
            ->value('category_ids');
    }
    

}

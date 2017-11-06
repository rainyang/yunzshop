<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:18
 */

namespace app\common\models;


use app\backend\models\BackendModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberShopInfo extends BackendModel
{
    use SoftDeletes;

    protected $table = 'yz_member';

    protected $guarded = [''];

    public $timestamps = true;

    public $primaryKey = 'member_id';


    /**
     * 设置全局作用域
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('uniacid',function (Builder $builder) {
            return $builder->uniacid();
        });
    }

    /**
     * 关联会员等级表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberLevel', 'level_id', 'id');
    }

    public function scopeSearch($query,$search)
    {
        if ($search['member_level']) {
            $query->ofLevelId($search['member_level']);
        }
        if ($search['member_group']) {
            $query->ofGroupId($search['member_group']);
        }
        return $query;
    }

    public function scopeOfLevelId($query,$levelId)
    {
        return $query->where('level_id',$levelId);
    }

    public function scopeOfGroupId($query,$groupId)
    {
        return $query->where('group_id',$groupId);
    }

    /**
     * 会员ID检索
     * @param $query
     * @param $memberId
     * @return mixed
     */
    public function scopeOfMemberId($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * 检索关联会员等级表
     * @param $query
     * @return mixed
     */
    public function scopeWithLevel($query)
    {
        return $query->with(['level' => function($query) {
            return $query;
        }]);
    }



    /**
     * 获取用户信息
     *
     * @param $memberId
     * @return mixed
     */
    public static function getMemberShopInfo($memberId)
    {
        return self::select('*')->where('member_id', $memberId)
            ->uniacid()
            ->first(1);
    }

    /**
     * 通过 openid 获取用户信息
     * @param $openid
     * @return mixed
     */
    public static function getMemberShopInfoByOpenid($openid)
    {
        return static::uniacid()->whereHas('hasOneMappingFans', function($query) use ($openid){
            $query->where('openid', '=', $openid);
        })->first();
    }

    /**
     * 获取我的下线
     *
     * @return mixed
     */
    public static function getAgentCount()
    {
        return self::uniacid()
             ->where('parent_id', \YunShop::app()->getMemberId())
             ->where('is_black', 0)
             ->count();
    }

    /**
     * 获取指定推荐人的下线
     *
     * @param $uids
     * @return mixed
     */
    public static function getAgentAllCount($uids)
    {
        return self::selectRaw('parent_id, count(member_id) as total')
            ->uniacid()
            ->whereIn('parent_id', $uids)
            ->where('is_black', 0)
            ->groupBy('parent_id')
            ->get();
    }

    public function hasManySelf()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'parent_id', 'member_id');
    }

    public function hasOnePreSelf()
    {
        return $this->hasOne('app\common\models\MemberShopInfo', 'member_id', 'parent_id');
    }

    public function hasOneMappingFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'member_id');
    }

    /**
     * 用户是否为黑名单用户
     *
     * @param $member_id
     * @return bool
     */
    public static function isBlack($member_id)
    {
        $member_model = self::getMemberShopInfo($member_id);

        if (1 == $member_model->is_black) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUserInfo($mobile)
    {
        return self::uniacid()
            ->where('withdraw_mobile', $mobile)
            ->first();
    }

    /**
     * 获取该公众号下所有用户的 member ID
     *
     * @return mixed
     */
    public static function getYzMembersId()
    {
        return static::uniacid()
            ->select (['member_id'])
            ->get();
    }
}

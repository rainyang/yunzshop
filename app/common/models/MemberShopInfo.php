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

class MemberShopInfo extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_member';

    protected $guarded = [''];

    //public $timestamps = true;

    public $primaryKey = 'member_id';


    private $lv1_offline;

    private $lv2_offline;

    private $lv3_offline;

    //团队
    //private $team_offline;







    /**
     * todo common 中的 model 不应该使用全局作用域 2018-03-02
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function level()
    {
        return $this->hasOne('app\common\models\MemberLevel', 'id', 'level_id');
    }


    /**
     * 会员第一级推客集合
     * @param $member_id
     * @return array
     */
    public function getLv1Offline($member_id)
    {
        return $this->setLv1Offline($member_id);
    }


    /**
     * 会员第二级推客集合
     * @param $member_id
     * @return array
     */
    public function getLv2Offline($member_id)
    {
        return $this->setLv2Offline($member_id);
    }


    /**
     * 会员第三级推客集合
     * @param $member_id
     * @return array
     */
    public function getLv3Offline($member_id)
    {
        return $this->setLv3Offline($member_id);
    }



    //团队
    public function getTeamOffline($member_id)
    {
        return $this->setTeamOffline($member_id);
    }


    /**
     * 会员第一级推客集合
     * @param $member_id
     * @return array
     */
    private function setLv1Offline($member_id)
    {
        $member_ids[] = $member_id;

        $this->lv1_offline = $this->getMemberOffline($member_ids);

        return $this->lv1_offline;
    }


    /**
     * 会员第二级推客集合
     * @param $member_id
     * @return array
     */
    private function setLv2Offline($member_id)
    {
        !isset($this->lv1_offline) && $this->setLv1Offline($member_id);

        $this->lv2_offline = $this->getMemberOffline($this->lv1_offline);

        return  $this->lv2_offline;
    }


    /**
     * 会员第三级推客集合
     * @param $member_id
     * @return array
     */
    private function setLv3Offline($member_id)
    {
        !isset($this->lv2_offline) && $this->setLv2Offline($member_id);

        $this->lv3_offline = $this->getMemberOffline($this->lv2_offline);

        return  $this->lv3_offline;
    }


    //团队
    private function setTeamOffline($member_id)
    {

    }


    /**
     * 查询会员推客集合 会员ID集合
     * @param array $member_ids
     * @return array
     */
    private function getMemberOffline(array $member_ids)
    {
        if (count($member_ids) > 10000) {

            $member_ids = array_chunk($member_ids, 10000);
            $result_assemble = [];
            foreach ($member_ids as $item) {
                $assemble = static::select('member_id')->whereIn('parent_id',$item)->get();
                $assemble = $assemble->isEmpty() ? [] : array_pluck($assemble->toArray(), 'member_id');

                $result_assemble = array_merge($result_assemble,$assemble);
            }
            return $result_assemble;
        }

        $assemble = static::select('member_id')->whereIn('parent_id',$member_ids)->get();

        return $assemble->isEmpty() ? [] : array_pluck($assemble->toArray(), 'member_id');
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

<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午11:22
 */

namespace app\backend\modules\member\models;


use app\common\facades\Setting;

class MemberLevel extends \app\common\models\MemberLevel
{


    public $guarded = [''];

    public function getLevelNameAttribute()
    {
        return static::defaultLevelName($this->attributes['level_name']);
    }

    public static function defaultLevelName($levelName)
    {
        return $levelName ?: Setting::get('shop.member')['level_name'];

    }


    /**
     * Get membership list
     *
     * @return
     */
    public static function getMemberLevelList()
    {
        return static::uniacid()->get()->toArray();
    }

    /**
     * 查询等级名称通过等级ID
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $levelId 等级id
     *
     * @return mixed
     */
    public static function getMemberLevelNameById($levelId)
    {
        $level = MemberLevel::when($levelId, function ($query) use ($levelId) {
            return $query->select('levelname')->where('id', $levelId);
        })
            ->first()->levelname;
        return $level ? $level : '';
    }

    /*
     * 获取等级分页列表
     *
     * @param int $pageSize
     *
     * @return object */
    public static function getLevelPageList($pageSize)
    {
        //todo 需要关联商品去title值
        return static::uniacid()
            ->with(['goods' => function ($query) {
                return $query->select('id', 'title');
            }])
            ->orderBy('level')
            ->paginate($pageSize);
    }

    /**
     * Get rank information by level ID
     *
     * @param int $levelId
     *
     * @return object
     */
    public static function getMemberLevelById($levelId)
    {
        return static::where('id', $levelId)
            ->with(['goods' => function ($query) {
                return $query->select('id', 'title');
            }])
            ->first();
    }

    /**
     * get members by definite member_level
     * @param $level member_level的level值,而不是其主键ID
     * @return mixed
     */
    public static function getMembersByLevel($level)
    {
        return static::uniacid()
            ->select(['id', 'level'])
            ->where('level', $level)
            ->with(['member' => function ($query) {
                return $query->select('member_id', 'level_id')
                    ->where('uniacid', \YunShop::app()->uniacid);
            }])
            ->first();
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'level' => '等级权重',
            'level_name' => '等级名称',
            'order_money' => '订单金额',
            'order_count' => '订单数量',
            'goods_id' => '商品ID',
            'discount' => '折扣'
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        $rule = [
            'level' => [
                'required',
                \Illuminate\Validation\Rule::unique($this->table)->where('uniacid', \YunShop::app()->uniacid)->ignore($this->id),
                'numeric',
                'between:1,9999'
            ],
            'level_name' => 'required',
            'discount' => 'numeric|between:0.1,10'
        ];

        $levelSet = Setting::get('shop.member');
        switch ($levelSet['level_type']) {
            case 0:
                $rule = array_merge(['order_money' => 'numeric|between:1,9999999999'], $rule);
                break;
            case 1:
                $rule = array_merge(['order_count' => 'integer|numeric|between:0,9999999999'], $rule);
                break;
            case 2:
                $rule = array_merge(['goods_id' => 'integer|numeric'], $rule);
                break;
        }

        return $rule;
    }

    //模型关联 关联商品
    public function goods()
    {
        return $this->hasOne('app\common\models\Goods', 'id', 'goods_id');
    }

    //关联会员
    public function member()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'level_id', 'id'); //注意yz_member数据表记录和关联的是member_level表的主键id, 而不是level值
    }

    public function getMemberLevelGoodsDiscountPrice($goodsPrice)
    {
        $this->discount = $this->discount == false ? 1 : $this->discount;
        return $this->discount * $goodsPrice;
    }
}

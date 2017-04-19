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
    /****************************       需要考虑。注意！！！      *******************
     *
     * 默认分组的完善，
     * 每次添加新公众号需要自动创建一条对应公众号uniacid的默认分组
     *
     *
     *****************************************************************************/


    public $guarded = [''];

    /**
     * Get membership list
     *
     * @return */
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
     * @return mixed */
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
            ->with(['goods' => function($query) {
                return $query->select('id','title');
            }])
            ->orderBy('level')
            ->paginate($pageSize);
    }

    /**
     * Get rank information by level ID
     *
     * @param int $levelId
     *
     * @return object */
    public static function getMemberLevelById($levelId)
    {
        return static::where('id', $levelId)
            ->with(['goods' => function($query) {
                return $query->select('id','title');
            }])
            ->first();
    }

    /**
     * get members by definite memberlevel
     * @param $levelId
     * @return mixed
     */
    public static function getMembersByLevel($levelId)
    {
        return static::where('id', $levelId)
                    ->select(['id', 'level'])
                    ->with(['member' => function($query){
                        return $query->select('member_id', 'level_id')->where('uniacid', \YunShop::app()->uniacid);
                    }])
                    ->first();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'level'         => '等级权重',
            'level_name'    => '等级名称',
            'order_money'   => '订单金额',
            'order_count'   => '订单数量',
            'goods_id'      => '商品ID',
            'discount'      => '折扣'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        $rule =  [
            'level'      => ['required',\Illuminate\Validation\Rule::unique($this->table)->ignore($this->id)],
            'level_name' => 'required',
            'discount'   => 'numeric|between:0.1,10'
        ];

        $levelSet = Setting::get('shop.member');
        if ($levelSet['level_type'] == 0) {
            $rule = array_merge(['order_money' => 'numeric'], $rule);
        }
        if ($levelSet['level_type'] == 1) {
            $rule = array_merge(['order_count' => 'integer|numeric'], $rule);
        }
        if ($levelSet['level_type'] == 2) {
            $rule = array_merge(['goods_id' => 'numeric'], $rule);
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
        return $this->hasMany('app\common\models\MemberShopInfo', 'level_id', 'level');
    }


}

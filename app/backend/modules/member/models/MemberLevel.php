<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午11:22
 */

namespace app\backend\modules\member\models;


use app\common\frame\Rule;

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
        return static::where('id', $levelId)->first();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            //'level'         => '等级权重不能为空且为唯一整数',
            'level_name'    => '等级名称不能为空',
            'discount'      => '请输入正确的折扣',
            'order_money'   => '请输入正确的订单金额',
            'order_count'   => '订单数量只能是整数'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            //'level'      => ['required',\Illuminate\Validation\Rule::unique($this->table)->ignore($this->id),'integer'],
            'level_name' => 'required',
            'discount'   => 'numeric',
            'order_money'=> 'numeric',
            'order_count'=> 'integer|numeric'
        ];
    }

    //模型关联 关联商品
    public function goods()
    {
        return $this->hasOne('app\common\models\Goods', 'id', 'goods_id');
    }

}

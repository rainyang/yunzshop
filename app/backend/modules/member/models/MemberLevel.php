<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午11:22
 */

namespace app\backend\modules\member\models;


class MemberLevel extends \app\common\models\MemberLevel
{
    /****************************       需要考虑。注意！！！      *******************
     *
     * 默认分组的完善，
     * 每次添加新公众号需要自动创建一条对应公众号uniacid的默认分组
     *
     *
     *****************************************************************************/




    //public $timestamps = false;
    public $guarded = [''];

    /****************************       对外接口       ****************************/


    /**
     * 查询等级名称通过等级ID
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $levelId 等级id
     *
     * @return mixed
     **/
    public static function getMemberLevelNameById($levelId)
    {
        $level = MemberLevel::when($levelId, function ($query) use ($levelId) {
            return $query->select('levelname')->where('id', $levelId);
        })
        ->first()->levelname;
        return $level ? $level : '';
    }

    /**
     * 触发会员升级系统【下单触发】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param mixed $goodsId 订单商品ID
     * @param int $orderMoney 完成订单总金额
     * @param int $discount 完成点总数量
     * @return
     **/
    public static function upgradeMemberLevel($goodsId, $orderMoney, $discount)
    {
        echo 1;exit;
        //待完善中
        //商品id可能是数组，需要判断
    }


    /****************************       后台数据操作       ****************************/

    /**
     * Get rank information by level ID
     *
     * @param int $levelId
     *
     * @return array
     **/
    public static function getMemberLevelById($levelId)
    {
        return static::where('id', $levelId)->first();
    }
    /**
     * Get membership list
     *
     * @return
     **/
    public static function getMemberLevelList()
    {
        return static::uniacid()->get()->toArray();
    }
    /**
     * Delete member level by level ID
     *
     * @param int $levelId
     *
     * @return
     **/
    public static function deleteMemberLevel($levelId)
    {
        return  static::where('id', $levelId)->delete();
    }

}
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
    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

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
    public function upgradeMemberLevel($goodsId, $orderMoney, $discount)
    {
        //待完善中
        //商品id可能是数组，需要判断
    }


    /****************************       后台数据操作       ****************************/

    /**
     * 获取等级列表
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     *
     * @return
     **/
    public static function getMemberLevelList()
    {
        $uniacid = \YunShop::app()->uniacid;
        $level_list = MemberLevel::where('uniacid', $uniacid)->get();

        return $level_list;
    }
    /**
     * 添加会员等级
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param array $data 会员组信息
     *
     * @return int $id
     **/
    public static function createMemberLevel($data)
    {
        $id = static::insert($data);
        return $id;
    }
    /**
     * 删除会员等级通过等级ID
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $level_id 等级id
     *
     * @return
     **/
    public static function deleteMemberLevel($level_id)
    {
        $status = static::where('id', $level_id)->delete();
        return $status;
    }

}
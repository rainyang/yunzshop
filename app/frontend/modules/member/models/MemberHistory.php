<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:43
 */

namespace app\frontend\modules\member\models;



class MemberHistory extends \app\common\models\MemberHistory
{
    //public $timestamps = false;

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];
    /**
     * Get member browsing records
     *
     * @param int $memberId 会员ID
     *
     * @return object $list
     **/
    public static function getMemberHistoryList($memberId)
    {
        return MemberHistory::uniacid()->where('member_id', $memberId)->get()->toArray();
    }
    /**
     * 添加浏览记录【增】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $memberId 会员ID
     * @param int $goodsId 商品ID
     **/
    public static function createMemberHistory($memberId, $goodsId)
    {
        $id = MemberHistory::insert([
            'member_id' => $memberId,
            'goods_id' => $goodsId,
            'uniacid' => \YunShop::app()->uniacid
        ]);
        return $id;
    }
    public static function saveMemberHistory($memberId, $goodsId)
    {
        $result = static::updateOrCreate(
            array(
                'member_id' => $memberId,
                'uniacid' => \YunShop::app()->uniacid,
                'goods_id' => $goodsId
            ),
            array(
                'member_id' => $memberId,
                'uniacid' => \YunShop::app()->uniacid,
                'goods_id' => $goodsId
            )
        );
        return $result;
    }
    /**
     * 更新浏览记录【改】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $goodsId 商品ID
     **/
    public static function updateMemberHistory($goodsId)
    {

    }
    /**
     * 删除浏览记录【删】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $goodsId 商品ID
     **/
    public static function deleteMemberHistory($goodsId)
    {

    }
    /**
     * 查看浏览记录是否存在【查】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $goodsId 商品ID
     **/
    public static function hasMemberHistory($memberId, $goodsId)
    {
        return static::uniacid()
            ->where('member_id', $memberId)
            ->where('goods_id', $goodsId)
            ->first();
    }
}
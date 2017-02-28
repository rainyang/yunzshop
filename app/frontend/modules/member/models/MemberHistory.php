<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:43
 */

namespace app\frontend\modules\member\models;


use app\backend\modules\member\models\MemberLevel;
use Illuminate\Database\Eloquent\Model;

class MemberHistory extends Model
{
    public $table = 'sz_yi_member_history';

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];
    /**
     * 获取会员浏览记录
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     * @param int $memberId 会员ID
     * @param int $uniacid 公众号ID
     *
     * @return object $list
     **/
    public static function getMemberHistoryList($memberId, $uniacid)
    {
        $list = MemberHistory::where('goodsid', $memberId)
            ->where('uniacid', $uniacid)
            ->orderBy('createtime', 'desc')
            ->take(10)
            ->get();

        return $list;
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
        $id = MemberLevel::insertGetId([
            'member_id' => $memberId,
            'goods_id' => $goodsId,
            'uniacid' => \YunShop::app()->uniacid
        ]);
        return $id;
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
    public static function hasMemberHistory($goodsId)
    {

    }
}
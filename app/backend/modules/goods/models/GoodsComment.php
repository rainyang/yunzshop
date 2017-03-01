<?php
namespace app\backend\modules\goods\models;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class GoodsComment extends \app\common\models\GoodsComment
{
    public $timestamps = false;

    /**
     * @param $uniacid
     * @return int
     */
    public static function getCommentTotal($uniacid)
    {
        return self::where('uniacid', $uniacid)
            ->count();
    }

    /**
     * @param $uniacid
     * @param $pindex
     * @param $psize
     * @return array
     */
    public static function getComments($uniacid, $pindex, $psize)
    {
        return self::select('yz_goods_comment.*', 'yz_goods.title', 'yz_goods.thumb')
            ->where('yz_goods_comment.uniacid', $uniacid)
            ->leftJoin('yz_goods', 'yz_goods_comment.goods_id', '=', 'yz_goods.id')
            ->orderBy('yz_goods_comment.created_at', 'desc')
            ->skip(($pindex - 1) * $psize)
            ->take($psize)
            ->get()
            ->toArray();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getComment($id)
    {
        return self::where('id', $id)
            ->first()
            ->toArray();
    }

    /**
     * @param $reply
     * @param $id
     * @return mixed
     */
    public static function updatedComment($data, $id)
    {
        return self::where('id', $id)
        ->update($data);
    }

    /**
     * @param $comment
     * @return bool
     */
    public static function saveComment($comment)
    {
        return self::insert($comment);
    }


}
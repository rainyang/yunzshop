<?php
namespace app\backend\modules\goods\models;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class Comment extends \app\common\models\Comment
{
    public $timestamps = false;

    /**
     * @param $uniacid
     * @return int
     */
    public static function getCommentTotal($uniacid)
    {
        return self::where('uniacid', $uniacid)
            ->where('comment_id', '0')
            ->count();

        $test = 1;
    }

    /**
     * @param $uniacid
     * @param $pindex
     * @param $psize
     * @return array
     */
    public static function getComments($uniacid, $pindex, $psize)
    {
        return self::select('yz_comment.*', 'yz_goods.title', 'yz_goods.thumb')
            ->where('yz_comment.uniacid', $uniacid)
            ->where('comment_id', '0')
            ->leftJoin('yz_goods', 'yz_comment.goods_id', '=', 'yz_goods.id')
            ->orderBy('yz_comment.created_at', 'desc')
            ->skip(($pindex - 1) * $psize)
            ->take($psize)
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getComment($id)
    {
        return self::where('id', $id)
            ->first();
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

    public static function getReplysByCommentId($comment_id)
    {
        return self::where('comment_id',$comment_id)
        ->orderBy('created_at', 'asc')
        ->get();
    }
    /**
     *  定义字段名
     * 可使
     * @return array */
    public static function atributeNames() {
        return [
            'goods_id'=> '评论商品',
            'content'=> '评论内容',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public static function rules() {
        return [
            'goods_id'=> 'required',
            'content'=> 'required'
        ];
    }
}
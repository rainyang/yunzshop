<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/3
 * Time: 下午2:35
 */
namespace app\frontend\modules\goods\models;

class Comment extends \app\common\models\Comment
{

    public static function getCommentsByGoods($goods_id)
    {
        return self::select(
            'id', 'order_id', 'goods_id', 'uid', 'nick_name', 'head_img_url', 'content', 'level',
            'images', 'created_at')
            ->uniacid()
            ->with(['subComments'=>function($query){
                return $query->select(['id','comment_id', 'order_id', 'goods_id', 'uid',
                    'nick_name', 'head_img_url', 'content', 'level',
                    'images', 'created_at', 'reply_id', 'reply_name'])
                    ->orderBy('created_at', 'asc');
            }])
            ->where('goods_id', $goods_id)
            ->where('comment_id', 0)
            ->orderBy('created_at', 'acs');
    }

}
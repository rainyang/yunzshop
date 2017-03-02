<?php
namespace app\backend\modules\goods\services;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class CommentService
{
    /**
     * @param array $search
     * @return mixed
     */
    public static function Search($search = [])
    {
        $data['keyword']    = '';
        $data['fade']       = '';
        $data['replystatus']= '';
        $data['searchtime'] = '';
        $data['starttime']  = strtotime('-1 month');
        $data['endtime']    = time();

        return $data;
    }

    /**
     * @param $comment
     * @return mixed
     */
    public static function comment($comment)
    {
        $comment['created_at'] = time();
        if (isset($comment['images']) && is_array($comment['images'])) {
            $comment['images'] = iserializer($comment['images']);
        } else {
            $comment['images'] = iserializer([]);
        }
        return $comment;
    }
    

    public static function reply($reply, $comment, $member)
    {
        $data = [
            'uniacid'   => $comment->uniacid,
            'order_id'   => $comment->order_id,
            'goods_id'   => $comment->goods_id,
            'content'   => $reply['reply_content'],
            'created_at'   => time(),
            'comment_id'   => $comment->id,
            'reply_id'   => $reply['reply_id'],
            'reply_name'   => $member->nick_name,
        ];

        if (isset($reply['reply_images']) && is_array($reply['reply_images'])) {
            $data['images'] = iserializer($reply['reply_images']);
        } else {
            $data['images'] = iserializer([]);
        }
        
        return $data;
    }


}
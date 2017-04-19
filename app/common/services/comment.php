<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/19
 * Time: 下午2:53
 */

namespace app\common\services;

class Comment
{
    public static function tplReplyAppend($replyData)
    {
        return view('goods.comment.tpl-reply', [
            'replyData' => $replyData
        ])->render();
    }


}
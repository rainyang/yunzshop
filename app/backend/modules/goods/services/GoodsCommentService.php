<?php
namespace app\backend\modules\goods\services;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class GoodsCommentService
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
     * @param $reply
     * @return array
     */
    public static function reply($reply)
    {
        return [
            'reply_content' => $reply['reply_content'],
            'reply_images' => is_array($reply['reply_images']) 
                ? iserializer($reply['reply_images']) 
                : iserializer(array()),
            'append_reply_content' => isset($reply['append_reply_content']) ? $reply['append_reply_content'] : '',
            'append_reply_images' => isset($reply['append_reply_images']) && is_array($reply['append_reply_images'])
                ? iserializer($reply['append_reply_images']) 
                : iserializer(array())
        ];
 
    }

}
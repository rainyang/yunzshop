<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/24
 * Time: 上午11:13
 */

namespace app\common\helpers;


class UeditorHelper
{
    public static function tpl_ueditor($id, $value = '', $options = array())
    {
        if (!$options) {
            $options = [
                'height' => '',
                'allow_upload_video' => null,
                'dest_dir' => '',
            ];
        } 
        
        return tpl_ueditor($id, $value, $options);
    }
}
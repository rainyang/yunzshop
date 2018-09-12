<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 9:23
 */

namespace app\backend\modules\goods\models;


/**
 * 无法使用 exec 已被禁用
 * Class GoodsVideo
 * @package app\backend\modules\goods\models
 */
class GoodsVideo extends \app\common\models\goods\GoodsVideo
{
     public function relationValidator($goodsId, $data, $operate)
     {

     }

    public static function relationSave($goodsId, $data, $operate = '')
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $model = self::getThis($goodsId, $operate);

        //判断deleted
        if ($operate == 'deleted') {
            return $model->delete();
        }
        $attr['goods_id'] = $goodsId;
        $attr['uniacid'] = \YunShop::app()->uniacid;
        $attr['goods_video'] = $data['goods_video'];

        //商品视频地址
        $attr['goods_video'] = yz_tomedia($data['goods_video']);

        if ($data['goods_video']) {
            $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'goods'.DIRECTORY_SEPARATOR.'video-image'.DIRECTORY_SEPARATOR.\YunShop::app()->uniacid.DIRECTORY_SEPARATOR.date('Y', time()).DIRECTORY_SEPARATOR.date('m', time()));
            if (!is_dir($path)) {
                load()->func('file');
                mkdirs($path);
            }
            $file_path = self::getFile($path);

            $command = 'ffmpeg -i '.$attr['goods_video'].' -y -f image2 -t 0.003 -s 352x240 '.$file_path;

            exec($command, $output,$return_val);

            if ($return_val !== 0) {
                $attr['status'] = 1;
            } else {
                $attr['video_image'] = substr($file_path, strpos($file_path, 'app'));
            }
        }

        $model->setRawAttributes($attr);

        return $model->save();
    }

    public static  function getFile($path)
    {
        $str = str_replace('.', '-', uniqid('YZ',true));

        return $path.DIRECTORY_SEPARATOR.$str.'.jpg';

    }

    public static function getThis($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;

        return $model;
    }
}
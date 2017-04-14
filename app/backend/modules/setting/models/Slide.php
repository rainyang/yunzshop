<?php
namespace app\backend\modules\setting\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/14
 * Time: 下午9:01
 */
class Slide extends \app\common\models\Slide
{
    public static function getSlides()
    {
        return self::uniacid()
            ->orderBy('display_order', 'decs');

    }
    
    public static function getSlideByid($id)
    {
        return self::find($id);
    }

    public function daletedSlide($id)
    {
        return self::where('id', $id)
            ->delete();
    }
}
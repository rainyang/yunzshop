<?php
namespace app\backend\modules\goods\services;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/5/2
 * Time: 上午17:26
 */
class DispatchService
{
    public static function getDispatch($dispatchData)
    {
        $dispatchData['weight_data'] = iserializer($dispatchData['weight']);
        $dispatchData['piece_data'] = iserializer($dispatchData['piece']);
        unset($dispatchData['weight']);
        unset($dispatchData['piece']);
        return $dispatchData;
    }
}
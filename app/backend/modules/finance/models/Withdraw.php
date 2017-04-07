<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 下午3:05
 */
namespace app\backend\modules\finance\models;

class Withdraw extends \app\common\models\Withdraw
{
    
    
    public static function getWithdrawList($search = [])
    {

        $Model = self::uniacid();

        $Model->with(['hasOneMember' => function ($query) {
            $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);

        return $Model;
    }
    public static function updatedWithdrawStatus($id, $updatedData)
    {
        return self::where('id',$id)
            ->update($updatedData);
    }



}
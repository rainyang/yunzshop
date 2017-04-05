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

    public static function getWithdrawById($id)
    {
        $Model = self::where('id', $id);
        
        $Model->with(['hasOneMember' => function ($query) {
            $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);
        $Model->with(['hasOneAgent' => function ($query) {
            $query->select('member_id', 'mobile', 'realname', 'nickname', 'avatar');
        }]);

        return $Model;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/21
 * Time: 下午1:58
 */

namespace app\frontend\modules\order\models;


class OrderGoods extends \app\common\models\OrderGoods
{
    public function getButtonsAttribute()
    {
        $result = [];
        if($this->comment_status == 1){
            $result[] = [
                'name' => '查看评价',
                'api' => '',
                'value' => ''
            ];
        }
        return $result;
    }

    public static function getMyCommentList($uid, $status)
    {
        $list = self::select()->where('uid', $uid)->Where('comment_status', $status)->get();
        return $list;
    }
}
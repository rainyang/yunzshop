<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/21
 * Time: 下午1:58
 */

namespace app\frontend\modules\order\models;

use app\frontend\modules\goods\models\Goods;
use app\frontend\modules\goods\models\GoodsOption;

class OrderGoods extends \app\common\models\OrderGoods
{
    public function goodsOption()
    {
        return $this->hasOne(GoodsOption::class, 'id', 'goods_option_id');

    }
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
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
    public static function getMyCommentList($uid, $status)
    {
        $list = self::select()->where('uid', $uid)->Where('comment_status', $status)->orderBy('id', 'desc')->get();
        return $list;
    }
    public function isFreeShipping()
    {
//        //todo 区域
//        if(exceptArea){
//
//        }

        if ($this->goods->hasOneSale->isFree($this)) {
            return true;
        }

        return false;
    }
}
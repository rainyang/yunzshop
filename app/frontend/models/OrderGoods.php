<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/21
 * Time: 下午1:58
 */

namespace app\frontend\models;

use Illuminate\Database\Eloquent\Builder;

class OrderGoods extends \app\common\models\OrderGoods
{
    public function goodsOption()
    {
        return $this->hasOne(GoodsOption::class, 'id', 'goods_option_id');

    }

    public function scopeDetail($query)
    {
        return $query->select(['id','order_id','goods_option_title','goods_id','goods_price','total','price','title','thumb','comment_status']);
    }

    public function getButtonsAttribute()
    {
        $result = [];
        if ($this->comment_status == 1) {
            $result[] = [
                'name' => '查看评价',
                'api' => '',
                'value' => ''
            ];
        }
        return $result;
    }

    public static function getMyCommentList($status)
    {
        $list = self::select()->Where('comment_status', $status)->orderBy('id', 'desc')->get();
        return $list;
    }

    public function isFreeShipping()
    {

        if ($this->belongsToGood->hasOneSale->isFree($this)) {
            return true;
        }

        return false;
    }

    public static function boot()
    {
        parent::boot();

        self::addGlobalScope(function (Builder $query) {
            return $query->where('uid', \YunShop::app()->getMemberId());
        });
    }
}
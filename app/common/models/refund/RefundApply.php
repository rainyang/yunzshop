<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/12
 * Time: 下午1:38
 */

namespace app\common\models\refund;

use app\common\models\BaseModel;
use app\common\observers\refund\RefundApplyObserver;

class RefundApply extends BaseModel
{
    protected $table = 'yz_order_refund';
    protected $fillable = ['reason','images','order_id'];
    protected $attributes = [
        'images'=>'[]',
        'content'=>''
    ];
    protected $casts = [
        'images'=>'json',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if(!isset($this->uniacid)){
            $this->uniacid = \YunShop::app()->uniacid;
        }
    }

    public static function boot()
    {
        parent::boot();

        static::observe(new RefundApplyObserver());
    }
}
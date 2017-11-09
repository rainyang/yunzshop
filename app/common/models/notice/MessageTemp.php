<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/9
 * Time: 下午3:09
 */

namespace app\common\models\notice;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class MessageTemp extends BaseModel
{
    public $table = 'yz_message_template';
    protected $guarded = [''];
    public $timestamps = true;

    protected $casts = [
        'data' => 'json'
    ];

    public static function getTempById($temp_id)
    {
        return self::select()->whereId($temp_id);
    }

    public static function fetchTempList($kwd)
    {
        return self::select()->likeTitle($kwd);
    }

    public function scopeLikeTitle($query, $kwd)
    {
        return $query->where('title', 'like', '%' . $kwd . '%');
    }

    public static function handleArray($data)
    {
        $data['uniacid'] = \YunShop::app()->uniacid;
        $data['data'] = [];
        foreach ($data['tp_kw'] as $key => $val )
        {
            $data['data'][] = [
                'keywords' => $data['tp_kw'][$key],
                'value' => $data['tp_value'][$key],
                'color' => $data['tp_color'][$key]
            ];
        }
        return array_except($data, ['tp_kw', 'tp_value', 'tp_color']);
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}
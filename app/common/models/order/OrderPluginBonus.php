<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:41
 */

namespace app\common\models\order;


use app\common\models\BaseModel;
use Illuminate\Support\Facades\DB;

class OrderPluginBonus extends BaseModel
{
    public $table = 'yz_order_plugin_bonus';
    public $timestamps = true;
    protected $guarded = [''];
    protected $casts = [
        'ids' => 'json'
    ];
    protected $appends = [
        'info'
    ];

    public static function addRow($row)
    {
        $model = new self();
        $model->fill($row);
        $model->save();
        return $model;
    }

    public static function getInfoByOrderId($order_id)
    {
        return self::select()->where('order_id', $order_id);
    }

    public function getInfoAttribute()
    {
        $info = DB::table($this->table_name)->select()
            ->whereIn('id', $this->ids)
            ->get();
        return $info;
    }
}
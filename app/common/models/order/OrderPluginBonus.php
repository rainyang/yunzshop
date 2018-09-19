<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/9/19
 * Time: 下午3:41
 */

namespace app\common\models\order;


use app\common\models\BaseModel;

class OrderPluginBonus extends BaseModel
{
    public $table = 'yz_order_plugin_bonus';
    public $timestamps = true;
    protected $guarded = [''];

    public static function addRow($row)
    {
        $model = new self();
        $model->fill($row);
        $model->save();
    }
}
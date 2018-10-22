<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 下午12:00
 */

namespace app\backend\modules\point\models;


use app\common\scopes\UniacidScope;

class RechargeModel extends \app\common\models\point\RechargeModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope('uniacid', new UniacidScope);
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'member_id' => "required",
            'money'     => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:9999999999',
            'type'      => 'required',
            'order_sn'  => 'required',
            'status'    => 'required',
            'remark'    => 'max:50'
        ];
    }
}

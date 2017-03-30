<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 上午9:34
 */

namespace app\common\models;


use app\backend\models\BackendModel;

class Withdraw extends BackendModel
{
    public $table = 'yz_withdraw';

    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];
    
    
    

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public  function atributeNames()
    {
        return [
            'member_id' => '会员ID',
            'type' => '提现类型',
            'amounts' => '提现金额',
            'pay_way' => '打款方式',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public  function rules()
    {
        return [
            'member_id' => 'required',
            'type' => 'required',
            'amounts' => 'required',
            'pay_way' => 'required',
        ];
    }
}
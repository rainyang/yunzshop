<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 上午9:34
 */

namespace app\common\models;


use app\backend\models\BackendModel;
use app\frontend\modules\finance\services\WithdrawService;

class Withdraw extends BackendModel
{
    public $table = 'yz_withdraw';
    
    public $StatusService;
    
    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];

    protected $appends = ['status_name'];

    public function getStatusService()
    {
        if (!isset($this->StatusService)) {

            $this->StatusService = WithdrawService::createStatusService($this);
        }
        return $this->StatusService;
    }

    public function getStatusNameAttribute()
    {
        return $this->getStatusService();
    }
    
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
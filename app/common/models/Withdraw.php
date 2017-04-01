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
    
    public $PayWayService;
    
    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];

    protected $appends = ['status_name','pay_way_name'];

    /**
     * @return string
     */
    public function getStatusService()
    {
        if (!isset($this->StatusService)) {

            $this->StatusService = WithdrawService::createStatusService($this);
        }
        return $this->StatusService;
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return $this->getStatusService();
    }

    /**
     * @return string
     */
    public function getPayWayService()
    {
        if (!isset($this->PayWayService)) {

            $this->PayWayService = WithdrawService::createPayWayService($this);
        }
        return $this->PayWayService;
    }

    /**
     * @return string
     */
    public function getPayWayNameAttribute()
    {
        return $this->getPayWayService();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
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
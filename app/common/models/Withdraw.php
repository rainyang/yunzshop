<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 上午9:34
 */

namespace app\common\models;


use app\backend\models\BackendModel;
use app\backend\modules\finance\models\IncomeOrder;
use app\frontend\modules\finance\services\WithdrawService;
use Illuminate\Support\Facades\Config;

class Withdraw extends BackendModel
{
    public $table = 'yz_withdraw';

    public $StatusService;

    public $PayWayService;

    public $TypeData;

    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];


    protected $appends = ['status_name', 'pay_way_name', 'type_data'];

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
     * @return string
     */
    public function getTypeDataAttribute()
    {
        
        if (!isset($this->TypeData)) {
            $configs = Config::get('income');
            foreach ($configs as $key => $config) {
                if ($key === $this->type) {
                    $orders = Income::getIncomeByIds($this->type_id)->get();
                    if($orders){
                        foreach ($orders as $order) {
                            $this->TypeData[] = $order->incometable->ordertable->toArray();
                        }
                    }
                }

            }
        }

        return $this->TypeData;
    }

    public static function getWithdrawById($id)
    {
        $Model = self::where('id', $id);

        $Model->with(['hasOneMember' => function ($query) {
            $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);
        $Model->with(['hasOneAgent' => function ($query) {
            $query->select('member_id', 'agent_level_id', 'commission_total');
        }]);

        return $Model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasOneAgent()
    {
        return $this->hasOne('Yunshop\Commission\models\Agents', 'member_id', 'member_id');
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
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
    public function rules()
    {
        return [
            'member_id' => 'required',
            'type' => 'required',
            'amounts' => 'required',
            'pay_way' => 'required',
        ];
    }
}
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
                if ($config['class'] === $this->type) {

                    $orders = Income::getIncomeByIds($this->type_id)->get();
//                    $is_pay = Income::getIncomeByIds($this->type_id)->where('pay_status','1')->get()->sum(amount);
                    if($orders){
                        $this->TypeData['income_total'] = $orders->count();
//                        $this->TypeData['is_pay'] = $is_pay;
                        $this->TypeData['incomes'] = $orders->toArray();
                        foreach ($this->TypeData['incomes'] as &$item) {
                            $item['detail'] = json_decode($item['detail'],true);
                        }
//                        foreach ($orders as $k => $order) {
////                            $this->TypeData['orders'][$k] = $order->incometable->ordertable->toArray();
//                            $this->TypeData['incomes'][$k] = $order->incometable->toArray();
//                        }

                    }
                }


            }
        }
        return $this->TypeData;
    }

    public static function getBalanceWithdrawById($id)
    {
        return self::uniacid()->where('id', $id)
            ->with(['hasOneMember' => function($query) {
                return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar')
                    ->with(['yzMember' => function($member) {
                        return $member->select('member_id', 'group_id')
                            ->with(['group' => function($group) {
                                return $group->select('id', 'group_name');
                            }]);
                    }]);
            }])
            ->first();

    }
    public static function getWithdrawById($id)
    {
        $Model = self::where('id', $id);
        $Model->orWhere('withdraw_sn',$id);
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

    public static function updatedWithdrawStatus($id, $updatedData)
    {
        return self::where('id',$id)
            ->orWhere('withdraw_sn',$id)
            ->update($updatedData);
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
     * @Author yitian */
    public function rules()
    {
        $rule =  [
            'member_id' => 'required',
            'type' => 'required',
            'amounts' => 'required',
            'pay_way' => 'required',
        ];

        return $rule;
    }
}
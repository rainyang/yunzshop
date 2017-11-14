<?php
/**
 * Created by PhpStorm.
 * Class Withdraw
 * Author: Yitan
 * Date: 2017/11/06
 * @package app\common\models
 */

namespace app\common\models;


use app\backend\models\BackendModel;
use app\frontend\modules\finance\services\WithdrawService;
use Illuminate\Support\Facades\Config;



class Withdraw extends BackendModel
{
    public $table = 'yz_withdraw';

    protected $guarded = [];

    protected $appends = ['status_name', 'pay_way_name'];


    //审核状态
    const STATUS_INVALID    = -1;

    const STATUS_INITIAL    = 0;

    const STATUS_AUDIT      = 1;

    const STATUS_PAY        = 2;

    const STATUS_REJECT     = 3;

    const STATUS_PAYING     = 4;




    const WITHDRAW_WITH_BALANCE = 'balance';

    const WITHDRAW_WITH_WECHAT = 'wechat';

    const WITHDRAW_WITH_ALIPAY = 'alipay';

    const WITHDRAW_WITH_MANUAL = 'manual';



    //手动提现位置
    const MANUAL_TO_BANK = 1;

    const MANUAL_TO_WECHAT = 2;

    const MANUAL_TO_ALIPAY = 3;




    public static $statusComment = [
        self::STATUS_INVALID    => '无效',
        self::STATUS_INITIAL    => '未审核',
        self::STATUS_AUDIT      => '未打款',
        self::STATUS_PAY        => '已打款',
        self::STATUS_REJECT     => '已驳回',
        self::STATUS_PAYING     => '打款中',
    ];

    public static $payWayComment = [
        self::WITHDRAW_WITH_BALANCE     => '提现到余额',
        self::WITHDRAW_WITH_WECHAT      => '提现到微信',
        self::WITHDRAW_WITH_ALIPAY      => '提现到支付宝',
        self::WITHDRAW_WITH_MANUAL      => '提现手动打款',
    ];




    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }




    /**
     * 通过 $status 值获取 $status 名称
     * @param $status
     * @return mixed|string
     */
    public static function getStatusComment($status)
    {
        return isset(static::$statusComment[$status]) ? static::$statusComment[$status] : '';
    }




    /**
     * 通过 $pay_way 值获取 $pay_way 名称
     * @param $pay_way
     * @return mixed|string
     */
    public static function getPayWayComment($pay_way)
    {
        return isset(static::$payWayComment[$pay_way]) ? static::$payWayComment[$pay_way] : '';
    }




    /**
     * 通过字段 status 输出 status_name ;
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return static::getStatusComment($this->attributes['status']);
    }




    /**
     * 通过字段 pay_way 输出 pay_way_name ;
     * @return string
     */
    public function getPayWayNameAttribute()
    {
        return static::getPayWayComment($this->attributes['pay_way']);
    }




    public function atributeNames()
    {
        return [
            'member_id'     => '会员ID',
            'type'          => '提现类型',
            'amounts'       => '提现金额',
            'pay_way'       => '打款方式',
        ];
    }




    public function rules()
    {
        return  [
            'member_id'     => 'required',
            'type'          => 'required',
            'amounts'       => 'required',
            'pay_way'       => 'required',
        ];
    }





/********************* 以下代码不确定功能逻辑，需要处理删除 ****************/




    public $widgets = [];

    public $attributes = [];

    public $StatusService;

    public $PayWayService;

    public $TypeData;


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


    public static function getWithdrawByWithdrawSN($withdrawSN)
    {
        return self::uniacid()->where('withdraw_sn',$withdrawSN)->first();
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
//        $Model->with(['hasOneAgent' => function ($query) {
//            $query->select('member_id', 'agent_level_id', 'commission_total');
//        }]);

        return $Model;
    }




//    public function hasOneAgent()
//    {
//        return $this->hasOne('Yunshop\Commission\models\Agents', 'member_id', 'member_id');
//    }

    public static function updatedWithdrawStatus($id, $updatedData)
    {
        return self::where('id',$id)
            ->orWhere('withdraw_sn',(string)$id)
            ->update($updatedData);
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/1
 * Time: 上午11:14
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额充值记录数据表
 *
 * */
class BalanceRecharge extends BaseModel
{
    public $table = 'yz_balance_recharge';

    protected $guarded = [''];

    const PAY_TYPE_SHOP = 0;

    const PAY_TYPE_ORDER = -1;

    const PAY_TYPE_MEMBER = -2;

    /*
     * 模型管理，关联会员数据表
     *
     * @Author yitian */
    public function member()
    {
        return $this->hasOne('app\common\models\member', 'uid', 'member_id');
    }

    /*
     *
     *
     * */
    public static function getMemberRechargeRecord($memberId)
    {
        return self::uniacid()->select('id','money', 'type', 'created_at')->where('member_id', $memberId)->get();
    }

    /*
     * 通过记录ID值获取记录
     *
     * @params int $recordId 记录ID
     *
     * @return object
     * @Author yitian */
    public static function getRechargeRecordByid($recordId)
    {
        return self::uniacid()->where('id', $recordId)->first();
    }

    /*
     * 获取充值记录分页列表
     *
     * return object
     *
     * @Author yitian */
    public static function getPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query) {
                return $query->select('uid', 'nickname','realname','mobile','avatar')
                    ->with(['yzMember' => function($memberInfo) {
                        return $memberInfo->select('member_id', 'group_id', 'level_id')
                            ->with(['level' => function($level) {
                                return $level->select('id','level_name');
                            }])
                            ->with(['group'=> function($group) {
                                return $group->select('id', 'group_name');
                            }]);
                }]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
    }

    /*
     * 搜索充值记录分页列表
     *
     * @params int $pageSize
     * @params array $search
     * return object
     *
     * @Author yitian */
    public static function getSearchPageList($pageSize, $search =[])
    {
        $query = self::select('member_id')->whereHas('member', function ($query)use($search) {
            return $query->select('uid')->where('realname', 'like', '%'.$search['realname'].'%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate($pageSize);
        return $query;
    }

    /*
     * 验证订单号是否存在，存在返回true
     *
     * @params varchar $orderSN
     *
     * @return bool true or false
     *
     * @Author yitian */
    public static function validatorOrderSn($orderSN)
    {
        return self::uniacid()->where('ordersn', $orderSN)->first();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID不能为空",
            'member_id' => "会员ID不能为空",
            //'old_money' => '余额必须是有效的数字',
            'money'     => '充值金额必须是有效的数字，允许两位小数',
            'new_money' => '计算后金额必须是有效的数字',
            'type'      => '充值类型不能为空',
            'ordersn'   => '充值订单号不能为空',
            'status'    => '状态不能为空'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'member_id' => "required",
            //'old_money' => 'numeric',
            'money'     => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/',
            'new_money' => 'numeric',
            'type'      => 'required',
            'ordersn'   => 'required',
            'status'    => 'required'
        ];
    }

}

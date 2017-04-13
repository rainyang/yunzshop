<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use app\common\services\finance\BalanceService;

/*
 * 余额变动记录表
 *
 * */
class Balance extends BaseModel
{
    public $table = 'yz_balance';

    public $timestamps = false;

    protected $guarded= [''];



    const BALANCE_RECHARGE  = 1; //充值

    const BALANCE_CONSUME   = 2; //消费

    const BALANCE_TRANSFER  = 3; //转让

    const BALANCE_DEDUCTION = 4; //抵扣

    const BALANCE_AWARD     = 5; //奖励

    const BALANCE_WITHDRAWAL= 6; //余额提现

    const BALANCE_INCOME    = 7; //提现至余额

    const CANCEL_DEDUCTION  = 8; //抵扣取消余额回滚

    const CANCEL_AWARD      = 9; //奖励取消回滚

    /*
     * 模型管理，关联会员数据表
     *
     * @Author yitian */
    public function member()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    /**
     * 通过字段 type 输出 type_name ;
     * @return string
     * @Author yitian */
    public function getTypeNameAttribute()
    {
        return BalanceService::attachedTypeName($this);
    }

    /**
     * 通过字段 service_type 输出 service_type_name ;
     * @return string
     * @Author yitian */
    public function getServiceTypeNameAttribute()
    {
        return BalanceService::attachedServiceTypeName($this);
    }

    /*
     * 获取分页列表
     *
     * @params int $pageSize
     *
     * @return object
     * @Autho yitian */
    public static function getPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query) {
                return $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
    }

    /**
     * 前端接口，通过 type 查看会员余额变动明细
     * @param $memberId
     * @param string $type
     * @return mixed
     * @Author yitia */
    public static function getMemberDetailRecord($memberId, $type= '')
    {
        $query = self::uniacid()->where('member_id',$memberId);
        if ($type == \app\common\services\finance\Balance::INCOME || $type == \app\common\services\finance\Balance::EXPENDITURE) {
            $query = $query->where('type', $type);
        }
        return $query->get();
    }

    /**
     * 通过记录ID获取记录详情
     * @param $id
     * @return mixed
     * @Author yitian */
    public static function getDetailById($id)
    {
        return static::uniacid()->where('id', $id)
            ->with(['member' => function($member) {
                return $member->select('uid', 'nickname', 'realname', 'avatar', 'mobile', 'credit2');
            }])
            ->first();
    }

}

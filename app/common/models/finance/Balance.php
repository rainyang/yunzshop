<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/29
 * Time: 下午5:23
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

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
        return $this->hasOne('app\common\models\member', 'uid', 'member_id');
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

    public static function getMemberDeatilRecord($memberId, $type= '')
    {
        $query = self::uniacid()->where('member_id',$memberId);
        if ($type == \app\common\services\fiance\Balance::INCOME || $type == \app\common\services\fiance\Balance::EXPENDITURE) {
            $query = $query->where('type', $type);
        }
        return $query->get();
    }



}
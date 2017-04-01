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

    /*
     * 模型管理，关联会员数据表
     *
     * @Author yitian */
    public function member()
    {
        return $this->hasOne('app\common\models\member', 'uid', 'member_id');
    }

    /*
     * 通过主键ID获取记录
     *
     * @params int $rechargeId
     *
     * @return object
     *
     * @Author yitian */
    public static function getRecordById($rechargeId)
    {
        return self::uniacid()->where('id',$rechargeId)->first();
    }

    /*
     * 获取充值记录列表
     *
     * return object
     *
     * @Author yitian */
    public static function getRechargeRecord($pageSize)
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

}
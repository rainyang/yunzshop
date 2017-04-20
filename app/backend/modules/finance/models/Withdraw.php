<?php

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/31
 * Time: 下午3:05
 */
namespace app\backend\modules\finance\models;

class Withdraw extends \app\common\models\Withdraw
{
    protected $appends = ['type_data'];

    public static function getWithdrawList($search = [])
    {

        $Model = self::uniacid();
        if ($search['status'] == '3') {
            $Model->whereNotNull(arrival_at);
        } elseif (isset($search['status'])) {
            $Model->where('status', $search['status']);
        }

        if($search['member']) {
            $Model->whereHas('hasOneMember', function($query)use($search){
                return $query->searchLike($search['member']);
            });
        }
        if($search['withdraw_sn']) {
            $Model->where('withdraw_sn', $search['withdraw_sn']);
        }
        if($search['type']) {
            $Model->where('type', $search['type']);
        }
        if($search['searchtime']){
            if($search['times']){
                $range = [$search['times']['start'], $search['times']['end']];
                $Model->whereBetween('created_at', $range);
            }
        }

        $Model->with(['hasOneMember' => function ($query) {
            return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);
        return $Model;
    }

    public function rules()
    {
        return [
            'poundage' => 'regex:/^[\d]?(\.[\d]{0,2})?$/',
            'withdrawmoney' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'roll_out_limit' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'poundage_rate' => 'regex:/^[\d]{1,2}+(\.[0-9]{1,2})?$/',
        ];
    }

    public function atributeNames()
    {
        return [
            'poundage' => "提现手续费",
            'withdrawmoney' => "提现限制金额",
            'roll_out_limit' => "佣金提现额度",
            'poundage_rate' => "佣金提现手续费"
        ];
    }


}
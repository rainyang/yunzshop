<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/31
 * Time: 下午3:05
 */
namespace app\backend\modules\finance\models;

use app\common\facades\Setting;

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

    public static function getAllWithdraw($type)
    {
        $ids = '';
        $total = 0;

        $data = self::getWithdrawListForType($type)->get();

        if (!is_null($data)) {
            foreach ($data as $rows) {
                $ids .= $rows->id . ',';
            }
        }

        $ids = rtrim($ids, ',');
        $total = count($data);

        if ($total == 0 && $ids == '') {
            $status = 0;
            $msg    = '暂无数据';
        } elseif ($total != count(explode(',', $ids))) {
            $status = -1;
            $msg     = '数据不符';
        } else {
            $status = 1;
            $msg    = 'ok';
        }

        return ['status' => $status, 'totals' => $total, 'ids' => $ids, 'msg' => $msg];
    }

    public static function getWithdrawListForType($type, $limit=800, $status=1)
    {
        $Model = self::uniacid();

        switch ($type) {
            case 1:
                $Model->whereIn('type', ['balance']);
                break;
            case 2:
                $Model->whereNotIn('type', ['balance']);
                break;
        }

        $Model->where('status', $status)
            ->where('pay_way', 'alipay')
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        return $Model;
    }

    public static function updateWidthdrawOrderStatus($withdrawId)
    {
        return self::uniacid()
            ->whereIn('id', $withdrawId)
            ->update(['status' => 4]);
    }

    public function rules()
    {
        return [
            'poundage' => 'numeric|min:1|max:999999999',
            'withdrawmoney'     => 'numeric|min:0|max:999999999',
            'roll_out_limit'    => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'poundage_rate'     => 'regex:/^[\d]{1,2}+(\.[0-9]{1,2})?$/',
        ];
    }

    public function atributeNames()
    {
        return [
            'poundage'          => "提现手续费",
            'withdrawmoney'     => "提现限制金额",
            'roll_out_limit'    => "佣金提现额度",
            'poundage_rate'     => "佣金提现手续费"
        ];
    }


}
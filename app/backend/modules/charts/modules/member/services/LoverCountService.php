<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:33
 */

namespace app\backend\modules\charts\modules\member\services;


use app\backend\modules\charts\modules\member\models\MemberLowerCount;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class LoverCountService
{
    public function memberCount()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $uniacid = \YunShop::app()->uniacid;
            $level_all_member = DB::select('select member_id,level,count(1) as total from ims_yz_member_children where uniacid=' . $uniacid . ' and level in (1,2,3) group by member_id,level');
            $level_all_member = collect($level_all_member);
            $result = [];

            foreach ($level_all_member as $val) {
                if (!isset($result[$val['member_id']])) {
                    $result[$val['member_id']] = [
                        'uid' => $val['member_id'],
                        'uniacid' => $uniacid,
                        'first_total' => $val['total'],
                        'second_total' => 0,
                        'third_total' => 0,
                        'team_total' => $val['total']
                    ];
                } else {
                    switch ($val['level']) {
                        case 2:
                            $result[$val['member_id']]['second_total'] = $val['total'];
                            break;
                        case 3:
                            $result[$val['member_id']]['third_total'] = $val['total'];
                            break;
                    }

                    $result[$val['member_id']]['team_total'] += $val['total'];
                }
            }
//        dd($result);
            $memberModel = new MemberLowerCount();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }

        }
    }
}
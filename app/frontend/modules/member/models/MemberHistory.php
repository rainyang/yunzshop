<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 上午10:43
 */

namespace app\frontend\modules\member\models;


use Illuminate\Database\Eloquent\Model;

class MemberHistory extends Model
{
    public $table = 'sz_yi_member_history';


    /*
     *
     *
     * */
    public static function getMemberHistoryList($member_id, $uniacid)
    {
        $list = MemberHistory::where('goodsid', $member_id)
            ->where('uniacid', $uniacid)
            ->orderBy('createtime', 'desc')
            ->take(10)
            ->get();

        return $list;
    }
}
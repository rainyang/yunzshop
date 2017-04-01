<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/23
 * Time: 上午11:00
 */

/**
 * 会员辅助表
 */
namespace app\frontend\modules\member\models;

use app\common\models\MemberShopInfo;

class SubMemberModel extends MemberShopInfo
{
    public static function getInfo($uniacid, $referralsn)
    {
        return self::where('uniacid', $uniacid)
            ->where('referralsn', $referralsn)
            ->first()
            ->toArray();
    }

    public static function updateDate($data, $where)
    {
        self::where('mobile', $where['mobile'])
            ->where('uniacid', $where['uniacid'])
            ->update($data);
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public static function insertData($data)
    {
         self::insert($data);
    }
}
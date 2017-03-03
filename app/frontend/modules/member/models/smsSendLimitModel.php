<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/3/3
 * Time: 上午7:20
 */

namespace app\frontend\modules\member\models;


class smsSendLimitModel
{
    public $table = 'yz_sms_send_limit';

    public function getMobileInfo($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
                   ->where('mobile', $mobile)
                   ->first()
                   ->toArray();
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public function insertData($data)
    {
        self::insert($data);
    }

    /**
     * 更新更新短信条数，时间
     *
     * @param $where
     * @param $data
     */
    public function updateData($where, $data)
    {
        self::where('uniacid', $where['uniacid'])
            ->where('mobile', $where['mobile'])
            ->update($data);
    }
}
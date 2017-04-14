<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 上午10:39
 */

namespace app\backend\modules\finance\services;

use app\common\traits\MessageTrait;
use app\common\services\finance\PointService as PointServiceParent;

class PointService
{
    use MessageTrait;

    public function verifyPointRecharge($point, $member)
    {
        $result = false;
        if ($point) {
            $data = [
                'point_mode'        => 5,
                'member_id'         => $member->uid,
                'uniacid'           => \YunShop::app()->uniacid,
                'point'             => floatval($point)
            ];
            if ($point < 0) {
                if ($member->credit1 - $point < 0) {
                    $this->error('扣除后的积分不能小于0');
                    $result = true;
                }
                $data['point_income_type'] = -1;
                $data['remark'] = '后台扣除[' . $data['point'] . ']积分';
            } else {
                $data['point_income_type'] = 1;
                $data['remark'] = '后台充值[' . $data['point'] . ']积分';
            }
            if (!$result) {
                $point_service = new PointServiceParent($data);
                $point_model = $point_service->changePoint();
                if ($point_model) {
                    return '充值成功';
                }
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 上午10:39
 */

namespace app\backend\modules\finance\services;

use app\backend\modules\member\models\Member;
use app\common\traits\MessageTrait;
use app\common\services\finance\PointService as PointServiceParent;
use Setting;

class PointService
{
    use MessageTrait;

    /**
     * @name 验证并充值积分
     * @author yangyang
     * @param $point
     * @param Member $member
     * @return bool|string
     */
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
        return false;
    }

    /**
     * @name 验证设置数组
     * @author yangyang
     * @param array $point_data
     * @return bool|string
     */
    public function verifyPointData($point_data)
    {
        if ($point_data['money_max'] > 100) {
            $this->error('商品最高抵扣积分不能超过100%');
        } else {
            Setting::set('point.set', $point_data);
            return '积分基础设置保存成功';
        }
        return false;
    }

    /**
     * @name 获取积分基础设置
     * @author yangyang
     * @param array $point_data
     * @param array $enoughs_data
     * @param array $give
     * @return array
     */
    public static function getPointData($point_data, $enoughs_data, $give)
    {
        if (!empty($enoughs_data)) {
            $enoughs = [];
            foreach ($enoughs_data as $key => $value) {
                $enough = floatval($value);
                if ($enough > 0) {
                    $enoughs[] = array('enough' => floatval($enoughs_data[$key]), 'give' => floatval($give[$key]));
                }
            }
            $point_data['enoughs'] = $enoughs;
        }
        return $point_data;
    }
}
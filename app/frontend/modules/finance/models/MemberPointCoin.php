<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 下午3:28
 */

namespace app\frontend\modules\finance\models;

use app\common\models\VirtualCoin;
use app\common\services\finance\PointService;
use app\frontend\models\MemberCoin;

class MemberPointCoin extends MemberCoin
{
    /**
     * 获取最多可用积分
     * @return mixed
     */
    public function getMaxUsableCoin()
    {
        return (new PointCoin)->setCoin($this->member->credit1);
    }

    function consume(VirtualCoin $coin,$data)
    {
        $point_service = new PointService([
            'point_income_type' => -1,
            'point_mode' => 6,
            'member_id' => $this->member->uid,
            'point' => -$coin->getCoin(),
            'remark' => '订单[' . $data['order_sn'] . ']抵扣[' . $coin->getMoney() . ']元'
        ]);
        $point_service->changePoint();

        $this->member->credit1 -= $coin->getCoin();
        return $this->member->save();
    }
}
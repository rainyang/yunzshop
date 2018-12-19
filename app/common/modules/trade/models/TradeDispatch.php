<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/23
 * Time: 5:11 PM
 */

namespace app\common\modules\trade\models;

use app\common\models\BaseModel;


class TradeDispatch extends BaseModel
{
    /**
     * @var Trade
     */
    private $trade;

    public function init(Trade $trade)
    {
        $this->trade = $trade;
        $this->setRelation('default_member_address', $this->getMemberAddress());
        return $this;
    }

    /**
     * @return mixed
     */
    private function getMemberAddress()
    {
        return $this->trade->orders->first()->orderAddress->getMemberAddress();
    }

}
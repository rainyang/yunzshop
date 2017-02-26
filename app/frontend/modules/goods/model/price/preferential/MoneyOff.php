<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午2:55
 */
namespace app\frontend\modules\goods\model;
use app\frontend\modules\goods\model\factory\GoodsModelFactory;
use app\frontend\modules\goods\service\MemberService;


class MoneyOff
{
    private $_goods_model;

    public function __construct(GoodsModel $goods_model)
    {
        $this->_goods_model = $goods_model;

    }

    public function getDiscountPrice(){
        return $this->_goods_model - $this->_getMoneyOff();

    }
    //折扣设置是商品的一部分
    private function _getDiscountSetting()
    {
        //todo 根据表结构调整
        return $this->_goods_model["discounts"];
    }

    private function _getMarketPrice(){
        return $this->_market_price;
    }
    //从用户服务中获取当前用户等级设置
    private function _getMemberLevel(){
        return MemberService::getCurrentMemberModel()->getLevel();
    }
    //获取满减金额
    private function _getMoneyOff()
    {
        if (!is_array($this->_getDiscountSetting())) {
            return 0;
        }
        if (!empty($this->_getMemberLevel()["id"])) {
            if (floatval($this->_getDiscountSetting()["level" . $this->_getMemberLevel()["id"]]) < $this->_getMarketPrice()) {
                $result = floatval($this->_getDiscountSetting()["level" . $this->_getMemberLevel()["id"]]);
            } elseif (floatval($this->_getMemberLevel()["discount"]) < $this->_getMarketPrice()) {
                $result = floatval($this->_getMemberLevel()["discount"]);
            }
        } else {
            if (floatval($this->_getDiscountSetting()["default"]) > 0 && floatval($this->_getDiscountSetting()["default"]) < $this->_getMarketPrice()) {
                $result = floatval($this->_getDiscountSetting()["default"]);
            } elseif (floatval($this->_getMemberLevel()["discount"]) > 0 && floatval($this->_getMemberLevel()["discount"]) < $this->_getMarketPrice()) {
                $result = floatval($this->_getMemberLevel()["discount"]);
            }
        }

        return $result;
    }
}
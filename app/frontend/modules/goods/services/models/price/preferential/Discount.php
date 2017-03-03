<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/21
 * Time: 下午2:54
 */

namespace app\frontend\modules\goods\services\models\price\preferential;

use app\frontend\modules\goods\services\models\factory\GoodsModelFactory;
use app\frontend\modules\member\service\MemberService;



class Discount
{
    private $_goods_model;

    public function __construct(GoodsModel $goods_model)
    {
        $this->_goods_model = $goods_model;

    }

    public function getDiscountPrice()
    {
        return $this->_goods_model['marketprice'] * $this->_getDiscount();
    }
    //折扣设置是商品的一部分

    private function _getDiscountSetting()
    {
        //todo 根据表结构调整
        return $this->_goods_model["discounts"];
    }
    //从用户服务中获取当前用户等级设置
    private function _getMemberLevel(){
        return MemberService::getCurrentMemberModel()->getLevel();
    }
    //获取折扣金额
    private function _getDiscount()
    {
        if (is_array($this->_getDiscountSetting())) {
            return 0;
        }
        if (!empty($this->_getMemberLevel()["id"])) {
            if (floatval($this->_getDiscountSetting()["level" . $this->_getMemberLevel()["id"]]) > 0 && floatval($this->_getDiscountSetting()["level" . $this->_getMemberLevel()["id"]]) < 10) {
                $result = floatval($this->_getDiscountSetting()["level" . $this->_getMemberLevel()["id"]]);
            } else if (floatval($this->_getMemberLevel()["discount"]) > 0 && floatval($this->_getMemberLevel()["discount"]) < 10) {
                $result = floatval($this->_getMemberLevel()["discount"]);
            } else {
                $result = 0;
            }
        } else {
            if (floatval($this->_getDiscountSetting()["default"]) > 0 && floatval($this->_getDiscountSetting()["default"]) < 10) {
                $result = floatval($this->_getDiscountSetting()["default"]);
            } else if (floatval($this->_getMemberLevel()["discount"]) > 0 && floatval($this->_getMemberLevel()["discount"]) < 10) {
                $result = floatval($this->_getMemberLevel()["discount"]);
            } else {
                $result = 0;
            }
        }

        return $result;
    }
}
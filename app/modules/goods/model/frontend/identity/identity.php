<?php
namespace mobile\order\demo\model;
class Identity
{
    public function getMoneyOff(){

    }
    public function getDiscount($goods_model,$member_model){
        $level = $member_model->getLevel();
        $discounts = $goods_model->getDiscounts();
        if (is_array($discounts)) {
            if (!empty($level["id"])) {
                if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                    $level["discount"] = floatval($discounts["level" . $level["id"]]);
                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                    $level["discount"] = floatval($level["discount"]);
                } else {
                    $level["discount"] = 0;
                }
            } else {
                if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                    $level["discount"] = floatval($discounts["default"]);
                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                    $level["discount"] = floatval($level["discount"]);
                } else {
                    $level["discount"] = 0;
                }
            }
        }
        return $level;
    }
}
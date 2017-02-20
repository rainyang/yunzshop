<?php
namespace mobile\order\demo\model;
class commissionIdentity extends Identity
{
    public function getMoneyOff(){

    }
    public function getLevel($member_model){
        return p("commission")->getLevel($member_model->getOpenid());
    }

    public function getDiscount($member_model){
        $level = p('commission')->getLevel($this->getOpenid());
        //插件商品表
        $discounts = json_decode($g['discounts2'], true);
        //是分销商
        $level["discount"] = 0;
        if ($member_model->plugin()->get('isagent') == 1 && $member_model['status'] == 1) {
            if (is_array($discounts)) {
                if (!empty($level["id"])) {
                    if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                        $level["discount"] = floatval($discounts["level" . $level["id"]]);
                    }
                } else {
                    if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                        $level["discount"] = floatval($discounts["default"]);
                    }
                }
            }
        }
        return $level;
    }
}
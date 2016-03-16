<?php
/*QQ:261753427*/
if (!defined("IN_IA")) {
    print("Access Denied");
}
require IA_ROOT . "/addons/sz_yi/defines.php";
require EWEI_SHOP_INC . "plugin/plugin_processor.php";
class ExhelperProcessor extends PluginProcessor
{
    public function __construct()
    {
        parent::__construct("exhelper");
    }
    public function respond($zym_var_13 = null)
    {
        global $_W;
        $zym_var_10 = $zym_var_13->message;
        $zym_var_9  = $zym_var_13->message["from"];
        $zym_var_11 = $zym_var_13->message["content"];
        $zym_var_12 = strtolower($zym_var_10["msgtype"]);
        $zym_var_14 = strtolower($zym_var_10["event"]);
        if ($zym_var_12 == "text" || $zym_var_14 == "click") {
            $zym_var_8 = pdo_fetch("select * from " . tablename("sz_yi_saler") . " where openid=:openid and uniacid=:uniacid limit 1", array(
                ":uniacid" => $_W["uniacid"],
                ":openid" => $zym_var_9
            ));
            if (empty($zym_var_8)) {
                return $this->responseEmpty();
            }
            if (!$zym_var_13->inContext) {
                $zym_var_13->beginContext();
                return $zym_var_13->respText("请输入兑换码:");
            } else if ($zym_var_13->inContext && is_numeric($zym_var_11)) {
                $zym_var_7 = pdo_fetch("select * from " . tablename("sz_yi_creditshop_log") . " where eno=:eno and uniacid=:uniacid  limit 1", array(
                    ":eno" => $zym_var_11,
                    ":uniacid" => $_W["uniacid"]
                ));
                if (empty($zym_var_7)) {
                    return $zym_var_13->respText("未找到要兑换码,请重新输入!");
                }
                $zym_var_2 = $zym_var_7["id"];
                if (empty($zym_var_7)) {
                    return $zym_var_13->respText("未找到要兑换码,请重新输入!");
                }
                if (empty($zym_var_7["status"])) {
                    return $zym_var_13->respText("无效兑换记录!");
                }
                if ($zym_var_7["status"] >= 3) {
                    return $zym_var_13->respText("此记录已兑换过了!");
                }
                $zym_var_1 = m("member")->getMember($zym_var_7["openid"]);
                $zym_var_3 = $this->model->getGoods($zym_var_7["goodsid"], $zym_var_1);
                if (empty($zym_var_3["id"])) {
                    return $zym_var_13->respText("商品记录不存在!");
                }
                if (empty($zym_var_3["isverify"])) {
                    $zym_var_13->endContext();
                    return $zym_var_13->respText("此商品不支持线下兑换!");
                }
                if (!empty($zym_var_3["type"])) {
                    if ($zym_var_7["status"] <= 1) {
                        return $zym_var_13->respText("未中奖，不能兑换!");
                    }
                }
                if ($zym_var_3["money"] > 0 && empty($zym_var_7["paystatus"])) {
                    return $zym_var_13->respText("未支付，无法进行兑换!");
                }
                if ($zym_var_3["dispatch"] > 0 && empty($zym_var_7["dispatchstatus"])) {
                    return $zym_var_13->respText("未支付运费，无法进行兑换!");
                }
                $zym_var_4 = explode(",", $zym_var_3["storeids"]);
                if (!empty($zym_var_6)) {
                    if (!empty($zym_var_8["storeid"])) {
                        if (!in_array($zym_var_8["storeid"], $zym_var_6)) {
                            return $zym_var_13->respText("您无此门店的兑换权限!");
                        }
                    }
                }
                $zym_var_5 = time();
                pdo_update("sz_yi_creditshop_log", array(
                    "status" => 3,
                    "usetime" => $zym_var_5,
                    "verifyopenid" => $zym_var_9
                ), array(
                    "id" => $zym_var_7["id"]
                ));
                $this->model->sendMessage($zym_var_2);
                $zym_var_13->endContext();
                return $zym_var_13->respText("兑换成功!");
            }
        }
    }
    private function responseEmpty()
    {
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        print(0);
    }
}
?>

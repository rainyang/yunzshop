<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午5:45
 */

namespace app\frontend\modules\goods\model\price;

use app\frontend\modules\goods\model\GoodsModel;
use app\frontend\modules\member\service\MemberService;

class GoodsDispatchPrice
{
    private $_city;
    private $_goods_data;

    public function __construct(GoodsModel $goods_model)
    {
        $this->_goods_data = $goods_model->getInitialData();

        //todo db member city
        $this->_city = MemberService::getCurrentMemberModel()->getInitialData()['city'];
    }
    public function getDispatchPrice(){
        $g = $this->_getGoodsData();
        if ($g["dispatchtype"] == 1) {  //统一邮费
            if ($g["dispatchprice"] > 0) {
                return $g["dispatchprice"];
            }
        } else if ($g["dispatchtype"] == 0) {   //运费模板
            //$_goods['isAllSameDispath'] = false;
            if (empty($g["dispatchid"])) {
                $_goods['dispatch_data'] = m("order")->getDefaultDispatch($g['supplier_uid']);
            } else {
                $_goods['dispatch_data'] = m("order")->getOneDispatch($g["dispatchid"], $g['supplier_uid']);
            }
            if (empty($_goods['dispatch_data'])) {
                $_goods['dispatch_data'] = m("order")->getNewDispatch($g['supplier_uid']);
            }
            if (!empty($_goods['dispatch_data'])) {
                if ($_goods['dispatch_data']["calculatetype"] == 1) {
                    $_goods['param'] = $g["total"];
                } else {
                    $_goods['param'] = $g["weight"] * $g["total"];
                }
            }

            $areas = unserialize($_goods['dispatch_data']["areas"]);
            if (!($this->_address())) {
                $_goods['dispatch_price'] = m("order")->getCityDispatchPrice($areas, $this->_address("city"), $_goods['param'], $_goods['dispatch_data']);
            } else if (!empty($this->_address("city"))) {
                $_goods['dispatch_price'] = m("order")->getCityDispatchPrice($areas, $this->_address("city"), $_goods['param'], $_goods['dispatch_data']);
            } else {
                $_goods['dispatch_price'] = m("order")->getDispatchPrice($_goods['param'], $_goods['dispatch_data'], -1);
            }
        }
        return $_goods['dispatch_price'];
    }
    private function _getGoodsData(){
        return $this->_goods_data;

    }
    private function _city(){
        $this->_city;
    }
    //todo db goods_dispatch
    public function isFree(GoodsModel $goods_model){
        $g = $goods_model->getInitialData();

        $result = false;
        if (!empty($g["issendfree"])) { //包邮
            $result = true;
        } else {
            $gareas = explode(";", $g["edareas"]);  //不参加包邮地区
            if ($g["total"] >= $g["ednum"] && $g["ednum"] > 0) {    //单品满xx件包邮

                if (empty($gareas)) {
                    $result = true;
                } else {
                    if (!($this->_address())) {
                        if (!in_array($this->_address('city'), $gareas)) {
                            $result = true;
                        }
                    } else if (!empty($this->_city())) {
                        if (!in_array($this->_city(), $gareas)) {
                            $result = true;
                        }
                    } else {
                        $result = true;
                    }
                }
            }

            if ($g["ggprice"] >= floatval($g["edmoney"]) && floatval($g["edmoney"]) > 0) {  //满额包邮
                if (empty($gareas)) {
                    $result = true;
                } else {
                    if (!($this->_address())) {
                        if (!in_array($this->_address("city"), $gareas)) {
                            $result = true;
                        }
                    } else if (!empty($this->_city())) {
                        if (!in_array($this->_city(), $gareas)) {
                            $result = true;
                        }
                    } else {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }
    //todo db member_address
    private function _address($key = null)
    {
        $fields = 'id,realname,mobile,address,province,city,area';
        //是否开启街道联动
        $trade = m('common')->getSysset('trade');
        if ($trade['is_street'] == '1') {
            $fields .= ',street';
        }
        $result = pdo_fetch('select ' . $fields . ' from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

            ':uniacid' => ShopService::getCurrentShopModel()->getShopId(),
            ':openid' => MemberService::getCurrentMemberModel()->getOpenId()
        ));
        if (isset($key)) {
            return $result[$key];
        }
        return $result;
    }
}
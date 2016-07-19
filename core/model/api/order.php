<?php
/*=============================================================================
#     FileName: order.php
#         Desc: 订单类
#       Author: Shenyang
#        Email: 564345292@qq.com
#     HomePage: 
#      Version: 0.0.1
#   LastChange: 2016-07-18 18:34:01
#      History:
=============================================================================*/
namespace Api\Model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class Order
{
    public function __construct()
    {

    }
    protected $name = array(
        'pay_type'=>array(
            '0' => "未支付",
            "1" => "余额支付",
            "11" => "后台付款",
            "2" => "在线支付",
            "21" => "微信支付",
            "22" => "支付宝支付",
            "23" => "银联支付",
            "3" => "货到付款",
        ),
        'status'=>array(
            '-1' => "已关闭",
            "0" => "待付款",
            "1" => "待发货",
            "2" => "待收货",
            "3" => "完成",
        ),
        'r_type' => array(
            '0' => '退款',
            '1' => '退货退款',
            '2' => '换货'
        )
    );
    public function getList($para)
    {
        global $_W;
        $condition['status'] = $this->getStatusCondition($para['status']);
        $condition['pay_type'] = $this->getPayTypeCondition($para['pay_type']);
        if($para['is_supplier_uid']){
            $condition['supplier'] .= $this->getSupplierCondition($_W['uid']);
        }
        $condition['other'] = 'AND o.uniacid = :uniacid and o.deleted=0';
        $paras = array(
            ":uniacid" => $_W["uniacid"]
        );
        $condition_str = ' 1 ';
        $condition_str .= implode(' ',$condition);
        $sql = 'select o.ordersn,o.status,o.price ,o.id
from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " 
left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " 
left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " 
left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " 
left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  
where {$condition_str} ORDER BY o.createtime DESC,o.status DESC  ";

        $list = pdo_fetchall($sql, $paras);
        $list = $this->formatResult($list);
        return $list;
    }
    protected function formatResult($order_list){
        global $_W;
        $orderstatus = $this->name['status'];
        $r_type = $this->name['r_type'];
        $plugin_diyform = p("diyform");

        foreach ($order_list as & $value) {
            $s = $value["status"];
            $pt = $value["paytype"];
            $value["statusvalue"] = $s;
            $value["status"] = $orderstatus[$value["status"]];
            if ($pt == 3 && empty($value["statusvalue"])) {
                $value["status"] = $orderstatus[1];
            }
            if ($s == 1) {
                if ($value["isverify"] == 1) {
                    $value["status"] = "待使用";
                } else if (empty($value["addressid"])) {
                    $value["status"] = "待取货";
                }
            }
            if ($s == - 1) {
                $value['status'] = $value['rstatus'];
                if (!empty($value["refundtime"])) {
                    if ($value['rstatus'] == 1) {
                        $value['status'] = '已' . $r_type[$value['rtype']];
                    }
                }
            }


            $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
                ":uniacid" => $_W["uniacid"],
                ":orderid" => $value["id"]
            ));
            foreach ($order_goods as & $og) {

                $goods = "" . $og["title"] . "";
                if (!empty($og["optiontitle"])) {
                    $goods.= " 规格: " . $og["optiontitle"];
                }
                if (!empty($og["option_goodssn"])) {
                    $og["goodssn"] = $og["option_goodssn"];
                }
                if (!empty($og["option_productsn"])) {
                    $og["productsn"] = $og["option_productsn"];
                }
                if (!empty($og["goodssn"])) {
                    $goods.= " 商品编号: " . $og["goodssn"];
                }
                if (!empty($og["productsn"])) {
                    $goods.= " 商品条码: " . $og["productsn"];
                }
                $goods.= " 单价: " . ($og["price"] / $og["total"]) . " 折扣后: " . ($og["realprice"] / $og["total"]) . " 数量: " . $og["total"] . " 总价: " . $og["price"] . " 折扣后: " . $og["realprice"] . "";
                if ($plugin_diyform && !empty($og["diyformfields"]) && !empty($og["diyformdata"])) {
                    $diyformdata_array = $plugin_diyform->getDatas(iunserializer($og["diyformfields"]) , iunserializer($og["diyformdata"]));
                    $diyformdata = "";
                    foreach ($diyformdata_array as $da) {
                        $diyformdata.= $da["name"] . ": " . $da["value"] . "";
                    }
                    $og["goods_diyformdata"] = $diyformdata;
                }
                $og['goods_attribute'] = $goods;
                $og = array_part('id,thumb,title,price,total,goods_attribute',$og);
            }
            unset($og);
            $value["goods"] = set_medias($order_goods, "thumb");

        }
        unset($value);
        return $order_list;
    }
    protected function getSupplierCondition($uid){
        " and o.supplier_uid={$uid} ";
    }
    protected function getPayTypeCondition($pay_type){
        $condition='';
        if ($pay_type == "2") {
            $condition.= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition.= " AND o.paytype =" . intval($pay_type);
        }
        return $condition;
    }
    protected function getStatusCondition($status)
    {

        if ($status == "-1") {
            $statuscondition = " AND o.status=-1 and o.refundtime=0";
        } else if ($status == "4") {
            $statuscondition = " AND o.refundstate>=0 AND o.refundid<>0";
        } else if ($status == "5") {
            $statuscondition = " AND o.refundtime<>0";
        } else if ($status == "1") {
            $statuscondition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
        } else if ($status == '0') {
            $statuscondition = " AND o.status = 0 and o.paytype<>3";
        } else {
            $statuscondition = " AND o.status = " . intval($status);
        }
        return $statuscondition;
    }
}

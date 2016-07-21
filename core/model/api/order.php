<?php
/**
 * 订单model
 *
 * 管理后台 APP API 订单model
 *
 * @package   订单模块
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace Api\Model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Order
{
    public function __construct()
    {

    }
    /**
     * 订单表字段值字典
     *
     * @var Array
     */
    protected $name_map = array(
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
    /**
     * 获取订单详情
     *
     * 详细描述（略）
     * @param string $para 查询条件数组
     * @return array 订单详情数组
     */
    public function getInfo($para){
        $order_info = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $para['id'],
            ":uniacid" => $para["uniacid"]
        ));
        $order_info["statusvalue"] = $order_info["status"];
        return $order_info;
    }

    /**
     * 获取订单列表
     *
     * 详细描述（略）
     * @param string $para 查询条件数组
     * @return array 订单列表
     */
    public function getList($para)
    {
        global $_W;
        if((int)($para['status'])) {
            $condition['status'] = $this->getStatusCondition($para['status']);
        }
        if((int)($para['pay_type'])) {
            $condition['pay_type'] = $this->getPayTypeCondition($para['pay_type']);
        }
        if($para['is_supplier_uid']){
            $condition['supplier'] .= $this->getSupplierCondition($_W['uid']);
        }
        if(!empty($para['id'])){
            $condition['id'] = "AND o.id < {$para['id']}";
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
where {$condition_str} ORDER BY o.id,o.createtime DESC,o.status DESC LIMIT 0,10 ";

        $list = pdo_fetchall($sql, $paras);
        $list = $this->formatOrderList($list);
        return $list;
    }
    /**
     * 处理订单列表
     * 添加包含商品列表,翻译字段数字值等
     *
     * @param array $order_list 订单列表
     * @return array 处理过的订单列表
     */
    protected function formatOrderList($order_list){
        global $_W;
        $status_name_map = $this->name_map['status'];
        $r_type = $this->name_map['r_type'];
        foreach ($order_list as & $order_item) {
            $pay_type = $order_item["paytype"];
            $order_item["status_name"] = $status_name_map[$order_item["status"]];
            if ($pay_type == 3 && empty($order_item["status"])) {
                $order_item["status"] = $status_name_map[1];
            }
            if ($order_item["status"] == 1) {
                if ($order_item["isverify"] == 1) {
                    $order_item["status"] = "待使用";
                } else if (empty($order_item["addressid"])) {
                    $order_item["status"] = "待取货";
                }
            }
            if ($order_item["status"] == - 1) {
                $order_item['status'] = $order_item['rstatus'];
                if (!empty($order_item["refundtime"])) {
                    if ($order_item['rstatus'] == 1) {
                        $order_item['status'] = '已' . $r_type[$order_item['rtype']];
                    }
                }
            }
            $order_goods = $this->getOrderGoods($order_item["id"],$_W["uniacid"]);
            $order_item["goods"] = set_medias($order_goods, "thumb");
        }
        return $order_list;
    }
    /**
     * 获取订单商品列表
     * 查询,格式化,并返回商品列表
     *
     * @param int $order_id 订单表ID
     * @param int $uniacid  公众号ID
     * @return array 订单包含的商品列表
     */
    protected function getOrderGoods($order_id,$uniacid){
        $plugin_diyform = p("diyform");
        $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $uniacid,
            ":orderid" => $order_id
        ));
        foreach ($order_goods as & $goods_item) {

            $goods = "" . $goods_item["title"] . "";
            if (!empty($goods_item["optiontitle"])) {
                $goods.= " 规格: " . $goods_item["optiontitle"];
            }
            if (!empty($goods_item["option_goodssn"])) {
                $goods_item["goodssn"] = $goods_item["option_goodssn"];
            }
            if (!empty($goods_item["option_productsn"])) {
                $goods_item["productsn"] = $goods_item["option_productsn"];
            }
            if (!empty($goods_item["goodssn"])) {
                $goods.= " 商品编号: " . $goods_item["goodssn"];
            }
            if (!empty($goods_item["productsn"])) {
                $goods.= " 商品条码: " . $goods_item["productsn"];
            }
            $goods.= " 单价: " . ($goods_item["price"] / $goods_item["total"]) . " 折扣后: " . ($goods_item["realprice"] / $goods_item["total"]) . " 数量: " . $goods_item["total"] . " 总价: " . $goods_item["price"] . " 折扣后: " . $goods_item["realprice"] . "";
            if ($plugin_diyform && !empty($goods_item["diyformfields"]) && !empty($goods_item["diyformdata"])) {
                $diyformdata_array = $plugin_diyform->getDatas(iunserializer($goods_item["diyformfields"]) , iunserializer($goods_item["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata.= $da["name"] . ": " . $da["value"] . "";
                }
                $goods_item["goods_diyformdata"] = $diyformdata;
            }
            //todo 应该取goods_option数组
            $goods_item['goods_attribute'] = $goods;
            $goods_item = array_part('id,thumb,title,price,total,goods_attribute',$goods_item);
        }
        return $order_goods;
    }
    /**
     * 返回供应商查询条件
     * 供应商sql查询条件
     *
     * @param int $uid 管理员ID
     * @return string 供应商sql查询条件字符串
     */
    protected function getSupplierCondition($uid){
        " and o.supplier_uid={$uid} ";
    }
    /**
     * 返回支付方式查询条件
     * 供应商sql查询条件
     *
     * @param int $uid 管理员ID
     * @return string 支付方式sql查询条件字符串
     */
    protected function getPayTypeCondition($pay_type){
        if ($pay_type == "2") {
            $condition= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition= " AND o.paytype =" . intval($pay_type);
        }
        return $condition;
    }
    /**
     * 返回订单状态查询条件
     * 订单状态sql查询条件
     *
     * @param int $status 订单状态值
     * @return string 订单状态sql查询条件字符串
     */
    protected function getStatusCondition($status)
    {
        switch ($status){
            case "-1":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "4":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "5":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "1":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "0":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            default:
                $condition = " AND o.status = " . intval($status);
        }
        return $condition;

    }
}

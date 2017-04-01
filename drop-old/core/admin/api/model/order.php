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
namespace admin\api\model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class order
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
        'pay_type' => array(
            '0' => "未支付",
            "1" => "余额支付",
            "11" => "后台付款",
            "2" => "在线支付",
            "21" => "微信支付",
            "22" => "支付宝支付",
            "23" => "银联支付",
            "3" => "货到付款",
        ),
        'status' => array(
            '-1' => "已关闭",
            "0" => "待付款",
            "1" => "待发货",
            "2" => "待收货",
            "3" => "已完成",
        ),
        'r_type' => array(
            '0' => '退款',
            '1' => '退货退款',
            '2' => '换货'
        )
    );

    public function getPayTypeName()
    {
        return $this->name_map['pay_type'];
    }

    /**
     * 获取订单详情
     *
     * 详细描述（略）
     * @param string $para 查询条件数组
     * @return array 订单详情数组
     */
    public function getInfo($para, $fields = '*')
    {
        $order_info = pdo_fetch("SELECT {$fields} FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $para['id'],
            ":uniacid" => $para["uniacid"]
        ));
        $order_info = $this->_formatOrderInfo($order_info);
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
        //dump($para);
        $condition[] = ' 1';
        if ($para['status'] !== '') {
            $condition['status'] = $this->_getStatusCondition((int)$para['status']);
        }
        if ((int)($para['pay_type'])) {
            $condition['pay_type'] = $this->_getPayTypeCondition($para['pay_type']);
        }
        if ($para['is_supplier_uid']) {
            $condition['supplier'] = $this->_getSupplierCondition($_W['uid']);
        }
        if (!empty($para['id'])) {
            $condition['id'] = "AND o.id < {$para['id']}";
        }
        $condition['other'] = 'AND o.uniacid = :uniacid and o.deleted=0';
        $paras = array(
            ":uniacid" => $_W["uniacid"]
        );

        $condition_str = implode(' ', $condition);
        $sql = 'select o.ordersn_general as ordersn,o.status,o.price ,o.id as order_id,o.changedispatchprice,o.changeprice,r.rtype,r.status as rstatus,o.isverify,o.isvirtual,o.addressid
from ' . tablename("sz_yi_order") . " o" . " 
left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " 
left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " 
left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " 
left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " 
left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  
where {$condition_str} ORDER BY o.id DESC LIMIT 0,10 ";
        $list = pdo_fetchall($sql, $paras);
        foreach ($list as &$order_item) {
            $order_item = $this->_formatOrderInfo($order_item);
        }
        return $list;
    }

    /**
     * 处理订单详情
     * 添加包含商品列表,翻译字段数字值等
     *
     * @param array $order_list 订单列表
     * @return array 处理过的订单列表
     */
    private function _formatOrderInfo($order_info)
    {
        //dump($order_info);
        global $_W;
        $pay_type = $order_info["paytype"];
        $order_status = $order_info["status"];

        $order_info["status_name"] = $this->_getStatusName($order_info["status"], $pay_type, $order_info);
        $refund_name = $this->_getRefundName($order_status, $order_info["rstatus"], $order_info["refundtime"], $order_info["rtype"]);
        $order_info["status_name"] = !empty($refund_name) ? $refund_name : $order_info["status_name"];//建议退款状态另起一个字段名,不要占用status_name

        $order_info['pay_type_name'] = $this->_getPayTypeName($pay_type);
        $order_info['button_info'] = $this->_getButton($pay_type, $order_status, $order_info['addressid'], $order_info['isverify'], $order_info['redstatus']);


        $order_goods = $this->getOrderGoods($order_info["order_id"], $_W["uniacid"]);
        $order_info["goods"] = $order_goods;
        //dump($order_info);
        //$res_order_info = array_part('ordersn,status,price,order_id,goods',$order_info);
        return $order_info;
    }

    private function _getStatusName($order_status, $pay_type, $order_info)
    {
        //todo 需要重构提取出其余的order_info 
        $status_name_map = $this->name_map['status'];
        $status_name = $status_name_map[$order_status];
        switch ($order_status) {
            case "0":
                if ($pay_type == 3) {
                    $status_name = '待发货';
                }
                break;
            case '1':
                if ($order_info['isverify'] == 1) {
                    $status_name = '待使用';
                } else if (empty($order_info['addressid'])) {
                    $status_name = '待取货';
                }
                break;
            case '3':
                if (empty($order_info['iscomment'])) {
                    $status_name = '待评价';
                }
                break;
        }
        return $status_name;
    }

    private function _getRefundName($order_status, $refund_status, $refund_time, $refund_type)
    {
        $refund_type_name_mapping = $this->name_map['r_type'];
        if ($order_status == -1) {
            //dump(order_info['rstatus']);
            //$order_info['status'] = $order_info['rstatus'];
            if (!empty($refund_time)) {
                if ($refund_status == 1) {
                    $refund_name = '已' . $refund_type_name_mapping[$refund_type];
                }
            }
        }
        return $refund_name;
    }

    private function _getButton($pay_type, $order_status, $address_id, $is_verify, $red_status = 0)
    {
        $button_mapping = array(
            '' => '',
            '确认付款' => 1,// order/ChangeStatus/confirmPay
            '确认发货' => 2,// order/ChangeStatus/confirmSend
            '确认核销' => 3,// order/ChangeStatus/confirmFetch
            '确认取货' => 4,// order/ChangeStatus/confirmFetch
            '确认收货' => 5,// order/ChangeStatus/order/ChangeStatus/finish
            '取消发货' => 6,// order/ChangeStatus/order/ChangeStatus/cancelSend
            '补发红包' => 7,// order/ChangeStatus/sendRedPack
            '查看物流' => 8
        );
        $button_name = '';
        if (empty($order_status)) {
            if (cv('order.op.pay')) {
                if ($pay_type == 3) {
                    $button_name = '确认发货';
                } else {
                    $button_name = '确认付款';
                }
            }
        } elseif ($order_status == 1) {
            if (!empty($address_id)) {
                if (cv('order.op.send')) {
                    $button_name = '确认发货';
                }
            } else {
                if ($is_verify) {
                    if (cv('order.op.verify')) {
                        $button_name = '确认核销';
                    }
                } else {
                    if (cv('order.op.fetch')) {
                        $button_name = '确认取货';
                    }
                }
            }

        } elseif ($order_status == 2) {
            if (!empty($address_id)) {
                //此处不懂为什么显示查看物流,保留代码以防返工
                /*if (cv('order.op.sendcancel')) {
                    $button_name = '取消发货';
                }*/
                //if (cv('order.op.sendcancel')) {
                $button_name = '查看物流';
                //}
            }
        } elseif ($order_status == 3) {
            /*if (!empty($red_status)) {
                $button_name = '补发红包';
            }*/
            if (!empty($address_id)) {
                $button_name = '查看物流';
            }
        }

        $value = $button_mapping[$button_name];
        $name = $button_name;
        $button = array(
            'name'=>$name,
            'value'=>$value
        );
        return $button;
    }

    private function _getPayTypeName($pay_type)
    {
        $pay_type_name_map = $this->name_map['pay_type'];
        $pay_type_name = $pay_type_name_map[$pay_type];
        return $pay_type_name;
    }

    public function getRefundInfo($orer_id, $uniacid)
    {
        $refund = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order_refund") . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc", array(
            ":orderid" => $orer_id,
            ":uniacid" => $uniacid
        ));
        //dump($refund);
        if (!empty($refund)) {
            if (!empty($refund['imgs'])) {
                $refund['imgs'] = iunserializer($refund['imgs']);
            }
            $refund['refundtype'] = array(
                'name' => $this->name_map['r_type'][$refund['refundtype']],
                'value' => $refund['refundtype'],
            );
            if ($refund['status'] == 0 || $refund['status'] >= 3) {
                $refund['refund_name'] = '处理申请';
            } elseif ($refund['status'] == -1) {
                $refund['refund_name'] = '已拒绝';
            } elseif ($refund['status'] == -2) {
                $refund['refund_name'] = '客户取消';
            } elseif ($refund['status'] == 1) {
                $refund['refund_name'] = '已完成';
            }
        }
        if (empty($refund)) {
            $refund = array('imgs', 'refundtype' => array('name' => '', 'value' => ''), 'status', 'refund_name');
        }
        return $refund;
    }

    public function getPriceInfo($order_info)
    {
        $price_info = array(
            'goodsprice' => $order_info['goodsprice'],//商品小计
            'olddispatchprice' => $order_info['olddispatchprice'],//运费
            'price' => $order_info['price'],//应收
            'deductenough' => $order_info['deductenough'],//满减
            'changeprice' => $order_info['changeprice'],//改价
            'changedispatchprice' => $order_info['changedispatchprice'],//改运费
        );
        array_map(function ($item) {
            return number_format($item, 2);
        }, $price_info);
        return $price_info;
    }

    /**
     * 获取订单商品列表
     * 查询,格式化,并返回商品列表
     *
     * @param int $order_id 订单表ID
     * @param int $uniacid 公众号ID
     * @return array 订单包含的商品列表
     */
    public function getOrderGoods($order_id, $uniacid)
    {
        $plugin_diyform = p("diyform");
        $order_goods = pdo_fetchall("select g.id as goods_id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $uniacid,
            ":orderid" => $order_id
        ));
        foreach ($order_goods as & $goods_item) {

            $goods = "" . $goods_item["title"] . "";
            if (!empty($goods_item["optiontitle"])) {
                $goods .= " 规格: " . $goods_item["optiontitle"];
            }
            if (!empty($goods_item["option_goodssn"])) {
                $goods_item["goodssn"] = $goods_item["option_goodssn"];
            }
            if (!empty($goods_item["option_productsn"])) {
                $goods_item["productsn"] = $goods_item["option_productsn"];
            }
            if (!empty($goods_item["goodssn"])) {
                $goods .= " 商品编号: " . $goods_item["goodssn"];
            }
            if (!empty($goods_item["productsn"])) {
                $goods .= " 商品条码: " . $goods_item["productsn"];
            }
            $goods .= " 单价: " . ($goods_item["price"] / $goods_item["total"]) . " 折扣后: " . ($goods_item["realprice"] / $goods_item["total"]) . " 数量: " . $goods_item["total"] . " 总价: " . $goods_item["price"] . " 折扣后: " . $goods_item["realprice"] . "";
            if ($plugin_diyform && !empty($goods_item["diyformfields"]) && !empty($goods_item["diyformdata"])) {
                $diyformdata_array = $plugin_diyform->getDatas(iunserializer($goods_item["diyformfields"]), iunserializer($goods_item["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata .= $da["name"] . ": " . $da["value"] . "";
                }
                $goods_item["goods_diyformdata"] = $diyformdata;
            }
            //todo 应该取goods_option数组
            $goods_item['goods_attribute'] = $goods;
            $goods_item = array_part('goods_id,thumb,title,price,total,goods_attribute', $goods_item);
        }
        $order_goods = set_medias($order_goods, "thumb");
        return $order_goods;
    }

    /**
     * 返回供应商查询条件
     * 供应商sql查询条件
     *
     * @param int $uid 管理员ID
     * @return string 供应商sql查询条件字符串
     */
    protected function _getSupplierCondition($uid)
    {
        " and o.supplier_uid={$uid} ";
    }

    /**
     * 返回支付方式查询条件
     * 供应商sql查询条件
     *
     * @param int $uid 管理员ID
     * @return string 支付方式sql查询条件字符串
     */
    protected function _getPayTypeCondition($pay_type)
    {
        if ($pay_type == "2") {
            $condition = " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition = " AND o.paytype =" . intval($pay_type);
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
    protected function _getStatusCondition($status)
    {
        switch ($status) {
            case "-1":
                $condition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "4":
                $condition = " AND o.refundstate>=0 AND o.refundid<>0";
                break;
            case "5":
                $condition = " AND o.refundtime<>0";
                break;
            case "1":
                $condition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
                break;
            case "0":
                $condition = " AND o.status = 0 and o.paytype<>3";
                break;
            default:
                $condition = " AND o.status = " . intval($status);
        }
        return $condition;

    }
}

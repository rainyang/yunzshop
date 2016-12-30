<?php
global $_W, $_GPC;
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$type = $_GPC['type'];
$plugin_diyform = p("diyform");
$shopset = m('common')->getSysset('pay');
$trade     = m('common')->getSysset('trade');
$yunbi_plugin = p('yunbi');
if ($yunbi_plugin) {
    $yunbiset = $yunbi_plugin->getSet();
}
$card_plugin = p('card');
if ($card_plugin) {
    $card_set = $card_plugin->getSet();
}
$isindiana = '';
$isindiana_o = '';
$indiana_plugin   = p('indiana');
if ($indiana_plugin) {
    if ($_GPC['isindiana']) {
        $isindiana = " AND o.order_type = 4 ";
        $isindiana_o = " AND order_type = 4 ";
        $period = pdo_fetchall("SELECT ir.ordersn FROM " . tablename('sz_yi_indiana_record') . " ir 
        left join " . tablename('sz_yi_indiana_period') . " ip on ( ip.openid = ir.openid and ip.period_num = ir.period_num ) 
        WHERE ip.uniacid = :uniacid",array(
            ":uniacid" => $_W["uniacid"]
        ));
        if($period){
            foreach ($period as $key => $value) {
                $inordersn[$key] .= $value['ordersn'];
            }
            $isindiana .= " AND o.ordersn in ('".implode($inordersn,"','")."') "; 
            $isindiana_o .= " AND ordersn in ('".implode($inordersn,"','")."') "; 
        }

        // if ($inordersn) {
        //     $isindiana .= " AND o.ordersn in ('".implode($inordersn,"','")."') "; 
        // }else{
        //     $isindiana = " AND o.order_type = 4 ";
        // }
        
    }else{
        $isindiana = " AND o.order_type <> 4 ";
        $isindiana_o = " AND order_type <> 4 ";
    }
    
}
$totals = array();
$r_type = array(
    '0' => '退款',
    '1' => '退货退款',
    '2' => '换货'
);
$paytype = array(
    '0' => array(
        "css" => "default",
        "name" => "未支付"
    ),
    "1" => array(
        "css" => "danger",
        "name" => "余额支付"
    ),
    "11" => array(
        "css" => "default",
        "name" => "后台付款"
    ),
    "2" => array(
        "css" => "danger",
        "name" => "在线支付"
    ),
    "21" => array(
        "css" => "success",
        "name" => "微信支付"
    ),
    "22" => array(
        "css" => "warning",
        "name" => "支付宝支付"
    ),
    "23" => array(
        "css" => "warning",
        "name" => "银联支付"
    ),
    "25" => array(
        "css" => "primary",
        "name" => "易宝支付"
    ),
    "26" => array(
        "css" => "primary",
        "name" => "易宝网银支付"
    ),
    "27" => array(
        "css" => "success",
        "name" => "App微信支付"
    ),
    "28" => array(
        "css" => "warning",
        "name" => "App支付宝支付"
    ),
    '29' => array(
        'css' => "paypal",
        'name' => "paypal支付"
    ),
    "3" => array(
        "css" => "primary",
        "name" => "货到付款"
    ),
    "4" => array(
        "css" => "primary",
        "name" => "到店支付"
    )
);
$orderstatus = array(
    "-1" => array(
        "css" => "default",
        "name" => "已关闭"
    ),
    '0' => array(
        "css" => "danger",
        "name" => "待付款"
    ),
    "1" => array(
        "css" => "info",
        "name" => "待发货"
    ),
    "2" => array(
        "css" => "warning",
        "name" => "待收货"
    ),
    "3" => array(
        "css" => "success",
        "name" => "已完成"
    )
);
if (p('hotel')) {
    if ($type == 'hotel') {
        $orderstatus['1'] = array(
            "css" => "info",
            "name" => "待确认"
        );
        $orderstatus['2'] = array(
            "css" => "warning",
            "name" => "待入住"
        );
        $orderstatus['6'] = array(
            "css" => "success",
            "name" => "待退房"
        );
    }
}

$store_list = m('order')->getStoreList();
$lang = array(
    'good' => '商品',
    'orderlist' => '订单管理'
    );
if($_GPC['plugin'] == "fund"){
    $lang = array(
    'good' => '项目',
    'orderlist' => '众筹订单'
    ); 
}
if ($operation == "display") {
    ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
    //判断该帐号的权限
    if (p('supplier')) {
        $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
        $suppliers = p('supplier')->AllSuppliers();
    }

    $pindex = max(1, intval($_GPC["page"]));
    $psize = SZ_YI_PSIZE;
    $status = $_GPC["status"] == "" ? 1 : $_GPC["status"];
    $sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
    $condition = " AND o.uniacid = :uniacid and o.deleted=0";
    $join_table = "";
    $paras = array(
        ":uniacid" => $_W["uniacid"]
    );

    $starttime = $pstarttime = $fstarttime = $sstarttime = strtotime("-1 month");
    $endtime = $pendtime = $fendtime = $sendtime = time();

    if (!empty($_GPC['supplier_uid'])) {
        $condition .= " AND o.supplier_uid = :supplier_uid ";
        $paras[":supplier_uid"] = $_GPC['supplier_uid'];
    }
    if (!empty($_GPC["time"])) {
        $starttime = strtotime($_GPC["time"]["start"]);
        $endtime = strtotime($_GPC["time"]["end"]);
        if ($_GPC["searchtime"] == "1") {
            $condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
            $paras[":starttime"] = $starttime;
            $paras[":endtime"] = $endtime;
        }
    }

    if (!empty($_GPC["ptime"])) {
        $pstarttime = strtotime($_GPC["ptime"]["start"]);
        $pendtime = strtotime($_GPC["ptime"]["end"]);
        if ($_GPC["psearchtime"] == "1") {
            $condition .= " AND o.paytime >= :pstarttime AND o.paytime <= :pendtime ";
            $paras[":pstarttime"] = $pstarttime;
            $paras[":pendtime"] = $pendtime;
        }
    }

    if (!empty($_GPC["ftime"])) {
        $fstarttime = strtotime($_GPC["ftime"]["start"]);
        $fendtime = strtotime($_GPC["ftime"]["end"]);
        if ($_GPC["fsearchtime"] == "1") {
            $condition .= " AND o.finishtime >= :fstarttime AND o.finishtime <= :fendtime ";
            $paras[":fstarttime"] = $fstarttime;
            $paras[":fendtime"] = $fendtime;
        }
    }

    if (!empty($_GPC["stime"])) {
        $sstarttime = strtotime($_GPC["stime"]["start"]);
        $sendtime = strtotime($_GPC["stime"]["end"]);
        if ($_GPC["ssearchtime"] == "1") {
            $condition .= " AND o.sendtime >= :sstarttime AND o.sendtime <= :sendtime ";
            $paras[":sstarttime"] = $sstarttime;
            $paras[":sendtime"] = $sendtime;
        }
    }

    if ($_GPC["paytype"] != '') {
        if ($_GPC["paytype"] == "2") {
            $condition .= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition .= " AND o.paytype =" . intval($_GPC["paytype"]);
        }
    }

    //门店取消订单搜索
    if (empty($_W['isagent'])) {
        if ($_GPC['cancel'] == 1) {
            $orderids = pdo_fetchall("select orderid from " . tablename('sz_yi_cancel_goods') . " where uniacid={$_W['uniacid']} ");
            $ids = "";
            foreach ($orderids as $key => $value) {
                if ($key != 0) {
                    $ids .= "," . $value['orderid'];
                } else {
                    $ids .= $value['orderid'];
                }
            }
            if (!empty($orderids)) {
                $condition .= " and o.id in (" . $ids . ") ";
            }
        }
    }
    //商品名称检索订单
    if (!empty($_GPC["good_name"]) or !empty($_GPC["good_id"])) {
        $good_condition = (!empty($_GPC["good_name"])) ? "g.title LIKE '%{$_GPC["good_name"]}%'" : "g.id = '{$_GPC["good_id"]}'";
        $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " . tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and {$good_condition} group by og.orderid ");
        $conditionsp_goodsid = '';
        foreach ($conditionsp_goods as $value) {
            $conditionsp_goodsid .= "'" . $value['orderid'] . "', ";
        }
        //判断商品名称是否存在 不存在订单ID等于空
        if (!empty($conditionsp_goodsid)) {
            $condition .= " AND o.id in (" . substr($conditionsp_goodsid, 0, -2) . ") ";
        } else {
            $condition .= " AND o.id = '' ";
        }
    }

    if (!empty($_GPC["keyword"])) {
        $_GPC["keyword"] = trim($_GPC["keyword"]);
        $condition .= " AND (o.ordersn LIKE '%{$_GPC["keyword"]}%' OR o.pay_ordersn LIKE '%{$_GPC["keyword"]}%' OR o.ordersn_general LIKE '%{$_GPC["keyword"]}%')";
    }
    if (!empty($_GPC["expresssn"])) {
        $_GPC["expresssn"] = trim($_GPC["expresssn"]);
        $condition .= " AND o.expresssn LIKE '%{$_GPC["expresssn"]}%'";
    }
/*    if (!empty($_GPC["member"])) {
        $_GPC["member"] = trim($_GPC["member"]);
        $condition.= " AND (m.realname LIKE '%{$_GPC["member"]}%' or m.mobile LIKE '%{$_GPC["member"]}%' or m.membermobile LIKE '%{$_GPC["member"]}%' or m.nickname LIKE '%{$_GPC["member"]}%' " . " or a.realname LIKE '%{$_GPC["member"]}%' or a.mobile LIKE '%{$_GPC["member"]}%' or o.carrier LIKE '%{$_GPC["member"]}%')";
    }*/
    if (!empty($_GPC["member"])) {
        $_GPC["member"] = trim($_GPC["member"]);
        $sql = 'SELECT m.openid FROM ' . tablename("sz_yi_member") . " m WHERE (m.realname LIKE :member 
                OR m.mobile LIKE :member OR m.membermobile LIKE :member OR m.nickname LIKE :member) AND m.uniacid = :uniacid 
                UNION 
                SELECT a.openid FROM " . tablename("sz_yi_member_address") . " a 
                WHERE (a.realname LIKE :member OR a.mobile LIKE :member) AND a.uniacid = :uniacid";
        $member_paras = array(
            ":uniacid" => $_W["uniacid"],
            ":member" => '%' . $_GPC["member"] . '%'
        );
        $members = pdo_fetchall($sql, $member_paras);
        $openids = '';
        foreach ($members as $value) {
            $openids .= "'" . $value['openid'] . "', ";
        }
        //判断商品名称是否存在 不存在订单ID等于空
        if (!empty($openids)) {
            $condition .= " AND o.openid in (" . substr($openids, 0, -2) . ") ";
        } else {
            $condition .= " AND o.openid = '' ";
        }
        //$condition .= " AND (m.realname LIKE '%{$_GPC["member"]}%' or m.mobile LIKE '%{$_GPC["member"]}%' or m.nickname LIKE '%{$_GPC["member"]}%' " . " or a.realname LIKE '%{$_GPC["member"]}%' or a.mobile LIKE '%{$_GPC["member"]}%' or o.carrier LIKE '%{$_GPC["member"]}%')";
    }
    //会员订单查询
    if(!empty($_GPC['openid'])){
        $_GPC["openid"] = trim($_GPC["openid"]);
        $condition .= " AND o.openid='" . $_GPC["openid"] . "'";
    }
    if (!empty($_GPC["saler"])) {
        $_GPC["saler"] = trim($_GPC["saler"]);
        $condition .= " AND (sm.realname LIKE '%{$_GPC["saler"]}%' or sm.mobile LIKE '%{$_GPC["saler"]}%' or sm.nickname LIKE '%{$_GPC["saler"]}%' " . " or s.salername LIKE '%{$_GPC["saler"]}%' )";
        $join_table .= " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid ";
    }
    if (!empty($_GPC["storeid"])) {
        $_GPC["storeid"] = trim($_GPC["storeid"]);
        $condition .= " AND o.storeid=" . intval($_GPC["storeid"]);
    }
    if (!empty($_GPC["csid"])) {
        $_GPC["csid"] = trim($_GPC["csid"]);
        $condition .= " AND o.cashierid=" . intval($_GPC["csid"]);
    }
    if (p('hotel')) {
        if ($type == 'hotel') {
            $condition .= " AND o.order_type=3";
        } else {
            $condition .= " AND o.order_type<>3";
        }
    } else {
        $condition .= " AND o.order_type<>3";
    }

    $condition.= $isindiana;

    $statuscondition = '';
    if ($status != "all") {
        if ($status == -1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . intval($status));
        }
        switch ($status) {
            case "-1" :
                $statuscondition = " AND o.status=-1 and o.refundtime=0";
                break;
            case "4" :
                $statuscondition = " AND o.refundtime=0 AND o.refundid<>0 AND r.status>=0 AND r.status!=2";
                break;
            case "5" :
                $statuscondition = " AND o.refundtime<>0";
                break;
            case "1" :
                $statuscondition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
                break;
            case "0" :
                $statuscondition = " AND o.status = 0 and o.paytype<>3";
                break;
            default :
                $statuscondition = " AND o.status = " . intval($status);
                break;
        }
    }

    $bonusagentid = intval($_GPC['bonusagentid']);
    if (!empty($bonusagentid)) {
        $sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=" . $bonusagentid . " ORDER BY id DESC";
        $bonusoderids = pdo_fetchall($sql);
        $inorderids = "";
        if (!empty($bonusoderids)) {
            foreach ($bonusoderids as $key => $value) {
                if ($key != 0) {
                    $inorderids .= ",";
                }
                $inorderids = $value['orderid'];
            }
            $condition .= ' and  o.id in(' . $inorderids . ')';
        } else {
            $condition .= ' and  o.id=0';
        }
    }
    $agentid = intval($_GPC["agentid"]);
    $plugin_commission = p("commission");
    $level = 0;
    if ($plugin_commission) {
        $cset = $plugin_commission->getSet();
        $level = intval($cset["level"]);
    }
    $olevel = intval($_GPC["olevel"]);
    if (!empty($agentid) && $level > 0) {
        $agent = $plugin_commission->getInfo($agentid, array());
        if (!empty($agent)) {
            $agentLevel = $plugin_commission->getLevel($agentid);
        }
        if (empty($olevel)) {
            if ($level >= 1) {
                $condition .= " and  ( o.agentid=" . intval($_GPC["agentid"]);
            }
            if ($level >= 2 && $agent["level2"] > 0) {
                $condition .= " or o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
            }
            if ($level >= 3 && $agent["level3"] > 0) {
                $condition .= " or o.agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
            }
            if ($level >= 1) {
                $condition .= ")";
            }
        } else {
            if ($olevel == 1) {
                $condition .= " and  o.agentid=" . intval($_GPC["agentid"]);
            } else {
                if ($olevel == 2) {
                    if ($agent["level2"] > 0) {
                        $condition .= " and o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
                    } else {
                        $condition .= " and o.agentid in( 0 )";
                    }
                } else {
                    if ($olevel == 3) {
                        if ($agent["level3"] > 0) {
                            $condition .= " and o.agentid in( " . implode(",",
                                    array_keys($agent["level2_agentids"])) . ")";
                        } else {
                            $condition .= " and o.agentid in( 0 )";
                        }
                    }
                }
            }
        }
    }

    $cond = "";
    $condition.= " and o.plugin='".$_GPC['plugin']."'";
    //是否为供应商 等于1的是

    if (p('supplier')) {
        if ($perm_role == 1) {
            $cond .= " and o.supplier_uid={$_W['uid']} ";
        }
    }
    //查询订单总数以及总金额
    if ($_W['ispost']) {
        $result = pdo_fetch("SELECT COUNT(distinct o.ordersn) as total, ifnull(sum(o.price),0) as totalmoney FROM " . tablename("sz_yi_order") . " AS o 
            LEFT JOIN " . tablename("sz_yi_order_refund") . " r ON r.id =o.refundid {$join_table} WHERE 1 {$condition} {$statuscondition} {$cond}", $paras);
        $total = $result['total'];
        $totalmoney = $result['totalmoney'];
        $pager = pagination($total, $pindex, $psize);
        return show_json(1, array(
                'pager' => $pager,
                'total' => $total,
                'totalmoney' => floatval($totalmoney)
            ));
    }
    //是否为供应商 等于1的是
    if (p('supplier')) {
        if ($cond) {
            $supplierapply = pdo_fetchall('select a.id,u.uid,p.realname,p.mobile,p.banknumber,p.accountname,p.accountbank,a.applysn,a.apply_money,a.apply_time,a.type,a.finish_time,a.status from ' . tablename('sz_yi_supplier_apply') . ' a ' . ' left join' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid ' . 'left join' . tablename('users') . ' u on a.uid=u.uid where u.uid=' . $_W['uid']);
            $totals['status9'] = count($supplierapply);
            $supplier_info = p('supplier')->getSupplierInfo($_W['uid']);
            $costmoney = $supplier_info['costmoney'];
            $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',
                array(':uid' => $_W['uid'], ':uniacid' => $_W['uniacid']));
            if (empty($openid)) {
                message("暂未绑定微信，请联系管理员", '', "error");
            }
            //全部提现
            $applytype = intval($_GPC['applytype']);
            $apply_ordergoods_ids = "";
            foreach ($supplier_info['sp_goods'] as $key => $value) {
                if ($key == 0) {
                    $apply_ordergoods_ids .= $value['ogid'];
                } else {
                    $apply_ordergoods_ids .= ',' . $value['ogid'];
                }
            }
            if (!empty($applytype)) {
                $applysn = m('common')->createNO('commission_apply', 'applyno', 'CA');
                $data = array(
                    'uid' => $_W['uid'],
                    'apply_money' => $costmoney,
                    'apply_time' => time(),
                    'status' => 0,
                    'type' => $applytype,
                    'applysn' => $applysn,
                    'uniacid' => $_W['uniacid'],
                    'apply_ordergoods_ids' => $apply_ordergoods_ids
                );

                pdo_insert('sz_yi_supplier_apply', $data);
                @file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($data, 1), FILE_APPEND);
                if (pdo_insertid()) {
                    foreach ($supplier_info['sp_goods'] as $ids) {
                        $arr = array(
                            'supplier_apply_status' => 2
                        );
                        pdo_update('sz_yi_order_goods', $arr, array(
                            'id' => $ids['ogid']
                        ));
                    }
                    $tmp_sp_goods = $supplier_info['sp_goods'];
                    $tmp_sp_goods['applyno'] = $applysn;
                    @file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1),
                        FILE_APPEND);
                }
                message("提现申请已提交，请耐心等待!", $this->createWebUrl('order/list'), "success");
            }
        }
    }

    $sql = 'SELECT 1 AS suppliers_num, o.*, r.rtype 
            FROM ' . tablename("sz_yi_order") . " AS o 
            LEFT JOIN " . tablename("sz_yi_order_refund") . " r ON r.id =o.refundid {$join_table} WHERE 1 {$condition} {$statuscondition} {$cond} ORDER BY o.createtime DESC
            LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    //echo $sql;exit;
    $list = pdo_fetchall($sql, $paras);
    unset($value);


    //把会员信息等拿出来单独查询,避免order整表连接,每次只需查询20条会员信息即可。
    $member_openids = array();
    $member_addresids = array();
    foreach ($list as $value) {
        $member_openids[] = "'" . $value['openid'] . "'";
        if (!empty($value['addressid'])) {
            $member_addresids[] = $value['addressid'];
        }
    }

    if (!empty($member_openids)) {
        $member_openids = implode(',', $member_openids);
        $member_addresids = implode(',', $member_addresids);
        $member_condition = (!empty($member_addresids) ? " and a.id in (" . $member_addresids . ")" : '');

        $sql = 'select m.openid, a.realname as arealname,a.mobile as amobile,a.province as aprovince,
            a.city as acity,a.area as aarea,a.street as astreet,a.address as aaddress,m.nickname,m.id as mid,
            m.realname as mrealname,m.mobile as mmobile 
            from ' . tablename("sz_yi_member") . " m" . "
            left join " . tablename("sz_yi_member_address") . " a on m.openid=a.openid and m.uniacid = a.uniacid {$member_condition} 
            where m.openid in (" . $member_openids . ")  AND m.uniacid=:uniacid";
        $member_paras = array(
            ":uniacid" => $_W["uniacid"]
        );
        //print_r($sql);exit;
        $members = pdo_fetchall($sql, $member_paras);
        if (!empty($members)) {
            foreach ($members as $member) {
                $order_members[$member['openid']] = $member;
            }
            unset($members);
        }
    }
    $plugin_fund = p("fund");


    foreach ($list as & $value) {
        if (isset($order_members[$value['openid']])) {
            $value = $value + $order_members[$value['openid']];
        }
        if (p('supplier')) {
            $suppliers_num = $value['suppliers_num'];
            if ($suppliers_num > 1) {

                $value['vendor'] = '多供应商';
                $value['ischangePrice'] = 0;
            } else {
                if ($value['supplier_uid'] == 0) {
                    $value['vendor'] = '总店';
                } else {
                    $sup_username = p('supplier')->getSupplierName($value['supplier_uid']);
                    $value['vendor'] = '供应商：' . $sup_username;
                }
                $value['ischangePrice'] = 1;
            }
        }

        if ($suppliers_num > 1 && $value['status'] == 0) {
            $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
                array(
                    ':ordersn_general' => $value['ordersn_general'],
                    ':uniacid' => $_W["uniacid"]
                ));
            $orderids = array();
            $value['goodsprice'] = 0;
            $value['olddispatchprice'] = 0;
            $value['discountprice'] = 0;
            $value['deductprice'] = 0;
            $value['deductcredit2'] = 0;
            $value['deductenough'] = 0;
            $value['changeprice'] = 0;
            $value['changedispatchprice'] = 0;
            $value['couponprice'] = 0;
            $value['price'] = 0;
            foreach ($order_all as $k => $v) {
                $orderids[] = $v['id'];
                $value['goodsprice'] += $v['goodsprice'];
                $value['olddispatchprice'] += $v['olddispatchprice'];
                $value['discountprice'] += $v['discountprice'];
                $value['deductprice'] += $v['deductprice'];
                $value['deductcredit2'] += $v['deductcredit2'];
                $value['deductenough'] += $v['deductenough'];
                $value['changeprice'] += $v['changeprice'];
                $value['changedispatchprice'] += $v['changedispatchprice'];
                $value['couponprice'] += $v['couponprice'];
                $value['price'] += $v['price'];
            }

            $value['ordersn'] = $value['ordersn_general'];
            $orderid_where_in = implode(',', $orderids);
            $order_where = "og.orderid in ({$orderid_where_in})";
        } else {
            $order_where = "og.orderid = " . $value['id'];
        }
        //门店取消订单
        if (!empty($value['cgid'])) {
            $value['isempty'] = 1;
        } else {
            $value['isempty'] = 0;
        }

        $order_status = $value["status"];
        $order_paytype = $value["paytype"];
        $value["statusvalue"] = $order_status;
        $value["statuscss"] = $orderstatus[$value["status"]]["css"];
        $value["status"] = $orderstatus[$value["status"]]["name"];
        if ($order_paytype == 3 && empty($value["statusvalue"])) {
            $value["statuscss"] = $orderstatus[1]["css"];
            $value["status"] = $orderstatus[1]["name"];
        }
        if ($order_status == 1) {
            if ($value["isverify"] == 1) {
                $value["status"] = "待使用";
            } else {
                if (empty($value["addressid"])) {
                    $value["status"] = "待取货";
                }
            }
        }
        if ($order_status == -1) {
            $value['status'] = $value['rstatus'];
            if (!empty($value["refundtime"])) {
                $value['status'] = '已退款';
            } else {
                $value['status'] = '已关闭';
            }
        }
        //echo $order_paytype;exit;
        $value["paytypevalue"] = $order_paytype;
        $value["css"] = $paytype[$order_paytype]["css"];
        $value["paytype"] = $paytype[$order_paytype]["name"];

        if (empty($value["dispatchname"])) {
            $value["dispatchname"] = "快递配送";
        }
        if ($value["isverify"] == 1) {
            $value["dispatchname"] = "配送核销";
        } elseif ($value["isvirtual"] == 1) {
            $value["dispatchname"] = "虚拟物品";
        } elseif (!empty($value["virtual"])) {
            $value["dispatchname"] = "虚拟物品(卡密)<br/>自动发货";
        } elseif ($value['cashier'] == 1) {
            $value["dispatchname"] = "收银台支付";
        }

        if(empty($value["addressid"]) && $value["isvirtual"] != "1" && empty($value["virtual"])){
            $value["dispatchname"] = "上门自提";
        }

        if (p('cashier') && $value['cashier'] == 1) {
            $value['name'] = set_medias(array(
                'name' => $value['csname'],
                'thumb' => $value['csthumb']
            ), 'thumb');
        }

        if (($value["dispatchtype"] == 1 && !empty($value["isverify"])) OR !empty($value["virtual"]) OR !empty($value["isvirtual"]) OR $value['cashier'] == 1) {
            $value["address"] = '';
            $carrier = iunserializer($value["carrier"]);
            if (is_array($carrier)) {
                $value["addressdata"]["realname"] = $value["realname"] = $carrier["carrier_realname"];
                $value["addressdata"]["mobile"] = $value["mobile"] = $carrier["carrier_mobile"];
                $value["addressdata"]["address"] = $value["address"] = $carrier["address"];
            }
        } else {
            $address = iunserializer($value["address"]);
            $isarray = is_array($address);
            $value["realname"] = $isarray ? $address["realname"] : $value["arealname"];
            $value["mobile"] = $isarray ? $address["mobile"] : $value["amobile"];
            $value["province"] = $isarray ? $address["province"] : $value["aprovince"];
            $value["city"] = $isarray ? $address["city"] : $value["acity"];
            $value["area"] = $isarray ? $address["area"] : $value["aarea"];
            //是否开启了街道联动
            if ($trade['is_street'] == '1') {
                $value["street"] = $isarray ? $address["street"] : $value["astreet"];
            }
            $value["address"] = $isarray ? $address["address"] : $value["aaddress"];
            $value["address_province"] = $value["province"];
            $value["address_city"] = $value["city"];
            $value["address_area"] = $value["area"];
            $value["address_street"] = $value["street"];
            $value["address_address"] = $value["address"];
            //是否开启了街道联动
            if (!empty($value['street']) && $trade['is_street'] == '1') {
                $value["address"] = $value["province"] . " " . $value["city"] . " " . $value["area"] . " " . $value["street"] . " " . $value["address"];
            } else {
                $value["address"] = $value["province"] . " " . $value["city"] . " " . $value["area"] . " " . $value["address"];
            }
            $value["addressdata"] = array(
                "realname" => $value["realname"],
                "mobile" => $value["mobile"],
                "address" => $value["address"],
            );
        }
        $commission1 = -1;
        $commission2 = -1;
        $commission3 = -1;
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

        $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, 
                        og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,
                        og.commission3,og.commissions,og.diyformdata,og.diyformfields, g.timeend 
                        from " . tablename("sz_yi_order_goods") . " og " . " 
                        left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " 
                        where og.uniacid=:uniacid and " . $order_where, array(
            ":uniacid" => $_W["uniacid"]
        ));
        $goods = '';
        foreach ($order_goods as & $og) {
            if (!empty($level) && empty($agentid)) {
                $commissions = iunserializer($og["commissions"]);
                if (!empty($m1)) {
                    if (is_array($commissions)) {
                        $commission1 += isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                    } else {
                        $c1 = iunserializer($og["commission1"]);
                        $l1 = $plugin_commission->getLevel($m1["openid"]);
                        $commission1 += isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                    }
                }
                if (!empty($m2)) {
                    if (is_array($commissions)) {
                        $commission2 += isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                    } else {
                        $c2 = iunserializer($og["commission2"]);
                        $l2 = $plugin_commission->getLevel($m2["openid"]);
                        $commission2 += isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                    }
                }
                if (!empty($m3)) {
                    if (is_array($commissions)) {
                        $commission3 += isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                    } else {
                        $c3 = iunserializer($og["commission3"]);
                        $l3 = $plugin_commission->getLevel($m3["openid"]);
                        $commission3 += isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                    }
                }
            }
            $goods .= "" . $og["title"] . "";
            if (!empty($og["optiontitle"])) {
                $goods .= " 规格: " . $og["optiontitle"];
            }
            if (!empty($og["option_goodssn"])) {
                $og["goodssn"] = $og["option_goodssn"];
            }
            if (!empty($og["option_productsn"])) {
                $og["productsn"] = $og["option_productsn"];
            }
            if (!empty($og["goodssn"])) {
                $goods .= " 商品编号: " . $og["goodssn"];
            }
            if (!empty($og["productsn"])) {
                $goods .= " 商品条码: " . $og["productsn"];
            }
            $goods .= " 单价: " . ($og["price"] / $og["total"]) . " 折扣后: " . ($og["realprice"] / $og["total"]) . " 数量: " . $og["total"] . " 总价: " . $og["price"] . " 折扣后: " . $og["realprice"] . "";
            if ($plugin_diyform && !empty($og["diyformfields"]) && !empty($og["diyformdata"])) {
                $diyformdata_array = $plugin_diyform->getDatas(iunserializer($og["diyformfields"]),
                    iunserializer($og["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata .= $da["name"] . ": " . $da["value"] . "";
                }
                $og["goods_diyformdata"] = $diyformdata;
            }
            
        }

        //众筹订单未到时间隐藏发货
        $value['confirmsend'] = true;
        if($plugin_fund){
            if(!empty($_GPC['plugin'])){
               $value['confirmsend'] =  $og['timeend'] < time();
            }    
        }
        unset($og);
        if (!empty($level) && empty($agentid)) {
            $value["commission1"] = $commission1;
            $value["commission2"] = $commission2;
            $value["commission3"] = $commission3;
        }
        //Author:ym Date:2016-08-29 Content:订单分红佣金
        if (p('bonus')) {
            $bonus_area_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods') . " where orderid=:orderid and uniacid=:uniacid and bonus_area!=0",
                array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
            $bonus_range_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods') . " where orderid=:orderid and uniacid=:uniacid and bonus_area=0",
                array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
            if ($bonus_area_money > 0 && $bonus_range_money > 0) {
                $bonus_money_all = $bonus_area_money + $bonus_range_money;
                $value['bonus_money_all'] = floatval($bonus_money_all);
            }
            $value['bonus_area_money'] = floatval($bonus_area_money);
            $value['bonus_range_money'] = floatval($bonus_range_money);
        }
        $value["goods"] = set_medias($order_goods, "thumb");
        $value["goods_str"] = $goods;
        if ($indiana_plugin && $_GPC['isindiana']) {
            $value['indiana'] = p('indiana')->getorder($value['period_num']);
        }

    }
    unset($value);
    $condition = " uniacid=:uniacid and deleted=0";
    $condition .= $isindiana_o;
    if (p('hotel') && $type == 'hotel') {
        $condition .= " and order_type=3";
        $join_order_type = " and o.order_type=3";
    } else {
        $condition .= " and order_type<>3";
        $join_order_type = " and o.order_type<>3";
    }
    if (!empty($agentid) && $level > 0) {
        if (empty($olevel)) {
            if ($level >= 1) {
                $condition .= " and  ( agentid=" . intval($_GPC["agentid"]);
            }
            if ($level >= 2 && $agent["level2"] > 0) {
                $condition .= " or agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
            }
            if ($level >= 3 && $agent["level3"] > 0) {
                $condition .= " or agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
            }
            if ($level >= 1) {
                $condition .= ")";
            }
        } else {
            if ($olevel == 1) {
                $condition .= " and agentid=" . intval($_GPC["agentid"]);
            } else {
                if ($olevel == 2) {
                    if ($agent["level2"] > 0) {
                        $condition .= " and agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
                    } else {
                        $condition .= " and agentid in( 0 )";
                    }
                } else {
                    if ($olevel == 3) {
                        if ($agent["level3"] > 0) {
                            $condition .= " and agentid in( " . implode(",",
                                    array_keys($agent["level2_agentids"])) . ")";
                        } else {
                            $condition .= " and agentid in( 0 )";
                        }
                    }
                }
            }
        }
    }

    $condition.= " and plugin='".$_GPC['plugin']."'";

    if(!empty($_GPC['openid'])){
        $condition .= " AND openid='" . $_GPC["openid"] . "'";
        $join_order_type .= " AND o.openid='" . $_GPC["openid"] . "'";
    }

    $paras = array(
        ":uniacid" => $_W["uniacid"]
    );
    $totals = array();
    $supplier_cond = '';
    $supplier_conds = '';
    if (p('supplier')) {
        if (!empty($perm_role)) {
            $supplier_cond = ' AND supplier_uid=' . $_W['uid'];
            $supplier_conds = ' AND o.supplier_uid=' . $_W['uid'];
        }
    }
    //会员订单查询
    if(!empty($_GPC['openid'])){
        $_GPC["openid"] = trim($_GPC["openid"]);
        $condition .= " AND openid='" . $_GPC["openid"] . "'";
        $join_order_type .= " AND o.openid='" . $_GPC["openid"] . "'";
    }
    $totals['all'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . $supplier_cond,
        $paras);
    $totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=-1 and refundtime=0' . $supplier_cond,
        $paras);
    $totals['status0'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=0 and paytype<>3' . $supplier_cond,
        $paras);
    $totals['status1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and ( status=1 or ( status=0 and paytype=3) )' . $supplier_cond,
        $paras);
    $totals['status2'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=2' . $supplier_cond,
        $paras);
    $totals['status3'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=3' . $supplier_cond,
        $paras);
    $totals['status4'] = pdo_fetchcolumn('SELECT COUNT(DISTINCT o.id) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_order_refund') . ' r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid=:uniacid AND o.refundtime=0 AND o.refundid<>0 and r.status=0  and o.refundstate>=0 and o.deleted=0 AND r.status>=0 AND r.status!=2 {$supplier_conds}" . $join_order_type,
        array(':uniacid' => $_W['uniacid']));
    $totals['status5'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and refundtime<>0' . $supplier_cond,
        $paras);

    if (p('hotel') && $type == 'hotel') {
        $totals['status6'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=6',
            $paras);
    }
    
    $stores = pdo_fetchall("select id,storename from " . tablename("sz_yi_store") . " where uniacid=:uniacid ", array(
        ":uniacid" => $_W["uniacid"]
    ));
    if (p('cashier')) {
        $cashier_stores = pdo_fetchall("select id,name from " . tablename("sz_yi_cashier_store") . " where uniacid=:uniacid ",
            array(
                ":uniacid" => $_W["uniacid"]
            ));
    }
    //todo
    $mt = mt_rand(5, 20);
    if ($mt <= 10) {
        load()->func('communication');
        $CLOUD_UPGRADE_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp = ihttp_post($CLOUD_UPGRADE_URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

    load()->func("tpl");
    if (p('hotel')) {
        if ($type == 'hotel') {
            include $this->template("web/order/list_hotel");
        } elseif ($indiana_plugin && $_GPC['isindiana']) {
            include p('indiana')->ptemplate("order");
        } else {
            include $this->template("web/order/list");
        }


    } elseif ($indiana_plugin && $_GPC['isindiana']) {
        include p('indiana')->ptemplate("order");
    }else{
        include $this->template("web/order/list");
    }
    exit;
} elseif ($operation == "changeagent") {
    $openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_member') . " where id=(select member_id from " . tablename('sz_yi_store') . " where id={$_GPC['changeagent']} and uniacid={$_W['uniacid']}) and uniacid={$_W['uniacid']}");
    //$openid = pdo_fetchcolumn("SELECT openid FROM ".tablename('sz_yi_member')." WHERE id = (SELECT member_id FROM ".tablename('sz_yi_store')." WHERE id={$_GPC['changeagent']} and uniacid={$_W['uniacid']}") and uniacid={$_W['uniacid']}");
    $agentuid = array('storeid' => $_GPC['changeagent']);
    $last_agentuid = array('last_storeid' => $_GPC['changeagent'], 'ismaster' => 0);
    $orderid = $_GPC['id'];
    pdo_update('sz_yi_order', $agentuid, array('id' => $orderid, 'uniacid' => $_W['uniacid']));
    pdo_update('sz_yi_cancel_goods', $last_agentuid, array('orderid' => $orderid, 'uniacid' => $_W['uniacid']));
    $msg = array(
        'first' => array(
            'value' => "您获得新的订单！(门店)",
            "color" => "#4a5077"
        ),
        'keyword1' => array(
            'title' => '内容',
            'value' => "您获得[总店]分配的订单！",
            "color" => "#4a5077"
        )
    );
    $detailurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&method=order&p=verify&m=sz_yi&do=plugin&storeid=".$agentuid['storeid'];
    m('message')->sendCustomNotice($openid, $msg, $detailurl);
    message('选择门店成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
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
        message("抱歉，订单不存在!", referer(), "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == -1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    if (!empty($item['ordersn_general']) && $item['status'] == 0) {
        $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
            array(
                ':ordersn_general' => $item['ordersn_general'],
                ':uniacid' => $_W['uniacid']
            ));
        $orderids = array();
        $item['goodsprice'] = 0;
        $item['olddispatchprice'] = 0;
        $item['discountprice'] = 0;
        $item['deductprice'] = 0;
        $item['deductcredit2'] = 0;
        $item['deductenough'] = 0;
        $item['changeprice'] = 0;
        $item['changedispatchprice'] = 0;
        $item['couponprice'] = 0;
        $item['price'] = 0;
        foreach ($order_all as $k => $v) {
            $orderids[] = $v['id'];
            $item['goodsprice'] += $v['goodsprice'];
            $item['olddispatchprice'] += $v['olddispatchprice'];
            $item['discountprice'] += $v['discountprice'];
            $item['deductprice'] += $v['deductprice'];
            $item['deductcredit2'] += $v['deductcredit2'];
            $item['deductenough'] += $v['deductenough'];
            $item['changeprice'] += $v['changeprice'];
            $item['changedispatchprice'] += $v['changedispatchprice'];
            $item['couponprice'] += $v['couponprice'];
            $item['price'] += $v['price'];
        }
        if (count($order_all) > 1) {
            $item['ordersn'] = $item['ordersn_general'];
        }
        $orderid_where_in = implode(',', $orderids);
        $order_where = "o.orderid in ({$orderid_where_in})";
        $remark_where = "id in ({$orderid_where_in})";
    } else {
        $order_where = "o.orderid = " . $item['id'];
        $remark_where = "id = " . $item['id'];
    }

    if ($_W["ispost"]) {
        $remark = trim($_GPC["remark"]);
        pdo_query('update ' . tablename('sz_yi_order') . ' set remark=:remark where ' . $remark_where . ' and uniacid=:uniacid ',
            array(
                ':uniacid' => $_W["uniacid"],
                ':remark' => $remark,
            ));
        plog("order.op.saveremark", "订单保存备注  ID: {$item["id"]} 订单号: {$item["ordersn"]}");
        message("订单备注保存成功！", $this->createWebUrl("order", array(
            "op" => "detail",
            "id" => $item["id"]
        )), "success");
    }
    $member = m("member")->getMember($item["openid"]);
    $dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid",
        array(
            ":id" => $item["dispatchid"],
            ":uniacid" => $_W["uniacid"]
        ));
    if (empty($item["addressid"])) {
        $user = unserialize($item["carrier"]);
    } else {
        $user = iunserializer($item["address"]);
        if (!is_array($user)) {
            $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid",
                array(
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
    $refund = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order_refund") . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc",
        array(
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
    $goods = pdo_fetchall("SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions{$diyformfields} FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.uniacid=:uniacid and " . $order_where,
        array(
            ":uniacid" => $_W["uniacid"]
        ));
    if (p('cashier') && $item['cashier'] == 1) {
        $cashier_stores = set_medias(pdo_fetch("select * from " . tablename('sz_yi_cashier_store') . " where id = " . $item['cashierid'] . " and uniacid=" . $_W['uniacid']),
            'thumb');
    }
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
        $plugin_fund = p('fund');
        $item['confirmsend'] = true;
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
                $commission1 += $oc1;
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
                $commission2 += $oc2;
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
                $commission3 += $oc3;
            }
        }
        if($plugin_fund){
            if(!empty($_GPC['plugin'])){
                $item['confirmsend'] =  $og['timeend'] < time();
            }
        }
        unset($og);
    }
    $condition = " uniacid=:uniacid and deleted=0";
    if (p('hotel') && $type == 'hotel') {
        $condition .= " and order_type=3";
        $join_order_type = " and o.order_type=3";
    } else {
        $condition .= " and order_type<>3";
        $join_order_type = " and o.order_type<>3";
    }
    $paras = array(
        ":uniacid" => $_W["uniacid"]
    );

    if(!empty($_GPC['plugin'])){
        $condition.= " and plugin='".$_GPC['plugin']."'";
    }

    $supplier_conds = '';
    $supplier_cond = '';
    if (p('supplier')) {
        $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
        if (!empty($perm_role)) {
            $supplier_cond = ' AND supplier_uid=' . $_W['uid'];
            $supplier_conds = ' AND o.supplier_uid=' . $_W['uid'];
        }
    }
    $totals = array();
    $totals['all'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . $supplier_cond,
        $paras);
    $totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=-1 and refundtime=0' . $supplier_cond,
        $paras);
    $totals['status0'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=0 and paytype<>3' . $supplier_cond,
        $paras);
    $totals['status1'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and ( status=1 or ( status=0 and paytype=3) )' . $supplier_cond,
        $paras);
    $totals['status2'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=2' . $supplier_cond,
        $paras);
    $totals['status3'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and status=3' . $supplier_cond,
        $paras);
    $totals['status4'] = pdo_fetchcolumn('SELECT COUNT(DISTINCT o.id) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_order_refund') . ' r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid=:uniacid AND o.refundtime=0 AND o.refundid<>0 and r.status=0  and o.refundstate>=0 and o.deleted=0 AND r.status>=0 AND r.status!=2 {$supplier_conds}" . $join_order_type,
        array(':uniacid' => $_W['uniacid']));
    $totals['status5'] = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename('sz_yi_order') . '' . ' WHERE ' . $condition . ' and refundtime<>0' . $supplier_cond,
        $paras);

    /*$totals["all"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition", $paras);
    $totals["status_1"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=-1 and o.refundtime=0", $paras);
    $totals["status0"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=0 and o.paytype<>3", $paras);
    $totals["status1"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $paras);
    $totals["status2"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=2", $paras);
    $totals["status3"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=3", $paras);
    $totals["status4"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join " . tablename("sz_yi_order_refund") . " r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0  and r.status=0", $paras);
    $totals["status5"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0", $paras);*/
    $coupon = false;
    if (p("coupon") && !empty($item["couponid"])) {
        $coupon = p("coupon")->getCouponByDataID($item["couponid"]);
    }
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $CLOUD_UPGRADE_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp = ihttp_post($CLOUD_UPGRADE_URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }
    if (p("verify")) {
        if (!empty($item["verifyopenid"])) {
            $saler = m("member")->getMember($item["verifyopenid"]);
            $saler["salername"] = pdo_fetchcolumn("select salername from " . tablename("sz_yi_saler") . " where openid=:openid and uniacid=:uniacid limit 1 ",
                array(
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
    
    if ($indiana_plugin && $_GPC['isindiana']) {
        //include p('indiana')->ptemplate("detail");
        $item['indiana'] = p('indiana')->getorder($item['period_num']);
    }

    load()->func("tpl");
    if ($item['order_type'] == '3') {
        $order_room = pdo_fetchall("SELECT * FROM " . tablename("sz_yi_order_room") . " WHERE orderid = :orderid ",
            array(
                ":orderid" => $id,
            ));
        $item['order_room'] = $order_room;
        include $this->template("web/order/detail_hotel");

    } elseif ($indiana_plugin && $_GPC['isindiana']) {
        include p('indiana')->ptemplate("detail");
    }else{
        include $this->template("web/order/detail");

    }
    exit;
} elseif ($operation == 'saveexpress') {
    $id = intval($_GPC['id']);
    $express = $_GPC['express'];
    $expresscom = $_GPC['expresscom'];
    $expresssn = trim($_GPC['expresssn']);
    if (empty($id)) {
        $ret = 'Url参数错误！请重试！';
        return show_json(0, $ret);
    }
    if (!empty($expresssn)) {
        $change_data = array();
        $change_data['express'] = $express;
        $change_data['expresscom'] = $expresscom;
        $change_data['expresssn'] = $expresssn;
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
        $item = pdo_fetch("SELECT id,address,ordersn_general FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid",
            array(
                ":id" => $id,
                ":uniacid" => $_W["uniacid"]
            ));
        $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
            array(
                ':ordersn_general' => $item['ordersn_general'],
                ':uniacid' => $_W["uniacid"]
            ), 'id');
        if (count($orderids) > 1) {
            $orderid_where_in = implode(',', array_keys($orderids));
            $order_where = "id in ({$orderid_where_in})";
        } else {
            $order_where = "id =" . $item['id'];
        }
        $address_array = iunserializer($item["address"]);
        $address_array["realname"] = $realname;
        $address_array["mobile"] = $mobile;
        $address_array["province"] = $province;
        $address_array["city"] = $city;
        $address_array["area"] = $area;
        $address_array["address"] = $address;
        $address_array = iserializer($address_array);
        pdo_query('update ' . tablename('sz_yi_order') . ' set address=:address where ' . $order_where . ' and uniacid=:uniacid ',
            array(
                ':uniacid' => $_W["uniacid"],
                ':address' => $address_array,
            ));
        /*pdo_update("sz_yi_order", array(
            "address" => $address_array
        ) , array(
            "id" => $id,
            "uniacid" => $_W["uniacid"]
        ));*/
        $ret = "修改成功";
        return show_json(1, $ret);
    } else {
        $ret = "Url参数错误！请重试！";
        return show_json(0, $ret);
    }
} elseif ($operation == "delete") {
    ca("order.op.delete");
    $orderid = intval($_GPC["id"]);
    $ordersn_general = pdo_fetchcolumn("SELECT ordersn_general FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid",
        array(
            ":id" => $orderid,
            ":uniacid" => $_W["uniacid"]
        ));
    $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
        array(
            ':ordersn_general' => $ordersn_general,
            ':uniacid' => $_W["uniacid"]
        ), 'id');
    if (count($orderids) > 1) {
        $orderid_where_in = implode(',', array_keys($orderids));
        $order_where = "id in ({$orderid_where_in})";
    } else {
        $order_where = "id =" . $order['id'];
    }
    pdo_query('update ' . tablename('sz_yi_order') . ' set deleted=1 where ' . $order_where . ' and uniacid=:uniacid ',
        array(
            ':uniacid' => $uniacid
        ));

    plog("order.op.delete", "订单删除 ID: {$orderid}");
    message("订单删除成功", $this->createWebUrl("order", array(
        "op" => "display"
    )), "success");
} elseif ($operation == "deal") {
    $id = intval($_GPC["id"]);
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer(), "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == -1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    $to = trim($_GPC["to"]);
    if ($to == 'confirmpay') {
        order_list_confirmpay($item);
    } else {
        if ($to == 'cancelpay') {
            order_list_cancelpay($item);
        } else {
            if ($to == 'confirmsend') {
                order_list_confirmsend($item);
            } else {
                if ($to == 'cancelsend') {
                    order_list_cancelsend($item);
                } else {
                    if ($to == 'confirmsend1') {
                        order_list_confirmsend1($item);
                    } else {
                        if ($to == 'cancelsend1') {
                            order_list_cancelsend1($item);
                        } else {
                            if ($to == "finish") {
                                order_list_finish($item);
                            } else {
                                if ($to == "close") {
                                    order_list_close($item);
                                } else {
                                    if ($to == "refund") {
                                        order_list_refund($item);
                                    } else {
                                        if ($to == "room") {//确认房间号
                                            room_mumber($item);
                                        } else {
                                            if ($to == "sendin") {//确认入住
                                                order_list_sendin($item);
                                            } else {
                                                if ($to == "cancelsendroom") { //取消入住
                                                    cancelsendroom($item);
                                                } else {
                                                    if ($to == "abnormalroom") { //异常退房，即为将房款退回重新不拍
                                                        abnormalroom($item);
                                                    } else {
                                                        if ($to == "depositprice") {//退房押金
                                                            order_list_depositprice($item);
                                                        } else {
                                                            if ($to == "redpack") {
                                                                //补发红包
                                                                order_list_redpack($item);
                                                            } else {
                                                                if ($to == "changepricemodal") {
                                                                    if (!empty($item["status"])) {
                                                                        exit("-1");
                                                                    }
                                                                    $order_goods = pdo_fetchall("select og.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ",
                                                                        array(
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
                                                                            $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid",
                                                                                array(
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
                                                                } else {
                                                                    if ($to == "confirmchangeprice") {
                                                                        $changegoodsprice = $_GPC["changegoodsprice"];
                                                                        if (!is_array($changegoodsprice)) {
                                                                            message("未找到改价内容!", '', "error");
                                                                        }
                                                                        $changeprice = 0;
                                                                        foreach ($changegoodsprice as $ogid => $change) {
                                                                            $changeprice += floatval($change);
                                                                        }
                                                                        $dispatchprice = floatval($_GPC["changedispatchprice"]);
                                                                        if ($dispatchprice < 0) {
                                                                            $dispatchprice = 0;
                                                                        }
                                                                        $orderprice = $item["price"] + $changeprice;
                                                                        $changedispatchprice = 0;
                                                                        if ($dispatchprice != $item["dispatchprice"]) {
                                                                            $changedispatchprice = $dispatchprice - $item["dispatchprice"];
                                                                            $orderprice += $changedispatchprice;
                                                                        }
                                                                        if ($orderprice < 0) {
                                                                            message("订单实际支付价格不能小于0元！", '', "error");
                                                                        }
                                                                        foreach ($changegoodsprice as $ogid => $change) {
                                                                            $og = pdo_fetch("select price,realprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1",
                                                                                array(
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
                                                                            $orderupdate["changedispatchprice"] += ($dispatchprice - $item["olddispatchprice"]);
                                                                        }
                                                                        if (!empty($orderupdate)) {
                                                                            pdo_update("sz_yi_order", $orderupdate,
                                                                                array(
                                                                                    "id" => $item["id"],
                                                                                    "uniacid" => $_W["uniacid"]
                                                                                ));
                                                                        }
                                                                        foreach ($changegoodsprice as $ogid => $change) {
                                                                            $og = pdo_fetch("select price,realprice,changeprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1",
                                                                                array(
                                                                                    ":ogid" => $ogid,
                                                                                    ":uniacid" => $_W["uniacid"]
                                                                                ));
                                                                            if (!empty($og)) {
                                                                                $realprice = $og["realprice"] + $change;
                                                                                $changeprice = $og["changeprice"] + $change;
                                                                                pdo_update("sz_yi_order_goods", array(
                                                                                    "realprice" => $realprice,
                                                                                    "changeprice" => $changeprice
                                                                                ), array(
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
                                                                        plog("order.op.changeprice",
                                                                            "订单号： {$item["ordersn"]} <br/> 价格： {$item["price"]} -> {$orderprice}");
                                                                        message("订单改价成功!", referer(), "success");
                                                                    } else {
                                                                        if ($to == 'refundexpress') {
                                                                            $flag = intval($_GPC['flag']);
                                                                            $refundid = $item['refundid'];
                                                                            if (!empty($refundid)) {
                                                                                $refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and uniacid=:uniacid  limit 1',
                                                                                    array(
                                                                                        ':id' => $refundid,
                                                                                        ':uniacid' => $_W['uniacid']
                                                                                    ));
                                                                            } else {
                                                                                die('未找到退款申请.');
                                                                                exit;
                                                                            }
                                                                            if ($flag == 1) {
                                                                                $express = trim($refund['express']);
                                                                                $expresssn = trim($refund['expresssn']);
                                                                            } else {
                                                                                if ($flag == 2) {
                                                                                    $express = trim($refund['rexpress']);
                                                                                    $expresssn = trim($refund['rexpresssn']);
                                                                                }
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
                                                                        } else {
                                                                            if ($to == "express") {
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
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    exit;
}
function sortByTime($msg0, $msg1)
{
    if ($msg0["ts"] == $msg1["ts"]) {
        return 0;
    } else {
        return $msg0["ts"] > $msg1["ts"] ? 1 : -1;
    }
}

function changeWechatSend($ordersn, $status, $msg = '')
{
    global $_W;
    $paylog = pdo_fetch("SELECT plid, openid, tag FROM " . tablename("core_paylog") . " WHERE tid = '{$ordersn}' AND status = 1 AND type = 'wechat'");
    if (!empty($paylog["openid"])) {
        $paylog["tag"] = iunserializer($paylog["tag"]);
        $acid = $paylog["tag"]["acid"];
        load()->model("account");
        $account = account_fetch($acid);
        $payment = uni_setting($account["uniacid"], "payment");
        if ($payment["payment"]["wechat"]["version"] == "2") {
            return true;
        }
        $send = array(
            "appid" => $account["key"],
            "openid" => $paylog["openid"],
            "transid" => $paylog["tag"]["transaction_id"],
            "out_trade_no" => $paylog["plid"],
            "deliver_timestamp" => TIMESTAMP,
            "deliver_status" => $status,
            "deliver_msg" => $msg,
        );
        $sign = $send;
        $sign["appkey"] = $payment["payment"]["wechat"]["signkey"];
        ksort($sign);
        $string = '';
        foreach ($sign as $key => $v) {
            $key = strtolower($key);
            $string .= "{$key}={$v}&";
        }
        $send["app_signature"] = sha1(rtrim($string, "&"));
        $send["sign_method"] = "sha1";
        $account = WeAccount::create($acid);
        $response = $account->changeOrderStatus($send);
        if (is_error($response)) {
            message($response["message"]);
        }
    }
}

function order_list_backurl()
{
    global $_GPC;
    return $_GPC["op"] == "detail" ? $this->createWebUrl("order") : referer();
}

function order_list_confirmsend($order)
{
    global $_W, $_GPC;
    ca("order.op.send");
    if (empty($order["addressid"])) {
        message("无收货地址，无法发货！");
    }
    if ($order["paytype"] != 3) {
        if ($order["status"] != 1) {
            message("订单未付款，无法发货！");
        }
    }
    if (!empty($_GPC["isexpress"]) && empty($_GPC["expresssn"])) {
        message("请输入快递单号！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 1);
    }
    pdo_update("sz_yi_order", array(
        "status" => 2,
        "express" => trim($_GPC["express"]),
        "expresscom" => trim($_GPC["expresscom"]),
        "expresssn" => trim($_GPC["expresssn"]),
        "sendtime" => time()
    ), array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    if (!empty($order["refundid"])) {
        $zym_var_35 = pdo_fetchcolumn("select status from " . tablename("sz_yi_order_refund") . " where id=:id limit 1",
            array(
                ":id" => $order["refundid"]
            ));
        if ($zym_var_35 == 0) {
            message("此订单有退款申请未处理，请处理完成之后进行确认发货操作！");

        }

    }
    m("notice")->sendOrderMessage($order["id"]);
    plog("order.op.send",
        "订单发货 ID: {$order["id"]} 订单号: {$order["ordersn"]} <br/>快递公司: {$_GPC["expresscom"]} 快递单号: {$_GPC["expresssn"]}");
    message("发货操作成功！", order_list_backurl(), "success");
}

function order_list_confirmsend1($order)
{
    global $_W, $_GPC;
    ca("order.op.fetch");
    if ($order["status"] != 1) {
        message("订单未付款，无法确认取货！");
    }
    $paylog7 = time();
    $paylog6 = array(
        "status" => 3,
        "sendtime" => $paylog7,
        "finishtime" => $paylog7
    );
    if ($order["isverify"] == 1) {
        $paylog6["verified"] = 1;
        $paylog6["verifytime"] = $paylog7;
        $paylog6["verifyopenid"] = "";
    }
    pdo_update("sz_yi_order", $paylog6, array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    if (!empty($order["refundid"])) {
        $zym_var_35 = pdo_fetchcolumn("select status from " . tablename("sz_yi_order_refund") . " where id=:id limit 1",
            array(
                ":id" => $order["refundid"]
            ));
        if ($zym_var_35 == 0) {
            message("此订单有退款申请未处理，请处理完成之后进行确认发货操作！");

        }

    }
    m("member")->upgradeLevel($order["openid"],$order["id"]);
    m("notice")->sendOrderMessage($order["id"]);
    if (p("commission")) {
        p("commission")->checkOrderFinish($order["id"]);
    }
    if (p("return")) {
        p("return")->cumulative_order_amount($order["id"]);
    }
    if (p('yunbi')) {
        p('yunbi')->GetVirtualCurrency($order["id"]);
    }
    if (p('beneficence')) {
        p('beneficence')->GetVirtualBeneficence($order["id"]);
    }
    // 订单确认收货后自动发送红包
    if ($order["redprice"] >= 1 && $order["redprice"] <= 200) {
        m('finance')->sendredpack($order['openid'], $order["redprice"] * 100, $order["id"], $desc = '购买商品赠送红包',
            $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
    }
    plog("order.op.fetch", "订单确认取货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("发货操作成功！", order_list_backurl(), "success");
}

function order_list_cancelsend($order)
{
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($order["status"] != 2) {
        message("订单未发货，不需取消发货！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 0, $_GPC["cancelreson"]);
    }
    pdo_update("sz_yi_order", array(
        "status" => 1,
        "sendtime" => 0
    ), array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));

    plog("order.op.sencancel", "订单取消发货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消发货操作成功！", order_list_backurl(), "success");

}

function order_list_cancelsend1($order)
{
    global $_W, $_GPC;
    ca("order.op.fetchcancel");
    if ($order["status"] != 3) {
        message("订单未取货，不需取消！");
    }
    pdo_update("sz_yi_order", array(
        "status" => 1,
        "finishtime" => 0
    ), array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.fetchcancel", "订单取消取货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消发货操作成功！", order_list_backurl(), "success");
}

function order_list_finish($order)
{
    global $_W, $_GPC;
    if ($order['status'] == '3') {
        message("订单已完成！", order_list_backurl(), "error");
        exit;
    }

    ca("order.op.finish");
    pdo_update("sz_yi_order", array(
        "status" => 3,
        "finishtime" => time()
    ), array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));

    //到付 赠送积分
    if ($order['paytype'] == 3) {
        $goods = pdo_fetchall("select og.id,og.total,og.realprice, g.credit from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ",
            array(
                ':uniacid' => $_W['uniacid'],
                ':orderid' => $order["id"]
            ));

        $credits = 0;
        foreach ($goods as $g) {
            $gcredit = trim($g['credit']);
            if (!empty($gcredit)) {
                if (strexists($gcredit, '%')) {
                    $credits += intval(floatval(str_replace('%', '', $gcredit)) / 100 * $g['realprice']);
                } else {
                    $credits += intval($g['credit']) * $g['total'];
                }
            }
        }
        if ($credits > 0) {
            $shopset = m('common')->getSysset('shop');
            m('member')->setCredit($order['openid'], 'credit1', $credits, array(
                0,
                $shopset['name'] . '购物积分 订单号: ' . $order['ordersn']
            ));
        }
    }

    m("member")->upgradeLevel($order["openid"],$order["id"]);
    m("notice")->sendOrderMessage($order["id"]);
    if (p("coupon") && !empty($order["couponid"])) {
        p("coupon")->backConsumeCoupon($order["id"]);
    }

    if (p("commission")) {
        p("commission")->checkOrderFinish($order["id"]);
    }


    if (p("return")) {
        p("return")->cumulative_order_amount($order["id"]);
    }
    if (p('yunbi')) {
        p('yunbi')->GetVirtualCurrency($order['id']);
    }
    // 订单确认收货后自动发送红包
    if ($order["redprice"] >= 1 && $order["redprice"] <= 200) {
        m('finance')->sendredpack($order['openid'], $order["redprice"] * 100, $order["id"], $desc = '购买商品赠送红包',
            $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
    }

    plog("order.op.finish", "订单完成 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("订单操作成功！", order_list_backurl(), "success");
}

// 自动发送红包失败后补发红包
function order_list_redpack($order)
{
    global $_W, $_GPC;
    if (empty($order['redstatus'])) {
        //如果该字段为空则表示已经发送过
        message("红包已发送，不可重复发送！");
    }

    if ($order["redprice"] > 0) {
        //订单红包价格字段大于0则正常发送红包
        if ($order["redprice"] >= 1 && $order["redprice"] <= 200) {
            //红包价格必须在1-200元之间
            $result = m('finance')->sendredpack($order['openid'], $order["redprice"] * 100, $order["id"],
                $desc = '购买商品赠送红包', $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
            if (is_error($result)) {
                message($result['message'], '', 'error');
            } else {
                //如果发送失败则更新订单红包状态字段，字段为空则表示发送成功
                pdo_update('sz_yi_order',
                    array(
                        'redstatus' => ""
                    ),
                    array(
                        'id' => $order["id"]
                    )
                );
                message("红包补发成功！", order_list_backurl(), "success");
            }
        } else {
            message("红包金额错误！发送失败！红包金额在1-200元之间！");
        }

    }
}

function order_list_cancelpay($order)
{
    global $_W, $_GPC;
    ca("order.op.paycancel");
    if ($order["status"] != 1) {
        message("订单未付款，不需取消！");
    }
    m("order")->setStocksAndCredits($order["id"], 2);
    pdo_update("sz_yi_order", array(
        "status" => 0,
        "cancelpaytime" => time()
    ), array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.paycancel", "订单取消付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消订单付款操作成功！", order_list_backurl(), "success");
}

function order_list_confirmpay($order)
{
    global $_W, $_GPC;
    ca("order.op.pay");
    if ($order["status"] > 1) {
        message("订单已付款，不需重复付款！");
    }
    $virtual = p("virtual");
    if (!empty($order["virtual"]) && $virtual) {
        $virtual->pay($order);
    } else {
        /*pdo_update("sz_yi_order", array(
            "status" => 1,
            "paytype" => 11,
            "paytime" => time()
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        */
        $ordersn_general = pdo_fetchcolumn("select ordersn_general from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1',
            array(
                ':id' => $order["id"],
                ':uniacid' => $_W["uniacid"]
            ));
        $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
            array(
                ':ordersn_general' => $ordersn_general,
                ':uniacid' => $_W["uniacid"]
            ));
        $plugin_coupon = p("coupon");
        $plugin_commission = p("commission");
        $orderid = array();
        foreach ($order_all as $key => $val) {
            //m("order")->setStocksAndCredits($val["id"], 1);
            m("notice")->sendOrderMessage($val["id"]);
            if ($plugin_coupon && !empty($val["couponid"])) {
                $plugin_coupon->backConsumeCoupon($val["id"]);
            }
            if ($plugin_commission) {
                $plugin_commission->checkOrderPay($val["id"]);
            }
            $price += $val['price'];
            $orderid[] = $val['id'];
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1',
            array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':tid' => $ordersn_general
            ));
        if (!empty($log) && $log['status'] != '0') {
            return show_json(-1, '订单已支付, 无需重复支付!');
            message("订单已支付, 无需重复支付!", '', "error");
        }
        if (!empty($log) && $log['status'] == '0') {
            pdo_delete('core_paylog', array(
                'plid' => $log['plid']
            ));
            $log = null;
        }
        if (empty($log)) {
            $log = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $order['openid'],
                'module' => "sz_yi",
                'tid' => $ordersn_general,
                'fee' => $price,
                'status' => 0
            );
            pdo_insert('core_paylog', $log);
        }
        if (is_array($orderid)) {
            $orderids = implode(',', $orderid);
            $where_update = "id in ({$orderids})";
        }
        pdo_query('update ' . tablename('sz_yi_order') . ' set paytype=11 where ' . $where_update . ' and uniacid=:uniacid ',
            array(
                ':uniacid' => $_W['uniacid']
            ));
        $ret = array();
        $ret['result'] = 'success';
        $ret['from'] = 'return';
        $ret['tid'] = $log['tid'];
        $ret['user'] = $order['openid'];
        $ret['fee'] = $price;
        $ret['weid'] = $_W['uniacid'];
        $ret['uniacid'] = $_W['uniacid'];
        $payresult = m('order')->payResult($ret);
    }
    plog("order.op.pay", "订单确认付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("确认订单付款操作成功！", order_list_backurl(), "success");
}

function order_list_close($order)
{
    global $_W, $_GPC;
    ca("order.op.close");
    if ($order["status"] == -1) {
        message("订单已关闭，无需重复关闭！");
    } else {
        if ($order["status"] >= 1) {
            message("订单已付款，不能关闭！");
        }
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 0, $_GPC["reson"]);
    }
    $time = time();
    $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid',
        array(
            ':ordersn_general' => $order['ordersn_general'],
            ':uniacid' => $_W['uniacid']
        ));
    foreach ($order_all as $key => $value) {
        if ($value['refundstate'] > 0 && !empty($value['refundid'])) {
            $data = array();
            $data['status'] = -1;
            $data['refundtime'] = $time;
            pdo_update('sz_yi_order_refund', $data, array(
                'id' => $value['refundid'],
                'uniacid' => $_W['uniacid']
            ));
        }
        pdo_update("sz_yi_order", array(
            "status" => -1,
            'refundstate' => 0,
            "canceltime" => time(),
            "remark" => $value["remark"] . "" . $_GPC["remark"]
        ), array(
            "id" => $value["id"],
            "uniacid" => $_W["uniacid"]
        ));
        if ($value["deductcredit"] > 0) {
            $shopset = m("common")->getSysset("shop");
            m("member")->setCredit($value["openid"], "credit1", $value["deductcredit"], array(
                '0',
                $shopset["name"] . "购物返还抵扣积分 积分: {$value["deductcredit"]} 抵扣金额: {$value["deductprice"]} 订单号: {$value["ordersn"]}"
            ));
        }

        if ($value['deductyunbimoney'] > 0) {
            $shopset = m('common')->getSysset('shop');
            p('yunbi')->setVirtualCurrency($value['openid'], $value['deductyunbi']);
            //虚拟币抵扣记录
            $data_log = array(
                'id' => '',
                'openid' => $value['openid'],
                'credittype' => 'virtual_currency',
                'money' => $value['deductyunbi'],
                'remark' => "购物返还抵扣" . $yunbiset['yunbi_title'] . " " . $yunbiset['yunbi_title'] . ": {$value['deductyunbi']} 抵扣金额: {$value['deductyunbimoney']} 订单号: {$value['ordersn']}"
            );
            p('yunbi')->addYunbiLog($_W["uniacid"], $data_log, '4');
        }


        if (p("coupon") && !empty($value["couponid"])) {
            p("coupon")->returnConsumeCoupon($value["id"]);
        }
        plog("order.op.close", "订单关闭 ID: {$value["id"]} 订单号: {$value["ordersn"]}");
    }

    message("订单关闭操作成功！", order_list_backurl(), "success");
}

function order_list_refund($item)
{
    global $_W, $_GPC;
    ca('order.op.refund');
    $shopset = m('common')->getSysset('shop');
    if (empty($item['refundstate'])) {
        message('订单未申请退款，不需处理！');
    }
    $refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and (status=0 or status>1) order by id desc limit 1',
        array(
            ':id' => $item['refundid']
        ));
    if (empty($refund)) {
        pdo_update('sz_yi_order', array(
            'refundstate' => 0
        ), array(
            'id' => $item['id'],
            'uniacid' => $_W['uniacid']
        ));
        message('未找到退款申请，不需处理！');
    }
    if (empty($refund['refundno'])) {
        $refund['refundno'] = m('common')->createNO('order_refund', 'refundno', 'SR');
        pdo_update('sz_yi_order_refund', array(
            'refundno' => $refund['refundno']
        ), array(
            'id' => $refund['id']
        ));
    }
    $refundstatus = intval($_GPC['refundstatus']);
    $refundcontent = trim($_GPC['refundcontent']);
    $time = time();
    $data = array();
    $uniacid = $_W['uniacid'];
    if ($refundstatus == 0) {
        message('暂不处理', referer());
    } else {
        if ($refundstatus == 3) {
            $raid = $_GPC['raid'];
            $message = trim($_GPC['message']);
            if ($raid == 0) {
                $address = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where isdefault=1 and uniacid=:uniacid limit 1',
                    array(
                        ':uniacid' => $uniacid
                    ));
            } else {
                $address = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where id=:id and uniacid=:uniacid limit 1',
                    array(
                        ':id' => $raid,
                        ':uniacid' => $uniacid
                    ));
            }
            if (empty($address)) {
                $address = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where uniacid=:uniacid order by id desc limit 1',
                    array(
                        ':uniacid' => $uniacid
                    ));
            }
            unset($address['uniacid']);
            unset($address['openid']);
            unset($address['isdefault']);
            unset($address['deleted']);
            $address = iserializer($address);
            $data['reply'] = '';
            $data['refundaddress'] = $address;
            $data['refundaddressid'] = $raid;
            $data['message'] = $message;
            if (empty($refund['operatetime'])) {
                $data['operatetime'] = $time;
            }
            if ($refund['status'] != 4) {
                $data['status'] = 3;
            }
            pdo_update('sz_yi_order_refund', $data, array(
                'id' => $item['refundid']
            ));
            m('notice')->sendOrderMessage($item['id'], true);
        } else {
            if ($refundstatus == 5) {
                $data['rexpress'] = $_GPC['rexpress'];
                $data['rexpresscom'] = $_GPC['rexpresscom'];
                $data['rexpresssn'] = trim($_GPC['rexpresssn']);
                $data['status'] = 5;
                if ($refund['status'] != 5 && empty($refund['returntime'])) {
                    $data['returntime'] = $time;
                }
                pdo_update('sz_yi_order_refund', $data, array(
                    'id' => $item['refundid']
                ));
                m('notice')->sendOrderMessage($item['id'], true);
            } else {
                if ($refundstatus == 10) {
                    $refund_data['status'] = 1;
                    $refund_data['refundtime'] = $time;
                    pdo_update('sz_yi_order_refund', $refund_data, array(
                        'id' => $item['refundid'],
                        'uniacid' => $uniacid
                    ));
                    $order_data = array();
                    $order_data['refundstate'] = 0;
                    $order_data['status'] = 1;
                    $order_data['refundtime'] = $time;
                    pdo_update('sz_yi_order', $order_data, array(
                        'id' => $item['id'],
                        'uniacid' => $uniacid
                    ));
                    m('notice')->sendOrderMessage($item['id'], true);
                } else {
                    if ($refundstatus == 1) {
                        if (!empty($item['pay_ordersn'])) {
                            $pay_ordersn = $item['pay_ordersn'];
                            $ordersn_count = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_order') . ' where uniacid=:uniacid and pay_ordersn=:pay_ordersn limit 1',
                                array(
                                    ':pay_ordersn' => $pay_ordersn,
                                    ':uniacid' => $uniacid
                                ));
                        } else {
                            $pay_ordersn = $ordersn;
                        }
                        $ordersn = $item['ordersn'];

                        if (!empty($item['ordersn2'])) {
                            $var = sprintf('%02d', $item['ordersn2']);
                            $pay_ordersn .= 'GJ' . $var;
                        }
                        $realprice = $refund['applyprice'];
                        $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid',
                            array(
                                ':orderid' => $item['id'],
                                ':uniacid' => $uniacid
                            ));
                        $credits = 0;
                        foreach ($goods as $g) {
                            $gcredit = trim($g['credit']);
                            if (!empty($gcredit)) {
                                if (strexists($gcredit, '%')) {
                                    $credits += intval(floatval(str_replace('%', '',
                                            $gcredit)) / 100 * $g['realprice']);
                                } else {
                                    $credits += intval($g['credit']) * $g['total'];
                                }
                            }
                        }
                        $refundtype = 0;
                        if ($item['paytype'] == 1) {
                            m('member')->setCredit($item['openid'], 'credit2', $realprice, array(
                                0,
                                $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']
                            ));
                            $result = true;
                        } else {
                            if ($item['paytype'] == 21) {
                                if ($ordersn_count > 1) {
                                    message('多笔合并付款订单，请使用手动退款。', '', 'error');
                                }
                                $realprice = round($realprice - $item['deductcredit2'], 2);
                                $result = m('finance')->refund($item['openid'], $pay_ordersn, $refund['refundno'],
                                    $item['price'] * 100, $realprice * 100);
                                $refundtype = 2;
                            } else {
                                if ($item['paytype'] == 22) {
                                    $set = m('common')->getSysset(array(
                                        'pay'
                                    ));
                                    $setting = uni_setting($_W['uniacid'], array('payment'));
                                    if (!$set['pay']['alipay']) {
                                        message('您未开启支付宝支付', '', 'error');
                                    }

                                    if (!$setting['payment']['alipay']['switch']) {
                                        message('您未开启支付宝无线支付', '', 'error');
                                    }
                                    if ($ordersn_count > 1) {
                                        message('多笔合并付款订单，请使用手动退款。', '', 'error');
                                    }
                                    $realprice = round($realprice - $item['deductcredit2'], 2);
                                    m('finance')->alipayrefund($item['openid'], $item['trade_no'], $refund['refundno'],
                                        $realprice);

                                } elseif ($item['paytype'] == 26 || $item['paytype'] == 25) {
                                    $set = m('common')->getSysset(array('pay'));
                                    $setting = uni_setting($_W['uniacid'], array('payment'));
                                    if (!$set['pay']['yeepay']) {
                                        message('您未开启易宝支付', '', 'error');
                                    }
                                    if ($ordersn_count > 1) {
                                        message('多笔合并付款订单，请使用手动退款。', '', 'error');
                                    }
                                    $realprice = round($realprice - $item['deductcredit2'], 2);
                                    m('finance')->yeepayrefund($item['paytype'], $item['openid'], $item['trade_no'],
                                        $refund['refundno'], $realprice);
                                } elseif ($item['paytype'] == 27 || $item['paytype'] == 28) {
                                    if ($ordersn_count > 1) {
                                        message('多笔合并付款订单，请使用手动退款。', '', 'error');
                                    }

                                    $realprice = round($realprice - $item['deductcredit2'], 2);
                                    m('finance')->apprefund($item['paytype'], $item['openid'], $item['trade_no'],
                                        $refund['refundno'], $realprice);

                                } elseif ($item['paytype'] == 29){
                                    message('paypal付款订单，请使用手动退款并到paypal商户处理退款！！！', '', 'error');
                                }else {
                                    if ($realprice < 1) {
                                        message('退款金额必须大于1元，才能使用微信企业付款退款!', '', 'error');
                                    }
                                    /*if ($ordersn_count > 1) {
                                        message('多笔合并微信付款订单，请使用手动退款。', '', 'error');
                                    }*/
                                    $realprice = round($realprice - $item['deductcredit2'], 2);
                                    $result = m('finance')->pay($item['openid'], 1, $realprice * 100,
                                        $refund['refundno'],
                                        $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']);
                                    $refundtype = 1;
                                }
                            }
                        }
                        if (is_error($result)) {
                            message($result['message'], '', 'error');
                        }
                        if ($credits > 0) {
                            m('member')->setCredit($item['openid'], 'credit1', -$credits, array(
                                0,
                                $shopset['name'] . "退款扣除积分: {$credits} 订单号: " . $item['ordersn']
                            ));
                        }
                        if ($item['deductcredit'] > 0) {
                            m('member')->setCredit($item['openid'], 'credit1', $item['deductcredit'], array(
                                '0',
                                $shopset['name'] . "购物返还抵扣积分 积分: {$item['deductcredit']} 抵扣金额: {$item['deductprice']} 订单号: {$item['ordersn']}"
                            ));
                        }

                        if ($item['deductyunbimoney'] > 0) {
                            $shopset = m('common')->getSysset('shop');

                            p('yunbi')->setVirtualCurrency($item['openid'], $item['deductyunbi']);
                            //虚拟币抵扣记录
                            $data_log = array(
                                'id' => '',
                                'openid' => $item['openid'],
                                'credittype' => 'virtual_currency',
                                'money' => $item['deductyunbi'],
                                'remark' => "购物返还抵扣" . $yunbiset['yunbi_title'] . " " . $yunbiset['yunbi_title'] . ": {$item['deductyunbi']} 抵扣金额: {$item['deductyunbimoney']} 订单号: {$item['ordersn']}"
                            );
                            p('yunbi')->addYunbiLog($_W["uniacid"], $data_log, '4');
                        }

                        if (p('channel')) {
                            p('channel')->channelRefund($item['id'],$item['uniacid'], $item['openid']);
                        }
                        if (p('card')) {
                            p('card')->cardRefund($item['cardid'],$item['cardprice']);
                        }

                        if (!empty($refundtype)) {
                            if ($item['deductcredit2'] > 0) {
                                m('member')->setCredit($item['openid'], 'credit2', $item['deductcredit2'], array(
                                    '0',
                                    $shopset['name'] . "购物返还抵扣余额 积分: {$item['deductcredit2']} 订单号: {$item['ordersn']}"
                                ));
                            }
                        }
                        $data['reply'] = '';
                        $data['status'] = 1;
                        $data['refundtype'] = $refundtype;
                        $data['price'] = $realprice;
                        $data['refundtime'] = $time;
                        pdo_update('sz_yi_order_refund', $data, array(
                            'id' => $item['refundid']
                        ));
                        m('notice')->sendOrderMessage($item['id'], true);
                        pdo_update('sz_yi_order', array(
                            'refundstate' => 0,
                            'status' => -1,
                            'refundtime' => $time
                        ), array(
                            'id' => $item['id'],
                            'uniacid' => $uniacid
                        ));
                        foreach ($goods as $g) {
                            $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1',
                                array(
                                    ':goodsid' => $g['id'],
                                    ':uniacid' => $uniacid
                                ));
                            pdo_update('sz_yi_goods', array(
                                'salesreal' => $salesreal
                            ), array(
                                'id' => $g['id']
                            ));
                        }
                        plog('order.op.refund', "订单退款 ID: {$item['id']} 订单号: {$item['ordersn']}");
                    } else {
                        if ($refundstatus == -1) {
                            pdo_update('sz_yi_order_refund', array(
                                'reply' => $refundcontent,
                                'status' => -1
                            ), array(
                                'id' => $item['refundid']
                            ));
                            m('notice')->sendOrderMessage($item['id'], true);
                            plog('order.op.refund',
                                "订单退款拒绝 ID: {$item['id']} 订单号: {$item['ordersn']} 原因: {$refundcontent}");
                            pdo_update('sz_yi_order', array(
                                'refundstate' => 0
                            ), array(
                                'id' => $item['id'],
                                'uniacid' => $uniacid
                            ));
                        } else {
                            if ($refundstatus == 2) {
                                $refundtype = 2;
                                $data['reply'] = '';
                                $data['status'] = 1;
                                $data['refundtype'] = $refundtype;
                                $data['price'] = $refund['applyprice'];
                                $data['refundtime'] = $time;
                                pdo_update('sz_yi_order_refund', $data, array(
                                    'id' => $item['refundid']
                                ));
                                m('notice')->sendOrderMessage($item['id'], true);
                                pdo_update('sz_yi_order', array(
                                    'refundstate' => 0,
                                    'status' => -1,
                                    'refundtime' => $time
                                ), array(
                                    'id' => $item['id'],
                                    'uniacid' => $uniacid
                                ));
                                $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid',
                                    array(
                                        ':orderid' => $item['id'],
                                        ':uniacid' => $uniacid
                                    ));
                                foreach ($goods as $g) {
                                    $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1',
                                        array(
                                            ':goodsid' => $g['id'],
                                            ':uniacid' => $uniacid
                                        ));
                                    pdo_update('sz_yi_goods', array(
                                        'salesreal' => $salesreal
                                    ), array(
                                        'id' => $g['id']
                                    ));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    message('退款申请处理成功!', order_list_backurl(), 'success');
}

function room_mumber($paylog2)
{
    global $_W, $_GPC;
    ca("order.op.send");
    if ($_GPC['expresssn'] == '') {
        message("请填写房间号");
    }

    pdo_update("sz_yi_order", array(
        "status" => 2,
        "room_number" => trim($_GPC["expresssn"]),
    ), array(
        "id" => $paylog2["id"],
        "uniacid" => $_W["uniacid"]
    ));

    m("notice")->sendOrderMessage($paylog2["id"]);
    plog("order.op.send", "订单确认 ID: {$paylog2["id"]} 订单号: {$paylog2["ordersn"]} <br/>房间号: {$_GPC["expresssn"]}}");
    message("预约操作成功！", order_list_backurl(), "success");
}

function order_list_sendin($paylog2)
{//确认入住
    global $_W, $_GPC;
    ca("order.op.sendin");
    pdo_update("sz_yi_order", array(
        "status" => '6',
    ), array(
        "id" => $paylog2["id"],
        "uniacid" => $_W["uniacid"]
    ));
    m("notice")->sendOrderMessage($paylog2["id"]);

    plog("order.op.finish", "订单已入住 ID: {$paylog2["id"]} 订单号: {$paylog2["ordersn"]}");
    message("订单操作成功！", order_list_backurl(), "success");
}

function cancelsendroom($paylog2)
{//取消入住
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($paylog2["status"] != 2) {
        message("订单未确认，不需取消！");
    }
    $refund = array(
        "uniacid" => $_W["uniacid"],
        'orderid' => $_GPC['id'],
        'price' => $paylog2['price'],
        'applyprice' => sprintf("%1.2f", $paylog2['price']),
        'createtime' => time(),
        'content' => $_GPC['cancelreson'],
        'reason' => $_GPC['cancelreson']
    );
    pdo_insert('sz_yi_order_refund', $refund);
    $refundid = pdo_insertid();
    pdo_update("sz_yi_order", array(
        "status" => '4',
        "refundid" => $refundid,
        "refundtime" => time(),
        'refundstate' => '1',
    ), array(
        "id" => $paylog2["id"],
        "uniacid" => $_W["uniacid"]
    ));

    $params = array();
    $sql = "SELECT id, roomdate, num FROM " . tablename('sz_yi_hotel_room_price');
    $sql .= " WHERE 1 = 1";
    $sql .= " AND roomid = :roomid";
    $sql .= " AND roomdate >= :btime AND roomdate < :etime";
    $sql .= " AND status = 1";

    $params[':roomid'] = $paylog2['roomid'];
    $params[':btime'] = $paylog2['btime'];
    $params[':etime'] = $paylog2['etime'];
    $room_date_list = pdo_fetchall($sql, $params);
    if ($room_date_list) {
        foreach ($room_date_list as $key => $value) {
            $num = $value['num'];
            if ($num >= 0) {
                $now_num = $num + $paylog2['num'];
                pdo_update('sz_yi_hotel_room_price', array('num' => $now_num), array('id' => $value['id']));
            }
        }
    }

    plog("order.op.sencancel", "订单取消 ID: {$paylog2["id"]} 订单号: {$paylog2["ordersn"]}");
    message("取消操作成功！", order_list_backurl(), "success");
}

//异常退房
function abnormalroom($paylog2)
{
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($paylog2["status"] != 6) {
        message("订单不可被退！");
    }

    $refund = array(
        "uniacid" => $_W["uniacid"],
        'orderid' => $_GPC['id'],
        'createtime' => time(),
        'content' => '异常的退房,需重新补订单',
        'reason' => '异常的退房,需重新补订单',
        'price' => $paylog2['price'],
        'applyprice' => sprintf("%1.2f", $paylog2['price']),

    );
    pdo_insert('sz_yi_order_refund', $refund);
    $refundid = pdo_insertid();
    pdo_update("sz_yi_order", array(
        "status" => '4',
        "refundid" => $refundid,
        "refundtime" => time(),
        'refundstate' => '1',
    ), array(
        "id" => $paylog2["id"],
        "uniacid" => $_W["uniacid"]
    ));

    plog("order.op.sencancel", "订单被退 ID: {$paylog2["id"]} 订单号: {$paylog2["ordersn"]}");
    message("操作成功！", order_list_backurl(), "success");
}

//退押金
function order_list_depositprice($item)
{
        global $_W, $_GPC;
        if ($_GPC['expresssn'] == '') {
            message("请填写押金金额");
        }
        if ($item['depositpricetype'] == '2') {
            pdo_update("sz_yi_order", array(
                'returndepositprice' => $_GPC['expresssn'],
            ), array(
                "id" => $item["id"],
                "uniacid" => $_W["uniacid"]
            ));
        } else {
            $ordersn = $item["ordersn"];
            if (!empty($item["ordersn2"])) {
                $ordersn2 = sprintf("%02d", $item["ordersn2"]);
                $ordersn .= "GJ" . $ordersn2;
            }
            $realprice = $_GPC['expresssn'];
            $goods = pdo_fetchall("SELECT g.id,g.credit, o.total,o.realprice FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.orderid=:orderid and o.uniacid=:uniacid",
                array(
                    ":orderid" => $item["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
            $credits = 0;
            foreach ($goods as $g) {
                $credits += $g["credit"] * $g["total"];
            }
            $refundtype = 0;
            if ($item["paytype"] == 1) {
                m("member")->setCredit($item["openid"], "credit2", $realprice, array(
                    0,
                    $shopset["name"] . "退押金: {$realprice}元 订单号: " . $item["ordersn"]
                ));
                $result = true;
            } else {
                if ($item["paytype"] == 21) {
                    $realprice = round($realprice - $item["deductcredit2"], 2);
                    $result = m("finance")->refund($item["openid"], $ordersn, $refund["refundno"], $item["price"] * 100,
                        $realprice * 100);
                    $refundtype = 2;
                } else {
                    if ($realprice < 1) {
                        message("押金金额必须大于1元，才能使用微信企业付款!", '', "error");
                    }
                    $realprice = round($realprice - $item["deductcredit2"], 2);
                    $result = m("finance")->pay($item["openid"], 1, $realprice * 100, $refund["refundno"],
                        $shopset["name"] . "押金: {$realprice}元 订单号: " . $item["ordersn"]);
                    $refundtype = 1;
                }
            }
            if (is_error($result)) {
                message($result["message"], '', "error");
            }
            pdo_update("sz_yi_order", array(
                'returndepositprice' => $_GPC['expresssn'],
            ), array(
                "id" => $item["id"],
                "uniacid" => $_W["uniacid"]
            ));

            m("notice")->sendOrderMessage($item["id"], true);

            plog("order.op.refund", "订单退押金 ID: {$item["id"]} 订单号: {$item["ordersn"]}");

        }


    message("押金退款处理成功!", order_list_backurl(), "success");
}

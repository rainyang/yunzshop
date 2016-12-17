<?php
set_time_limit(0);
global $_W, $_GPC;
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$type = $_GPC['type'];
$plugin_diyform = p("diyform");
$shopset = m('common')->getSysset('pay');
$totals = array();
$r_type         = array(
    '0' => '退款',
    '1' => '退货退款',
    '2' => '换货'
);
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
    "25" => array(
        "css" => "primary",
        "name" => "易宝支付"
    ) ,
    "26" => array(
        "css" => "primary",
        "name" => "易宝网银支付"
    ) ,
    "3" => array(
        "css" => "primary",
        "name" => "货到付款"
    ) ,
    "4" => array(
        "css" => "primary",
        "name" => "到店支付"
    )
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
if(p('hotel')){
    if($type=='hotel'){
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
    }
}

$store_list = pdo_fetchall("SELECT * FROM ".tablename('sz_yi_store')." WHERE uniacid=:uniacid and status=1", array(':uniacid' => $_W['uniacid']));
if ($operation == "display") {
    ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
    //判断该帐号的权限
    if(p('supplier')){
        $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
        $suppliers = p('supplier')->AllSuppliers();
    }
    //END
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $status = $_GPC["status"] == "" ? 1 : $_GPC["status"];
    $sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
    $condition = " o.uniacid = :uniacid and o.deleted=0";
    $paras = $paras1 = array(
        ":uniacid" => $_W["uniacid"]
    );
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime("-1 month");
        $endtime = time();
    }
    if (!empty($_GPC['supplier_uid'])) {
        $condition.= " AND o.supplier_uid = :supplier_uid ";
        $paras[":supplier_uid"] = $_GPC['supplier_uid'];
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
    //门店取消订单搜索
    if(empty($_W['isagent'])){
        if($_GPC['cancel'] == 1){
            $orderids = pdo_fetchall("select orderid from " . tablename('sz_yi_cancel_goods') . " where uniacid={$_W['uniacid']} ");
            $ids = "";
            foreach ($orderids as $key => $value) {
                if($key != 0){
                    $ids .= "," . $value['orderid'];
                }else{
                    $ids .= $value['orderid'];
                }
            }
            if (!empty($orderids)) {
                $condition.= " and o.id in (" .$ids. ") ";
            }

        }
    }
    //商品名称检索订单
    if (!empty($_GPC["good_name"])) {
        $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and g.title LIKE '%{$_GPC["good_name"]}%' group by og.orderid ");
        $conditionsp_goodsid = '';
        foreach ($conditionsp_goods as $value) {
            $conditionsp_goodsid .= "'".$value['orderid']."', ";
        }
        //判断商品名称是否存在 不存在订单ID等于空
        if (!empty($conditionsp_goodsid)) {
            $condition .= " AND o.id in (".substr($conditionsp_goodsid,0,-2).") ";
        }else {
            $condition .= " AND o.id = '' ";
        }

    }
    //商品ID检索
    if (!empty($_GPC["good_id"])) {
        $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and g.id = '{$_GPC["good_id"]}' group by og.orderid ");
        $conditionsp_goodsid = '';
        foreach ($conditionsp_goods as $value) {
            $conditionsp_goodsid .= "'".$value['orderid']."', ";
        }
        //判断商品ID是否存在 不存在订单ID等于空
        if (!empty($conditionsp_goodsid)) {
            $condition .= " AND o.id in (".substr($conditionsp_goodsid,0,-2).") ";
        }else {
            $condition .= " AND o.id = '' ";
        }
    }


    if (!empty($_GPC["keyword"])) {
        $_GPC["keyword"] = trim($_GPC["keyword"]);
        $condition.= " AND (o.pay_ordersn LIKE '%{$_GPC["keyword"]}%' OR o.ordersn_general LIKE '%{$_GPC["keyword"]}%')";
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
    if (!empty($_GPC["csid"])) {
        $_GPC["csid"] = trim($_GPC["csid"]);
        $condition.= " AND o.cashierid=" . intval($_GPC["csid"]);
    }
    if (p('hotel')) {
        if($type=='hotel'){
            $condition.= " AND o.order_type=3";
        }else{
            $condition.= " AND o.order_type<>3";
        }
    }else{
        $condition.= " AND o.order_type<>3";
    }
    $statuscondition = '';
    if ($status != "all") {
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
    $bonusagentid = intval($_GPC['bonusagentid']);
    if(!empty($bonusagentid)){
        $sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=".$bonusagentid." ORDER BY id DESC";
        $bonusoderids = pdo_fetchall($sql);
        $inorderids = "";
        if(!empty($bonusoderids)){
            foreach ($bonusoderids as $key => $value) {
                if($key != 0){
                    $inorderids .= ",";
                }
                $inorderids = $value['orderid'];
            }
            $condition .= ' and  o.id in('.$inorderids.')';
        }else{
            $condition .= ' and  o.id=0';
        }
    }
    $agentid = intval($_GPC["agentid"]);
    $p = p("commission");
    $level = 0;
    if ($p) {
        $cset = $p->getSet();
        $level = intval($cset["level"]);
    }
    $olevel = intval($_GPC["olevel"]);
    if (!empty($agentid) && $level > 0) {
        $agent = $p->getInfo($agentid, array());
        if (!empty($agent)) {
            $agentLevel = $p->getLevel($agentid);
        }
        if (empty($olevel)) {
            if ($level >= 1) {
                $condition.= " and  ( o.agentid=" . intval($_GPC["agentid"]);
            }
            if ($level >= 2 && $agent["level2"] > 0) {
                $condition.= " or o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
            }
            if ($level >= 3 && $agent["level3"] > 0) {
                $condition.= " or o.agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
            }
            if ($level >= 1) {
                $condition.= ")";
            }
        } else {
            if ($olevel == 1) {
                $condition.= " and  o.agentid=" . intval($_GPC["agentid"]);
            } else if ($olevel == 2) {
                if ($agent["level2"] > 0) {
                    $condition.= " and o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
                } else {
                    $condition.= " and o.agentid in( 0 )";
                }
            } else if ($olevel == 3) {
                if ($agent["level3"] > 0) {
                    $condition.= " and o.agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
                } else {
                    $condition.= " and o.agentid in( 0 )";
                }
            }
        }
    }
    //是否为供应商 等于1的是
    if(p('supplier')){
        $cond = "";
        if($perm_role == 1){
            $cond .= " and o.supplier_uid={$_W['uid']} ";
            $supplierapply = pdo_fetchall('select a.id,u.uid,p.realname,p.mobile,p.banknumber,p.accountname,p.accountbank,a.applysn,a.apply_money,a.apply_time,a.type,a.finish_time,a.status from ' . tablename('sz_yi_supplier_apply') . ' a ' . ' left join' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid ' . 'left join' . tablename('users') . ' u on a.uid=u.uid where u.uid=' . $_W['uid']);
            $totals['status9'] = count($supplierapply);
            $supplier_info = p('supplier')->getSupplierInfo($_W['uid']);
            $costmoney = $supplier_info['costmoney'];
            $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',array(':uid' => $_W['uid'],':uniacid'=> $_W['uniacid']));
            if(empty($openid)){
                message("暂未绑定微信，请联系管理员", '', "error");
            }
            //全部提现
            $applytype = intval($_GPC['applytype']);
            $apply_ordergoods_ids = "";
            foreach ($supplier_info['sp_goods'] as $key => $value) {
                if ($key == 0) {
                    $apply_ordergoods_ids .= $value['ogid'];
                } else {
                    $apply_ordergoods_ids .= ','.$value['ogid'];
                }
            }
            if(!empty($applytype)){
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

                pdo_insert('sz_yi_supplier_apply',$data);
                @file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($data, 1), FILE_APPEND);
                if( pdo_insertid() ) {
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
                    @file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
                }
                message("提现申请已提交，请耐心等待!", $this->createWebUrl('order/list'), "success");
            }
        }
    }
    //Author:ym Date:2016-07-20 Content:订单分组查询
    $sql = 'select count(1) as suppliers_num, o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus,o.pay_ordersn, o.dispatchtype, o.isverify, o.storeid from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} {$statuscondition} {$cond} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ";
    if (p('bonus')) {
        $bonus_sql = 'select sum(bg.money) as bonus_total from ' . tablename("sz_yi_order") . " o" . " left join " . tablename('sz_yi_bonus_goods') . " bg on bg.orderid=o.id left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} {$statuscondition} {$cond} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ";
        $bonus_total = pdo_fetchcolumn($bonus_sql,$paras);
    }


    $export_total_sql = 'select count(1) from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} {$statuscondition} {$cond}   ";
    $export_total = pdo_fetchcolumn($export_total_sql, $paras);
    $psize = SZ_YI_EXPORT; // 每个excel文件的数量(可在defines.php文件里修改)
    $page_total = ceil($export_total / $psize);
    $orderindex = (isset($_GPC['orderindex'])) ? intval($_GPC['orderindex']) : 0;
    $current_page = (isset($_GPC['current_page'])) ? intval($_GPC['current_page']) : 1;
    for ($export_page = $current_page; $export_page <= $page_total; $export_page++ ) {
        // if ($export_page != $page_total) {
        // $_GET['current_page'] = $export_page+1;
        // $_GET['orderindex'] = $orderindex;
        // $url = "http://". $_SERVER['SERVER_NAME']."/".$_W['script_name'] . '?' . http_build_query($_GET);
        // header($url);
        // } 
        unset($list);
        unset($sql);
        $sql = 'select count(1) as suppliers_num, o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus,o.pay_ordersn, o.dispatchtype, o.isverify, o.storeid from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} {$statuscondition} {$cond} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ";
        $sql .= "LIMIT " . ($export_page - 1) * $psize . "," . $psize;
        $list = pdo_fetchall($sql, $paras);
        unset($value);

        foreach ($list as & $value) {
            $suppliers_num = $value['suppliers_num'];
            if (p('supplier')) {
                if($suppliers_num > 1){
                    $value['vendor'] = '多供应商';
                    $value['ischangePrice'] = 0;
                }else{
                    if ($value['supplier_uid'] == 0) {
                        $value['vendor'] = '总店';
                    } else {
                        $sup_username = pdo_fetchcolumn("select username from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$value['supplier_uid']}");
                        $value['vendor'] = '供应商：' . $sup_username;
                    }
                    $value['ischangePrice'] = 1;
                }
            }

            if($suppliers_num > 1 && $value['status'] == 0){
                $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
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
            }else{
                $order_where = "og.orderid = ".$value['id'];
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
                $value['status'] = $value['rstatus'];
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
            } else if ($value['cashier']==1) {
                $value["dispatchname"] = "收银台支付";
            }
            if(p('cashier') && $value['cashier'] == 1){
                $value['name'] = set_medias(pdo_fetch('select cs.name,cs.thumb from ' .tablename('sz_yi_cashier_store'). 'cs '.'left join ' .tablename('sz_yi_cashier_order'). ' co on cs.id = co.cashier_store_id where co.order_id=:orderid and co.uniacid=:uniacid', array(':orderid' => $value['id'],':uniacid'=>$_W['uniacid'])), 'thumb');
            }

            if (($value["dispatchtype"] == 1 && !empty($value["isverify"])) || !empty($value["virtual"]) || !empty($value["isvirtual"])|| $value['cashier'] == 1) {
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

            $order_goods = pdo_fetchall("select g.id,g.title,g.costprice,og.optionid,g.bonusmoney,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and ".$order_where , array(
                ":uniacid" => $_W["uniacid"]
            ));
            $goods = '';
            foreach ($order_goods as & $og) {
                if (!empty($level) && empty($agentid)) {
                    $commissions = iunserializer($og["commissions"]);
                    if (!empty($m1)) {
                        if (is_array($commissions)) {
                            $commission1+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                        } else {
                            $c1 = iunserializer($og["commission1"]);
                            $l1 = $p->getLevel($m1["openid"]);
                            $commission1+= isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                        }
                    }
                    if (!empty($m2)) {
                        if (is_array($commissions)) {
                            $commission2+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                        } else {
                            $c2 = iunserializer($og["commission2"]);
                            $l2 = $p->getLevel($m2["openid"]);
                            $commission2+= isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                        }
                    }
                    if (!empty($m3)) {
                        if (is_array($commissions)) {
                            $commission3+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                        } else {
                            $c3 = iunserializer($og["commission3"]);
                            $l3 = $p->getLevel($m3["openid"]);
                            $commission3+= isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                        }
                    }
                }
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
            //Author:ym Date:2016-08-29 Content:订单分红佣金
            if(p('bonus')){
                $bonus_area_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area!=0", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
                $bonus_range_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area=0", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
                if($bonus_area_money > 0 && $bonus_range_money > 0){
                    $bonus_money_all = $bonus_area_money + $bonus_range_money;
                    $value['bonus_money_all'] = floatval($bonus_money_all);
                }
                $value['bonus_area_money'] = floatval($bonus_area_money);
                $value['bonus_range_money'] = floatval($bonus_range_money);
            }
            $value["goods"] = set_medias($order_goods, "thumb");
            $value["goods_str"] = $goods;
            if (!empty($agentid) && $level > 0) {
                $commission_level = 0;
                if ($value["agentid"] == $agentid) {
                    $value["level"] = 1;
                    $level1_commissions = pdo_fetchall("select commission1,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and o.agentid= " . $agentid . "  and o.uniacid=:uniacid", array(
                        ":orderid" => $value["id"],
                        ":uniacid" => $_W["uniacid"]
                    ));
                    foreach ($level1_commissions as $c) {
                        $commission = iunserializer($c["commission1"]);
                        $commissions = iunserializer($c["commissions"]);
                        if (empty($commissions)) {
                            $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                        } else {
                            $commission_level+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                        }
                    }
                } else if (in_array($value["agentid"], array_keys($agent["level1_agentids"]))) {
                    $value["level"] = 2;
                    if ($agent["level2"] > 0) {
                        $level2_commissions = pdo_fetchall("select commission2,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level1_agentids"])) . ")  and o.uniacid=:uniacid", array(
                            ":orderid" => $value["id"],
                            ":uniacid" => $_W["uniacid"]
                        ));
                        foreach ($level2_commissions as $c) {
                            $commission = iunserializer($c["commission2"]);
                            $commissions = iunserializer($c["commissions"]);
                            if (empty($commissions)) {
                                $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                            } else {
                                $commission_level+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                            }
                        }
                    }
                } else if (in_array($value["agentid"], array_keys($agent["level2_agentids"]))) {
                    $value["level"] = 3;
                    if ($agent["level3"] > 0) {
                        $level3_commissions = pdo_fetchall("select commission3,commissions from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level2_agentids"])) . ")  and o.uniacid=:uniacid", array(
                            ":orderid" => $value["id"],
                            ":uniacid" => $_W["uniacid"]
                        ));
                        foreach ($level3_commissions as $c) {
                            $commission = iunserializer($c["commission3"]);
                            $commissions = iunserializer($c["commissions"]);
                            if (empty($commissions)) {
                                $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                            } else {
                                $commission_level+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                            }
                        }
                    }
                }
                $value["commission"] = $commission_level;
            }

        }
        unset($value);
        if (1) {
            ca("order.op.export");
            plog("order.op.export", "导出订单");
            $columns = array(
                array(
                    "title" => "订单编号",
                    "field" => "ordersn_general",
                    "width" => 24
                ) ,
                array(
                    "title" => "支付单号",
                    "field" => "pay_ordersn",
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
                    "title" => "成本价",
                    "field" => "costprice",
                    "width" => 12
                ) ,
                array(
                    "title" => "分红金额",
                    "field" => "bonus_money",
                    "width" => 12
                ),
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
                $orderindex++;
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
                    if (!empty($g['optionid'])) {
                        $option = m('goods')->getOption($g['id'],$g['optionid']);
                        if ($option['costprice']>0) {
                            $r['costprice'] = $option['costprice'];
                        } else {
                            $r['costprice'] = $g['costprice'];
                        }
                    } else {
                        $r['costprice'] = $g['costprice'];
                    }
                    $r['bonus_money'] = $g['bonusmoney'];
                    $exportlist[] = $r;
                }
            }
            unset($r);   
            m("excel")->exportOrder($exportlist, array(
                "title" => "order-" . date("Y-m-d-H-i", time()) ,
                "columns" => $columns
            ), $export_page, $page_total);
            if ($export_page != $page_total) {                
                $_GET['current_page'] = $export_page+1;
                $_GET['orderindex'] = $orderindex;
                $url = "http://". $_SERVER['SERVER_NAME']."/".$_W['script_name'] . '?' . http_build_query($_GET);
                $backurl = "http://". $_SERVER['SERVER_NAME']."/web/index.php?c=site&a=entry&op=display&do=order&m=sz_yi";               
                echo '<div style="border: 6px solid #e0e0e0;width: 12%;margin: 0 auto;margin-top: 12%;padding: 26px 100px;box-shadow: 0 0 14px #a2a2a2;color: #616161;">共'.$page_total.'个excel文件, 已完成'.$current_page. '个。 <div>';
                echo '<meta http-equiv="Refresh" content="1; url='.$url.'" />';
                exit;
            }
        }
    }

}


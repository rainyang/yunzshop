<?php

if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
    ca('cashier.statistics.view');
    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 20;
    $condition = ' uniacid = :uniacid';
    $params    = array(':uniacid' => $_W['uniacid']);
    if (!empty($_GPC['keyword'])) {
        $condition .= ' AND name LIKE :name OR contact LIKE :contact OR mobile LIKE :mobile OR address LIKE :address';
        $_GPC['keyword']    = trim($_GPC['keyword']);
        $params[':name']    = '%' . trim($_GPC['keyword']) . '%';
        $params[':contact'] = '%' . trim($_GPC['keyword']) . '%';
        $params[':mobile']  = '%' . trim($_GPC['keyword']) . '%';
        $params[':address'] = '%' . trim($_GPC['keyword']) . '%';
    }
    if (!empty($_GPC['time'])) {
        if ($_GPC['searchtime'] == '1') {
            $condition .= ' AND create_time >= :start AND create_time <= :end';
            $params[':start'] = $_GPC['time']['start'];
            $params[':end']   = $_GPC['time']['end'];
        }
    }
    $sql   = 'SELECT * FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition} ORDER BY id DESC LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
    $list  = pdo_fetchall($sql, $params);
    $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('sz_yi_cashier_store') . " where 1 and {$condition}", $params);
    $pager = pagination($total, $page, $pagesize);

    $tidyList = array();
    foreach ($list as &$row) {

        

        $cashier_order = pdo_fetchall('SELECT order_id FROM ' . tablename('sz_yi_cashier_order') . ' WHERE uniacid = :uniacid AND cashier_store_id = :cashier_store_id', array(':uniacid' => $_W['uniacid'], ':cashier_store_id' => $row['id']));
        $orderids = array();
        foreach ($cashier_order as $order) {
            $orderstatus = pdo_fetchall("select status from ".tablename('sz_yi_order')." where id =".$order['order_id']);
            foreach($orderstatus as $status){
                if($status['status'] == 3){
                    $orderids[] = $order['order_id'];
                }
            }
             
        }
        
        if ($orderids) {
            // 累计支付金额
            $totalprices = pdo_fetch('SELECT SUM(price) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' .$_W['uniacid']." and cashier = 1 and status = '3'  AND id IN (" . implode(',', $orderids) . ")");
            $row['totalprices'] = $totalprices['tprice'];

            

            $realtotalprices = pdo_fetch('SELECT SUM(realprice) AS tprice FROM ' . tablename('sz_yi_order') . ' WHERE uniacid = ' .$_W['uniacid']." and cashier = 1 and status = '3'  AND id IN (" . implode(',', $orderids) . ")");
            $row['realtotalprices'] = $realtotalprices['tprice'];
         
        }else{
            $row['totalprices'] = 0;
            $row['realtotalprices'] = 0;
        }

        $row['total_commission'] = 0;
        $row['total_credits']    = 0;
        $row['commission1_total'] = 0;
        $row['commission2_total'] = 0;
        $row['commission3_total'] = 0;
        foreach ($orderids as $orderid) {
            $commissions=pdo_fetch('select * from '.tablename('sz_yi_order_goods').' where orderid='.$orderid . ' and uniacid='.$_W['uniacid']);

            $row['commission1'] = iunserializer($commissions['commission1']);
            $row['commission2'] = iunserializer($commissions['commission2']);
            $row['commission3'] = iunserializer($commissions['commission3']);
            $row['commission1_total'] += $row['commission1']['default'];
            $row['commission2_total'] += $row['commission2']['default'];
            $row['commission3_total'] += $row['commission3']['default'];
            $credits = $this->model->setCredits($orderid, true);
            if ($credits) {
                $row['total_credits'] += $credits;
            }
        }
        // 已经提现成功或正申请提现的金额
        
        $row['total_no_withdraw'] = !empty($row['realtotalprices']) ? $row['realtotalprices'] : 0;
        $totalwithdraw = pdo_fetch('SELECT SUM(money) as total_money FROM ' . tablename('sz_yi_cashier_withdraw') . ' WHERE uniacid = ' . $_W['uniacid'] . ' AND cashier_store_id = ' . $row['id'] . ' AND status = 1');
        if ($totalwithdraw) {
            $row['total_withdraw'] = !empty($totalwithdraw['total_money']) ? $totalwithdraw['total_money'] : 0;
            if (!empty($row['total_withdraw'])) {
                $row['total_no_withdraw'] = number_format($row['realtotalprices'] - $row['total_withdraw'], 2);
            }
        }
        $tidyList[] = $row;
    }
}else if($operation == 'detail'){
    $id=trim($_GPC['id']);
    if(!$id){
        message("抱歉，此商户不存在!", referer() , "error");
    }
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

    $condition = ' o.uniacid = :uniacid';
    $page      = max(1, intval($_GPC['page']));
    $pagesize  = 20;
    $params    = array(':uniacid' => $_W['uniacid']);
    $condition.= " AND co.cashier_store_id = :id ";
    $params[':id' ] = $id;
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime("-1 month");
        $endtime = time();
    }
    if (!empty($_GPC["time"])) {
        $starttime = strtotime($_GPC["time"]["start"]);
        $endtime = strtotime($_GPC["time"]["end"]);
        if ($_GPC["searchtime"] == "1") {
            $condition.= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
            $params[":starttime"] = $starttime;
            $params[":endtime"] = $endtime;
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
            $params[":fstarttime"] = $fstarttime;
            $params[":fendtime"] = $fendtime;
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

    if (empty($_GPC["export"])) {
        $sql.= "LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    }
    $sql   = "SELECT o.*,co.cashier_store_id,co.order_id FROM " . tablename('sz_yi_order') . " o left join ".tablename('sz_yi_cashier_order')." co on o.id = co.order_id "." where 1 and {$condition}  and o.status = 3 and o.cashier = 1 ORDER BY o.id DESC LIMIT " . ($page - 1) * $pagesize . "," . $pagesize;
    $list  = pdo_fetchall($sql, $params);

    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('sz_yi_order') . " o left join ".tablename('sz_yi_cashier_order')." co on o.id = co.order_id "." where 1 and o.status = 3  and o.cashier = 1 and {$condition}", $params);
    $store = pdo_fetch(" select * from ".tablename('sz_yi_cashier_store')." where uniacid = :uniacid and id = :id",array(':uniacid' => $_W['uniacid'],':id' => $id));
    $pager = pagination($total, $page, $pagesize);
    $exportlist = array();
    foreach ($list as &$row) {
        if($row['deredpack'] == 1 && $row['decommission'] == 1 && $row['decredits'] == 1){
            $row['text'] = '(已扣除佣金和红包费用以及奖励余额费用)';
        }
        else if ($row['deredpack'] == 1 && $row['decommission'] == 1 && $row['decredits'] != 1){
            $row['text'] = '(已扣除红包和佣金费用)';
        }
        else if ($row['decommission'] == 1 && $row['decredits'] == 1 && $row['deredpack'] != 1){
            $row['text'] = '(已扣除佣金和奖励余额费用)';
        }
        else if ($row['deredpack'] == 1 && $row['decredits'] == 1 && $row['decommission'] != 1){
            $row['text'] = '(已扣除红包和奖励余额费用)';
        }
        else if ($row['decredits'] == 1){
            $row['text'] = '(已扣除奖励余额费用)';
        }
        else if ($row['deredpack'] == 1){
            $row['text'] = '(已扣除红包费用)';
        }
        else if ($row['decommission'] == 1){
            $row['text'] = '(已扣除佣金费用)';
        }
        $pt = $row["paytype"];
        $row["css"] = $paytype[$pt]["css"];
        $row["paytype"] = $paytype[$pt]["name"];
        $totalmoney += $row['price'];
        $realtotalmoney += $row['realprice'];
        $commission=pdo_fetch('select commission1,commission2,commission3 from '.tablename('sz_yi_order_goods').' where orderid='.$row['id']);
        $row['commission1'] = iunserializer( $commission['commission1']);
        $row['commission2'] = iunserializer( $commission['commission2']);
        $row['commission3'] = iunserializer( $commission['commission3']);
        $row['commission1'] =  $row['commission1']['default'];
        $row['commission2'] =  $row['commission2']['default'];
        $row['commission3'] =  $row['commission3']['default'];
        if($row['price'] >= $store['redpack_min']){
            $row['redpackmoney'] = $row['price']*($store['redpack']/100);
        }else{
            $row['redpackmoney'] = 0;
        }
        $row['creditpackmoney'] = $row['price']*($store['creditpack']/100);
        $row['platform_poundage'] = $row['price']*($store['settle_platform']/100);
        $row['credits'] = $this->model->setCredits($row['id'], true);
        if(!$row['credits']){
            $row['credits'] = 0;
        }
        $row['carrier'] = iunserializer($row['carrier']);
        $row['realname'] = $row['carrier']['carrier_realname'];
        $row['mobile'] = $row['carrier']['carrier_mobile'];
        $row['createtime'] = date('Y-m-d,H:i:s',$row['createtime']);
        $row["paytime"] = !empty($row["paytime"]) ? date("Y-m-d H:i:s", $row["paytime"]) : '';            
        $row["finishtime"] = !empty($row["finishtime"]) ? date("Y-m-d H:i:s", $row["finishtime"]) : '';
        if ($row["deductcredit"] > 0) {
            $row["deductcredit"] = "-" . $row["deductcredit"];
        }
        if ($row["deductcredit2"] > 0) {
            $row["deductcredit2"] = "-" . $row["deductcredit2"];
        }
        if ($row["couponprice"] > 0) {
            $row["couponprice"] = "-" . $row["couponprice"];
        }
        $exportlist[] = $row;
    }
    unset($row);
    
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
                "title" => "支付金额(元)",
                "field" => "price",
                "width" => 12
            ) ,
            array(
                "title" => "订单金额(元)",
                "field" => "goodsprice",
                "width" => 12
            ) ,
            array(
                "title" => "结算金额(元)",
                "field" => "realprice",
                "width" => 12
            ) ,
            array(
                "title" => "红包奖励(元)",
                "field" => "redpackmoney",
                "width" => 12
            ) ,
            array(
                "title" => "余额奖励(元)",
                "field" => "creditpackmoney",
                "width" => 12
            ) ,
            array(
                "title" => "一级佣金(元)",
                "field" => "commission1",
                "width" => 12
            ) ,
            array(
                "title" => "二级佣金(元)",
                "field" => "commission2",
                "width" => 12
            ) ,
            array(
                "title" => "三级佣金(元)",
                "field" => "commission3",
                "width" => 12
            ) ,
            array(
                "title" => "平台分成(元)",
                "field" => "platform_poundage",
                "width" => 12
            ) ,
            array(
                "title" => "付款方式",
                "field" => "paytype",
                "width" => 12
            ) ,
            array(
                "title" => "联系人",
                "field" => "realname",
                "width" => 12
            ) ,
            array(
                "title" => "电话",
                "field" => "mobile",
                "width" => 12
            ) ,
            array(
                "title" => "下单时间",
                "field" => "createtime",
                "width" => 24
            ) ,
            array(
                "title" => "支付时间",
                "field" => "paytime",
                "width" => 24
            ) ,
            array(
                "title" => "完成时间",
                "field" => "finishtime",
                "width" => 24
            ) ,
            array(
                "title" => "余额抵扣金额(元)",
                "field" => "deductcredit2",
                "width" => 12
            ) ,
            array(
                "title" => "积分抵扣金额(元)",
                "field" => "deductcredit",
                "width" => 12
            ) ,
            array(
                "title" => "优惠券金额",
                "field" => "couponprice",
                "width" => 12
            ) ,
            array(
                "title" => "扣除情况",
                "field" => "text",
                "width" => 36
            ) ,
        );
        
        m("excel")->export($exportlist, array(
            "title" => "订单数据-" . date("Y-m-d-H-i", time()) ,
            "columns" => $columns
        ));
    }    
}

include $this->template('statistics');

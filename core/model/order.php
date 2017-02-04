<?php
/*=============================================================================
#     FileName: order.php
#         Desc: 订单类
#       Author: RainYang - https://github.com/rainyang
#        Email: rainyang2012@qq.com
#     HomePage: http://rainyang.github.io
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:34:01
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Order
{
    function getStoreList(){
        global $_W;
        return pdo_fetchall("SELECT * FROM ".tablename('sz_yi_store')." WHERE uniacid=:uniacid and status=1", array(':uniacid' => $_W['uniacid']));
    }
    
    function getDispatchPrice($weight, $dispatch_data, $calculatetype = -1){
        if (empty($dispatch_data)){
            return 0;
        }
        $price = 0;
        if ($calculatetype == -1){
            $calculatetype = $dispatch_data['calculatetype'];
        }
        if ($calculatetype == 1){
            if ($weight <= $dispatch_data['firstnum']){
                $price = floatval($dispatch_data['firstnumprice']);
            }else{
                $price = floatval($dispatch_data['firstnumprice']);
                $secondweight = $weight - floatval($dispatch_data['firstnum']);
                $dsecondweight = floatval($dispatch_data['secondnum']) <= 0 ? 1 : floatval($dispatch_data['secondnum']);
                $secondprice = 0;
                if ($secondweight % $dsecondweight == 0){
                    $secondprice = ($secondweight / $dsecondweight) * floatval($dispatch_data['secondnumprice']);
                }else{
                    $secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($dispatch_data['secondnumprice']);
                }
                $price += $secondprice;
            }
        }else{
            if ($weight <= $dispatch_data['firstweight']){
                $price = floatval($dispatch_data['firstprice']);
            }else{
                $price = floatval($dispatch_data['firstprice']);
                $secondweight = $weight - floatval($dispatch_data['firstweight']);
                $dsecondweight = floatval($dispatch_data['secondweight']) <= 0 ? 1 : floatval($dispatch_data['secondweight']);
                $secondprice = 0;
                if ($secondweight % $dsecondweight == 0){
                    $secondprice = ($secondweight / $dsecondweight) * floatval($dispatch_data['secondprice']);
                }else{
                    $secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($dispatch_data['secondprice']);
                }
                $price += $secondprice;
            }
        }
        return $price;
    }

    /*
    function getDispatchPrice($weight, $d)
    {
        if (empty($d)) {
            return 0;
        }
        $price = 0;
        if ($weight <= $d['firstweight']) {
            $price = floatval($d['firstprice']);
        } else {
            $price         = floatval($d['firstprice']);
            $secondweight  = $weight - floatval($d['firstweight']);
            $dsecondweight = floatval($d['secondweight']) <= 0 ? 1 : floatval($d['secondweight']);
            $secondprice   = 0;
            if ($secondweight % $dsecondweight == 0) {
                $secondprice = ($secondweight / $dsecondweight) * floatval($d['secondprice']);
            } else {
                $secondprice = ((int) ($secondweight / $dsecondweight) + 1) * floatval($d['secondprice']);
            }
            $price += $secondprice;
        }
        return $price;
    }
     */

    /*
    function getCityDispatchPrice($_var_6, $_var_7, $weight, $d)
    {
        if (is_array($_var_6) && count($_var_6) > 0) {
            foreach ($_var_6 as $_var_8) {
                $_var_9 = explode(';', $_var_8['citys']);
                if (in_array($_var_7, $_var_9) && !empty($_var_9)) {
                    return $this->getDispatchPrice($weight, $_var_8);
                }
            }
        }
        return $this->getDispatchPrice($weight, $d);
    }
     */

    function getCityDispatchPrice($areas, $city, $param, $dispatch_data){
        if (is_array($areas) && count($areas) > 0){
            foreach ($areas as $area){
                $citys = explode(';', $area['citys']);

                //处理运费模版辖区,辖县问题
                if(mb_strlen($city) == mb_strrpos($city, "市") + 1){
                    $cityShortName = mb_substr($city, 0, mb_strlen($city) - 1);
                    if (!empty($citys) && (in_array($city, $citys) || in_array($cityShortName . "辖区", $citys) || in_array($cityShortName . "辖县", $citys))){
                        return $this->getDispatchPrice($param, $area, $dispatch_data['calculatetype']);
                    }
                }
                if (!empty($citys) && in_array($city, $citys)){
                    return $this->getDispatchPrice($param, $area, $dispatch_data['calculatetype']);
                }
            }
        }
        return $this->getDispatchPrice($param, $dispatch_data);
    }

    /**
     * 判断是否有不支持当前配送方式的商品
     * @param params array
     * @return array()
     * modify Wujingyu 2016.10.14
     */
    public function isSupportDelivery($order_data = array()) {
        global $_W;
        foreach ($order_data as $key => $order_value) {
            $dispatchtype1 = intval($order_value['dispatchtype']);
            $goodsarr_1      = explode('|', $order_value['goods']);
            $dispatchsend1 = false;
            if ($dispatchtype1 == '2') {
                $dispatchtype1 = '0';
                $dispatchsend1 = true;
            }
            $can_goodsid_1 = array();
            foreach ($goodsarr_1 as $row1) {
                if (!empty($row1)) {
                    $row1 = explode(',', $row1);
                    $can_goodsid_1[] = $row1[0];
                }
            }
            if (!empty($can_goodsid_1) && is_array($can_goodsid_1)) {
                $goods_data = pdo_fetchall(" SELECT id,isverify,isverifysend,dispatchsend,title FROM " .tablename('sz_yi_goods'). " WHERE uniacid=:uniacid AND id IN (".implode(',', $can_goodsid_1).")", array(':uniacid' => $_W['uniacid']));

            }

            foreach ($goods_data as $gdata) {
                $isverify1  = false;
                $isverifysend1  = false;
                if ($gdata['isverify'] == 2 && !$dispatchsend1) {
                    $isverify1 = true;
                }
                if (empty($dispatchtype1) && $isverify1) {
                    $isverifysend1 = true;
                }
                //判断此商品是否支持配送核销
                if ($isverifysend1) {
                    if ($gdata['isverifysend'] != 1) {
                        $info = array('status' => -1, 'title' => $gdata['title']);
                        return $info;
                    }

                }
                //判断此商品是否支持快递配送
                if ($dispatchsend1) {
                    if ($gdata['dispatchsend'] != 1) {
                        $info = array('status' => -2, 'title' => $gdata['title']);
                        return $info;
                    }
                }
            }
        }
        return array('status' => 1);
    }
    /**
     * 支付完成回调方法
     * @param params array
     * @return array()
     * modify RainYang 2016.4.7
     */
    public function payResult($params)
    {
        global $_W;
        $fee     = $params['fee'];
        $uniacid = $_W['uniacid'];
        $data    = array(
            'status' => $params['result'] == 'success' ? 1 : 0
        );
        $ordersn = $params['tid'];
        $orderall = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
            ':ordersn_general' => $ordersn,
            ':uniacid' => $uniacid
        ));
        //dump($orderall);exit;
        if(count($orderall) > 1){
            $order = array();
            $order['ordersn'] = $ordersn;
            $orderid = array();
            foreach ($orderall as $key => $val) {
                $order['price']           += $val['price'];
                $order['deductcredit2']   += $val['deductcredit2'];
                $order['ordersn2']        += $val['ordersn2'];
                $orderid[]                 = $val['id'];
            }
            $order['dispatchtype'] = $val['dispatchtype'];
            $order['addressid'] = $val['addressid'];
            $order['isvirtual'] = $val['isvirtual'];
            $order['carrier'] = $val['carrier'];
            $order['status'] = $val['status'];
            $order['virtual'] = $val['virtual'];
            $order['couponid'] = $val['couponid'];
        }else{
            $order   = $orderall[0];
            $orderid = $order['id'];
            $verify_set = m('common')->getSetData();
            $allset = iunserializer($verify_set['plugins']);
            $pset = m('common')->getSysset();
            if ($order['isverify'] == 1 && isset($allset['verify']) && $allset['verify']['sendcode'] == 1 && isset($pset['sms']) && $pset['sms']['type'] == 1) {
                $carriers = unserialize($order['carrier']);
                $address = unserialize($order['address']);
                if (empty($order['dispatchtype'])) {
                    $mobile = $address['mobile'];
                } else {
                    $mobile = $carriers['carrier_mobile'];
                }
                $type = 'verify';
                $order_goods = pdo_fetch("SELECT * FROM ".tablename('sz_yi_order_goods')." WHERE orderid=:id and uniacid=:uniacid", array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
                $goodstitle = pdo_fetchcolumn("SELECT title FROM ".tablename('sz_yi_goods')." WHERE id=:id and uniacid=:uniacid",array(':id' => $order_goods['goodsid'], ':uniacid' => $_W['uniacid']));
                $store = pdo_fetch(" SELECT * FROM ".tablename('sz_yi_store')." WHERE id=".$order['storeid']);
                $issendsms = $this->sendSms($mobile, $order['verifycode'], 'reg', $type, $carriers['carrier_realname'],$goodstitle, $order_goods['total'], $store['tel']);
                
            }
        }

        //验证paylog里金额是否与订单金额一致
        $log = pdo_fetch('select * from ' . tablename('core_paylog') . ' where `uniacid`=:uniacid and fee=:fee and `module`=:module and `tid`=:tid limit 1',
            array(
            ':uniacid' => $_W['uniacid'],
            ':module' => 'sz_yi',
            ':fee' => $fee,
            ':tid' => $ordersn
        ));
        if (empty($log)) {
            return show_json(-1, '订单金额错误, 请重试!');
            exit;
        }

        //$orderid = $order['id'];
        if ($params['from'] == 'return') {
            $address = false;
            if (empty($order['dispatchtype'])) {
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(
                    ':id' => $order['addressid']
                ));
            }
            $carrier = false;
            if ($order['dispatchtype'] == 1 || $order['isvirtual'] == 1) {
                $carrier = unserialize($order['carrier']);
            }
            if ($params['type'] == 'cash') {
                return array(
                    'result' => 'success',
                    'order' => $order,
                    'address' => $address,
                    'carrier' => $carrier
                );
            } else {
                //多供应商支付成功条件
                if(is_array($orderid)){
                    $orderids     = implode(',', $orderid);
                    $order_update = "id in ({$orderids})";
                    $orderdetail_where = "o.id in ({$orderids})";
                    $goods_where = "og.orderid in ({$orderids})";
                    $orderids_where = " and orderid in ({$orderids})";
                }else{
                    $order_update = "id = ".$orderid;
                    $orderdetail_where = "o.id = {$orderid}";
                    $goods_where = "og.orderid = {$orderid}";
                    $orderids_where = " and orderid = {$orderid}";
                }
                if ($order['status'] == 0) {
                    $pv = p('virtual');
                    if (!empty($order['virtual']) && $pv) {
                        $pv->pay($order);
                    } else {
                        if (p('channel')) {
                            //渠道商采购的订单，直接完成
                            if ($params['ischannelpay'] == 1) {
                                pdo_query('update ' . tablename('sz_yi_order') . " set status=3, paytime=".time().", finishtime=".time().", pay_ordersn=ordersn_general, ordersn_general=ordersn where {$order_update} and uniacid='{$uniacid}' ");
                            } else {
                                pdo_query('update ' . tablename('sz_yi_order') . " set status=1, paytime=".time().", pay_ordersn=ordersn_general, ordersn_general=ordersn where {$order_update} and uniacid='{$uniacid}' ");
                            }
                        } else {
                            pdo_query('update ' . tablename('sz_yi_order') . " set status=1, paytime=".time().", pay_ordersn=ordersn_general, ordersn_general=ordersn where {$order_update} and uniacid='{$uniacid}' ");
                        }
                        if ($order['deductcredit2'] > 0) {
                            $shopset = m('common')->getSysset('shop');
                            m('member')->setCredit($order['openid'], 'credit2', -$order['deductcredit2'], array(
                                0,
                                $shopset['name'] . "余额抵扣: {$order['deductcredit2']} 订单号: " . $order['ordersn']
                            ));
                        }
                        //if ($order['order_type'] != '4') {
                            //$order['order_type']=4 为夺宝订单 夺宝订单不执行下面代码
                            if(is_array($orderid)){
                                foreach ($orderall as $k => $v) {
                                    if (p('channel')) {
                                        //更改库存
                                        p('channel')->changeStock($v['id']);
                                    } else {
                                        $this->setStocksAndCredits($v['id'], 1);
                                    }
                                    if (p('coupon') && !empty($v['couponid'])) {
                                        p('coupon')->backConsumeCoupon($v['id']);
                                    }
                                    m('notice')->sendOrderMessage($v['id']);
                                    if (p('commission')) {
                                        p('commission')->checkOrderPay($v['id']);
                                    }
                                }
                            }else{
                                $this->setStocksAndCredits($orderid, 1);
                                if (p('channel')) {
                                    //更改库存
                                    p('channel')->changeStock($orderid);
                                }
                                if (p('coupon') && !empty($order['couponid'])) {
                                    p('coupon')->backConsumeCoupon($orderid);
                                }
                                m('notice')->sendOrderMessage($orderid);
                                if (p('commission')) {
                                    p('commission')->checkOrderPay($orderid);
                                }
                            }
                        //}  

                    }
                }
                //云打印
                if (p('yunprint')) {
                    $yunprint_set = p('yunprint')->getSet();
                    if ($yunprint_set['isopenprint'] == 1) {
                        p('yunprint')->executePrint($order['id']);
                    }
                }
                //支付后订单打印
                if(p('hotel')){
                //打印订单      
                $set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
                //订单信息
                $print_order = $order;
                //商品信息
                $ordergoods = pdo_fetchall("select * from " . tablename('sz_yi_order_goods') . " where uniacid=".$_W['uniacid'] . $orderids_where);
                $plugin_fund = p('fund');
                    foreach ($ordergoods as $key =>$value) {
                        if($plugin_fund){
                            $plugin_fund->check_goods($value['goodsid']);
                        }
                        //$ordergoods[$key]['price'] = pdo_fetchcolumn("select marketprice from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                        $ordergoods[$key]['goodstitle'] = pdo_fetchcolumn("select title from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                        $ordergoods[$key]['totalmoney'] = number_format($ordergoods[$key]['price']*$value['total'],2);
                        $ordergoods[$key]['print_id'] = pdo_fetchcolumn("select print_id from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                        $ordergoods[$key]['type'] = pdo_fetchcolumn("select type from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");

                    }
                    $print_order['goods']=$ordergoods;
                    $print_id = $print_order['goods'][0]['print_id'];
                    $goodtype = $print_order['goods'][0]['type'];
                    if($print_id!=''){
                        $print_detail = pdo_fetch("select * from " . tablename('sz_yi_print_list') . " where uniacid={$_W['uniacid']} and id={$print_id}");
                        if(!empty($print_detail) &&  $print_detail['status']=='0'){//是否存在打印机，以及判断是否为支付前打印
                                $member_code = $print_detail['member_code'];
                                $device_no = $print_detail['print_no'];
                                $key = $print_detail['key'];
                                include IA_ROOT.'/addons/sz_yi/core/model/print.php';
                                if($goodtype=='99'){//类型为房间
                                    //房间金额信息
                                    $sql2 = 'SELECT * FROM ' . tablename('sz_yi_order_room') . ' WHERE `orderid` = :orderid';
                                    $params2 = array(':orderid' => $orderid);
                                    $price_list = pdo_fetchall($sql2, $params2);
                                    $msgNo = testSendFreeMessage($print_order, $member_code, $device_no, $key,$set,$price_list);
                                }else if($goodtype=='1'){
                                     $msgNo = testSendFreeMessageshop($print_order, $member_code, $device_no, $key,$set);
                                }
                        }
                    }
                }
                
                $orderdetail=pdo_fetch("select o.dispatchprice,o.ordersn,o.price,og.optionname as optiontitle,og.optionid,og.total from " .tablename('sz_yi_order'). " o left join " .tablename('sz_yi_order_goods').  "og on og.orderid = o.id where {$orderdetail_where} and o.uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
                $sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where ' . $goods_where . ' order by og.id asc';
                $orderdetail['goods1'] = set_medias(pdo_fetchall($sql), 'thumb');
                $pluginlove = p('love');
                if($pluginlove){
                   $pluginlove->checkOrder($goods_where, $order['openid'], 0);
                }
                $orderdetail['goodscount'] = count($orderdetail['goods1']);
                if ($order['order_type'] == '4') {
                    p('indiana')->dispose($orderid);
                }
                return array(
                    'result' => 'success',
                    'order' => $order,
                    'address' => $address,
                    'carrier' => $carrier,
                    'virtual' => $order['virtual'],
                    'goods'=>$orderdetail,
                    'verifycode'=>$issendsms

                );
            }
        }
    }
    function sendSms($mobile, $code, $templateType = 'reg', $type = 'check', $name = '', $title = '', $total = '', $tel = '')
    {
        $set = m('common')->getSysset();
        if ($set['sms']['type'] == 1) {
            return send_sms($set['sms']['account'], $set['sms']['password'], $mobile, $code, $type, $name, $title, $total, $tel);
        } else {
            return send_sms_alidayu($mobile, $code, $templateType);
        }
    }
    function setStocksAndCredits($orderid = '', $type = 0)
    {
        global $_W;
        $verifyset  = m('common')->getSetData();
        $allset = iunserializer($verifyset['plugins']);
        $store_total = false;
        if (isset($allset['verify']) && $allset['verify']['store_total'] == 1) {
            $store_total = true;
        }
        $order   = pdo_fetch('select id,ordersn,price,openid,dispatchtype,addressid,carrier,status,storeid,status from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(
            ':id' => $orderid
        ));
        $cond = "";
        if (p('channel')) {
            $cond    = ',og.channel_id,og.ischannelpay';
        }
        $goods   = pdo_fetchall("select og.id,og.goodsid" . $cond . ",og.total,g.totalcnf,og.realprice, g.credit,og.optionid,g.total as goodstotal,og.optionid,g.sales,g.salesreal from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $orderid
        ));

        //comment:如果是渠道商零售或者采购的订单不走下面的逻辑(造成即扣除了渠道商的库存又扣除了商城的库存,这么改的影响是：只要和渠道商有关的订单都不会赠送积分) edit yangyang date 01-03

        $credits = 0;

        foreach ($goods as $g) {
            if (!empty($g['channel_id'])) {
                return;
            }
            $stocktype = 0;
            if ($type == 0) {
                if ($g['totalcnf'] == 0) {
                    $stocktype = -1;
                }
            } else if ($type == 1) {
                if ($g['totalcnf'] == 1) {
                    $stocktype = -1;
                }
            } else if ($type == 2) {
                if ($order['status'] >= 1) {
                    if ($g['totalcnf'] == 1) {
                        $stocktype = 1;
                    }
                } else {
                    if ($g['totalcnf'] == 0) {
                        $stocktype = 1;
                    }
                }
            }
            if (!empty($stocktype)) {
                if (!empty($g['optionid'])) {
                    if (!$store_total) {
                        $option = m('goods')->getOption($g['goodsid'], $g['optionid']);
                        if (!empty($option) && $option['stock'] != -1) {
                            $stock = -1;
                            if ($stocktype == 1) {
                                $stock = $option['stock'] + $g['total'];
                            } else if ($stocktype == -1) {
                                $stock = $option['stock'] - $g['total'];
                                $stock <= 0 && $stock = 0;
                            }
                            if ($stock != -1) {
                                pdo_update('sz_yi_goods_option', array(
                                    'stock' => $stock
                                ), array(
                                    'uniacid' => $_W['uniacid'],
                                    'goodsid' => $g['goodsid'],
                                    'id' => $g['optionid']
                                ));
                            }
                        }
                    }
                    if (!empty($order['storeid']) && $store_total) {
                        $option = pdo_fetch("SELECT total as stock FROM ".tablename('sz_yi_store_goods')." WHERE goodsid=:goodsid and optionid=:optionid and uniacid=:uniacid and storeid=:storeid", array(':goodsid' => $g['goodsid'], ':optionid' => $g['optionid'], ':uniacid' => $_W['uniacid'], ':storeid' => $order['storeid']));
                        if (!empty($option) && $option['stock'] != -1) {
                            $stock = -1;
                            if ($stocktype == 1) {
                                $stock = $option['stock'] + $g['total'];
                            } else if ($stocktype == -1) {
                                $stock = $option['stock'] - $g['total'];
                                $stock <= 0 && $stock = 0;
                            }
                            if ($stock != -1) {
                                pdo_update('sz_yi_store_goods', array(
                                    'total' => $stock
                                ), array(
                                    'uniacid' => $_W['uniacid'],
                                    'goodsid' => $g['goodsid'],
                                    'optionid' => $g['optionid'],
                                    'storeid' => $order['storeid']

                                ));
                            }
                        }
                    }
                } else {
                    if (!$store_total) {
                        if (!empty($g['goodstotal']) && $g['goodstotal'] != -1) {
                            $totalstock = -1;
                            if ($stocktype == 1) {
                                $totalstock = $g['goodstotal'] + $g['total'];
                            } else if ($stocktype == -1) {
                                $totalstock = $g['goodstotal'] - $g['total'];
                                $totalstock <= 0 && $totalstock = 0;
                            }
                            if ($totalstock != -1) {
                                pdo_update('sz_yi_goods', array(
                                    'total' => $totalstock
                                ), array(
                                    'uniacid' => $_W['uniacid'],
                                    'id' => $g['goodsid']
                                ));
                            }
                        }
                    }
                    if (!empty($order['storeid']) && $store_total) {
                        $store_goods = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store_goods')." WHERE goodsid=:goodsid and storeid=:storeid and optionid=0", array(':goodsid' => $g['goodsid'], ':storeid' => $order['storeid']));
                        if (!empty($store_goods['total']) && $store_goods['total'] != -1) {
                            $totalstock = -1;
                            if ($stocktype == 1) {
                                $totalstock = $store_goods['total'] + $g['total'];
                            } else if ($stocktype == -1) {
                                $totalstock = $store_goods['total'] - $g['total'];
                                $totalstock <= 0 && $totalstock = 0;
                            }
                            if ($totalstock != -1) {
                                pdo_update('sz_yi_store_goods', array(
                                    'total' => $totalstock
                                ), array(
                                    'uniacid' => $_W['uniacid'],
                                    'goodsid' => $g['goodsid'],
                                    'storeid' => $order['storeid'],
                                    'optionid' => 0
                                ));
                            }
                        }
                    }
                }

            }
            $gcredit = trim($g['credit']);
            if (!empty($gcredit)) {
                if (strexists($gcredit, '%')) {
                    $credits += intval(floatval(str_replace('%', '', $gcredit)) / 100 * $g['realprice']);
                } else {
                    $credits += intval($g['credit']) * $g['total'];
                }
            }
            if ($type == 0) {
                pdo_update('sz_yi_goods', array(
                    'sales' => $g['sales'] + $g['total']
                ), array(
                    'uniacid' => $_W['uniacid'],
                    'id' => $g['goodsid']
                ));
            } elseif ($type == 1) {
                if ($order['status'] >= 1) {
                    $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(
                        ':goodsid' => $g['goodsid'],
                        ':uniacid' => $_W['uniacid']
                    ));
                    pdo_update('sz_yi_goods', array(
                        'salesreal' => $salesreal
                    ), array(
                        'id' => $g['goodsid']
                    ));
                }
            }
        }
        if ($credits > 0) {
            $shopset = m('common')->getSysset('shop');
            if ($type == 1) {
                m('member')->setCredit($order['openid'], 'credit1', $credits, array(
                    0,
                    $shopset['name'] . '购物积分 订单号: ' . $order['ordersn']
                ));
                pdo_update('sz_yi_order', array('credit1'=>$credits), array('ordersn' => $order['ordersn'], 'uniacid' => $_W['uniacid']));
            } elseif ($type == 2) {
                if ($order['status'] >= 1) {
                    m('member')->setCredit($order['openid'], 'credit1', -$credits, array(
                        0,
                        $shopset['name'] . '购物取消订单扣除积分 订单号: ' . $order['ordersn']
                    ));
                }
            }
        }
    }
    function getDefaultDispatch($supplier_uid = 0){
        global $_W;
        $sql = 'select * from ' . tablename('sz_yi_dispatch') . ' where isdefault=1 and uniacid=:uniacid and enabled=1 and supplier_uid=:supplier_uid Limit 1';
        $prem = array(':supplier_uid' => $supplier_uid, ':uniacid' => $_W['uniacid']);
        $DefaultDispatch = pdo_fetch($sql, $prem);
        return $DefaultDispatch;
    }
    function getNewDispatch($supplier_uid = 0){
        global $_W;
        $sql = 'select * from ' . tablename('sz_yi_dispatch') . ' where uniacid=:uniacid and enabled=1 and supplier_uid=:supplier_uid order by id desc Limit 1';
        $prem = array(':supplier_uid' => $supplier_uid,':uniacid' => $_W['uniacid']);
        $NewDispatch = pdo_fetch($sql, $prem);
        return $NewDispatch;
    }
    function getOneDispatch($dispatch_id, $supplier_uid = 0){
        global $_W;
        $sql = 'select * from ' . tablename('sz_yi_dispatch') . ' where id=:id and uniacid=:uniacid and enabled=1 and supplier_uid=:supplier_uid Limit 1';
        $prem = array(':supplier_uid' => $supplier_uid, ':id' => $dispatch_id, ':uniacid' => $_W['uniacid']);
        $OneDispatch = pdo_fetch($sql, $prem);
        return $OneDispatch;
    }

    //自动执行方法
    function autoexec($uniacid = 0){
        global $_W, $_GPC;
        if(empty($uniacid)){
            return;
        }
        $_W['uniacid'] = $uniacid;
        $trade = m('common')->getSysset('trade', $_W['uniacid']);
        $days = intval($trade['receive']);
        if ($days > 0) {
            $daytimes = 86400 * $days;
            $p = p('commission');
            $pcoupon = p('coupon');
            $orders = pdo_fetchall('select id,couponid from ' . tablename('sz_yi_order') . " where uniacid={$_W['uniacid']} and status=2 and sendtime + {$daytimes} <=unix_timestamp() ", array(), 'id');
            if (!empty($orders)) {
                $orderkeys = array_keys($orders);
                $orderids = implode(',', $orderkeys);
                if (!empty($orderids)) {
                    pdo_query('update ' . tablename('sz_yi_order') . ' set status=3,finishtime=' . time() . ' where id in (' . $orderids . ')');
                    foreach ($orders as $orderid => $o) {
                        m('notice')->sendOrderMessage($orderid);
                        if ($pcoupon) {
                            if (!empty($o['couponid'])) {
                                $pcoupon->backConsumeCoupon($o['id']);
                            }
                        }
                        if ($p) {
                            $p->checkOrderFinish($orderid);
                        }
                        if (p('return')) {
                            p('return')->cumulative_order_amount($orderid);
                        }

                        if (p('yunbi')) {
                            p('yunbi')->GetVirtualCurrency($orderid);
                        }
                        if (p('beneficence')) {
                            p('beneficence')->GetVirtualBeneficence($orderid);
                        }
                        
                    }
                }
            }
        }
        $days = intval($trade['closeorder']);
        if ($days > 0) {
            $daytimes = 86400 * $days;
            $orders = pdo_fetchall('select id from ' . tablename('sz_yi_order') . " where  uniacid={$_W['uniacid']} and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp() ");
            $p = p('coupon');
            foreach ($orders as $o) {
                $onew = pdo_fetch('select status from ' . tablename('sz_yi_order') . " where id=:id and status=0 and paytype<>3  and createtime + {$daytimes} <=unix_timestamp()  limit 1", array(':id' => $o['id']));
                if (!empty($onew) && $onew['status'] == 0) {
                    //商品返库存

                    $this->returnStock($o['id']);

                    pdo_query('update ' . tablename('sz_yi_order') . ' set status=-1,canceltime=' . time() . ' where id=' . $o['id']);
                    if ($p) {
                        if (!empty($o['couponid'])) {
                            $p->returnConsumeCoupon($o['id']);
                        }
                    }
                }
            }
        }
    }

    /**
     * 获取订单商品
     *
     * @param $orderid
     * @return array
     */
    public function getOrderGodds($orderid)
    {
        global $_W;

        $order_goods = pdo_fetchall("select g.id,og.orderid, g.type, og.total,og.optionid, og.totalcnf from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ",

            array(
                ":uniacid" => $_W["uniacid"],
                ":orderid" => $orderid
            ));

        return $order_goods;
    }

    /**
     * 更新无规格商品库存
     *
     * @param $goodsid
     * @param $total
     */
    public function updateGoodsStock($goodsid, $total)
    {
        global $_W;
        if ($goodsid) {
            $total = intval($total);
            $sql = "UPDATE " . tablename('sz_yi_goods') . " SET total = total + " . $total . " 
                    WHERE uniacid= " . $_W['uniacid'] . " AND id= " . $goodsid;

            pdo_query($sql);
        }
    }

    /**
     * 更新规格商品库存
     *
     * @param $goodsid
     * @param $optionid
     * @param $total
     */
    public function updateGoodsOptionStock($goodsid, $optionid, $total)
    {
        global $_W;
        if ($goodsid && $optionid) {
            $total = intval($total);
            $sql = "UPDATE " . tablename('sz_yi_goods_option') . " SET stock = stock + " . $total . " 
                WHERE uniacid= " . $_W['uniacid'] .  " AND id = " . $optionid . " AND goodsid= " . $goodsid ;
        }
        pdo_query($sql);
    }

    /**
     * 更新虚拟商品记录
     *
     * @param $orderid
     */
    public function updateVirtualGoodsRecord($orderid, $goodsid)
    {
        global $_W;

        $typeid = pdo_fetchcolumn("SELECT typeid FROM " . tablename('sz_yi_virtual_data') . " 
                   WHERE uniacid=:uniacid AND orderid=:orderid",
                   array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));

        $this->updateGoodsStock($goodsid, 1);

        $sql = "UPDATE " . tablename('sz_yi_virtual_data') . " SET openid = '', usetime = '0', orderid = '0', ordersn = '0', price = '0'
                WHERE uniacid = " . $_W['uniacid'] . " AND orderid = " . $orderid;

        pdo_query($sql);

        $sql = "UPDATE " . tablename('sz_yi_virtual_type') . " SET usedata = usedata-1
                WHERE uniacid = " . $_W['uniacid'] . " AND id = " . $typeid;

        pdo_query($sql);

    }

    /**
     * 订单取消商品返库存
     *
     * @param $orderid
     */
    public function returnStock($orderid)
    {
        //订单商品
        $order_goods = m('order')->getOrderGodds($orderid);

        //商品返库存
        foreach ($order_goods as $items) {
            if ($items['totalcnf'] == 0) {//拍下减库存
                if ($items['type'] == 3) {//虚拟产品
                    m('order')->updateVirtualGoodsRecord($items['orderid'], $items['id']);
                } elseif (!empty($items['optionid'])) {//多规格商品
                    m('order')->updateGoodsOptionStock($items['id'], $items['optionid'], $items['total']);
                } else {//无规格商品
                    m('order')->updateGoodsStock($items['id'], $items['total']);
                }
            }
        }
    }
}

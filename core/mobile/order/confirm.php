<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member    = m("member")->getMember($openid);
$shopset   = m('common')->getSysset('shop');
$uniacid   = $_W['uniacid'];
$fromcart  = 0;
$trade     = m('common')->getSysset('trade');
$verifyset  = m('common')->getSetData();
$allset = iunserializer($verifyset['plugins']);
$store_total = false;
$issale = true;

if (isset($allset['verify']) && $allset['verify']['store_total'] == 1) {
    $store_total = true;
}
if (p('recharge')) {
        $telephone =  $_GPC['telephone'];
    }
if (!empty($trade['shareaddress'])  && is_weixin()) {
    if (!$_W['isajax']) {
        $shareAddress = m('common')->shareAddress();
        if (empty($shareAddress)) {
            exit;
        }
    }
}

$pv = p('virtual');
$hascouponplugin = false;
$plugc           = p("coupon");
if ($plugc) {
    $hascouponplugin = true;
}
$hascard = false;
$plugincard = p('card');
if ($plugincard) {
    $hascard = true;
    $card_set = $plugincard->getSet();
}
$goodid = $_GPC['id'] ? intval($_GPC['id']) : 0;
$cartid = $_GPC['cartids'] ? $_GPC['cartids'] : 0;
$diyform_plugin = p("diyform");
$order_formInfo = false;

if ($diyform_plugin) {
    $diyform_set = $diyform_plugin->getSet();
    if (!empty($diyform_set["order_diyform_open"])) {
        $orderdiyformid = intval($diyform_set["order_diyform"]);
        if (!empty($orderdiyformid)) {
            $order_formInfo = $diyform_plugin->getDiyformInfo($orderdiyformid);
            $fields         = $order_formInfo["fields"];
            $f_data         = $diyform_plugin->getLastOrderData($orderdiyformid, $member);
        }
    }
}
$carrier_list = pdo_fetchall("SELECT * FROM " . tablename("sz_yi_store") . " WHERE uniacid=:uniacid AND status=1 AND myself_support=1", array(
    ":uniacid" => $_W["uniacid"]
));

$isladder = false;
if (p('ladder')) {
    $ladder_set = p('ladder')->getSet();
    if ($ladder_set['isladder']) {
        $isladder = true;   
    }
}
if ($operation == "display" || $operation == "create") {
    $id   = ($operation == "create") ? intval($_GPC["order"][0]["id"]) : intval($_GPC["id"]);
    $show = 1;
    if ($diyform_plugin) {
        if (!empty($id)) {
            $sql         = "SELECT id as goodsid,type,diyformtype,diyformid,diymode FROM " . tablename("sz_yi_goods") . " WHERE id=:id AND uniacid=:uniacid  limit 1";
            $goods_data  = pdo_fetch($sql, array(
                ":uniacid" => $uniacid,
                ":id" => $id
            ));
            //print_r($goods_data);exit;
            $diyformtype = $goods_data["diyformtype"];
            $diyformid   = $goods_data["diyformid"];
            $diymode     = $goods_data["diymode"];
            if (!empty($diyformtype) && !empty($diyformid)) {
                $formInfo      = $diyform_plugin->getDiyformInfo($diyformid);
                $goods_data_id   = $operation == "create" ? intval($_GPC["order"][0]["gdid"]) : intval($_GPC["gdid"]);
            }
        }
    }
}

$ischannelpick = $_GPC['ischannelpick'];

if ($operation == "date") {
    global $_GPC, $_W;
    $id   = intval($_GPC["id"]);
    if ($search_array && !empty($search_array['bdate']) && !empty($search_array['day'])) {
        $bdate = $search_array['bdate'];
        $day = $search_array['day'];
    } else {
        $bdate = date('Y-m-d');
        $day = 1;
    }
    load()->func('tpl');
    include $this->template('order/date');
    exit;
} elseif ($operation == 'ajaxData') {
    global $_GPC, $_W;
    $id   = intval($_GPC["id"]);
    switch ($_GPC['ac']) {
        //选择日期
        case 'time':
            $bdate = $_GPC['bdate'];
            $day = $_GPC['day'];
            if (!empty($bdate) && !empty($day)) {
                $btime = strtotime($bdate);
                $etime = $btime + $day * 86400;
                //$weekarray = array("日", "一", "二", "三", "四", "五", "六");
                $data['btime'] = $btime;
                $data['etime'] = $etime;
                $data['bdate'] = $bdate;
                $data['edate'] = date('Y-m-d', $etime);
                //$data['bweek'] = '星期' . $weekarray[date("w", $btime)];
                //$data['eweek'] = '星期' . $weekarray[date("w", $etime)];
                $data['day'] = $day;
                //setcookie('data',serialize($data),time()+2*7*24*3600);
                $_SESSION['data']=$data;
                $url = $this->createMobileUrl('order', array('p' =>'confirm','id'=> $id));
                die(json_encode(array("result" => 1, "url" => $url)));
            }
            break;
    }
} elseif ($operation == 'ladder' && $_W['isajax']) {
    $laddermoney = 0;
    if ($isladder) {
        $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                ':id' => $_GPC['goodsid']
            ));
        if ($ladders) {
            $ladders = unserialize($ladders['ladders']);
            $laddermoney = m('goods')->getLaderMoney($ladders,$_GPC['total']);
        }
        if (intval($_GPC['optionid'])) {
            $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,virtual,stock,weight,option_ladders from ' . tablename('sz_yi_goods_option') . ' WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1', array(
                        ':uniacid' => $uniacid,
                        ':goodsid' => $_GPC['goodsid'],
                        ':id' => $_GPC['optionid']
                    ));
            //阶梯价格
            if ($isladder) {
                $ladders = unserialize($option['option_ladders']);
                if ($ladders) {
                    $laddermoney = m('goods')->getLaderMoney($ladders,$_GPC['total']);
                    //$option['marketprice'] = $laddermoney > 0 ? $laddermoney : $option['marketprice'];
                }
            }
        }
    }
    $marketprice = $laddermoney > 0 ? $laddermoney : $_GPC['marketprice'];

    return show_json(1, array('marketprice' => $marketprice));
}

$yunbi_plugin   = p('yunbi');
if ($yunbi_plugin) {
    $yunbiset = $yunbi_plugin->getSet();
}

if ($_W['isajax']) {
    if (p('recharge')) {
        $telephone =  $_GPC['telephone'];
    }
    $ischannelpick = intval($_GPC['ischannelpick']);
    //$isyunbipay = intval($_GPC['isyunbipay']);
    if ($operation == 'display') {
        $id   = intval($_GPC["id"]);
        if (strpos($_GPC['optionid'], '|')) {
            $optionid = rtrim($_GPC['optionid'], '|');
            $optionid = explode('|', $optionid);
        } else {
            $optionid = intval($_GPC['optionid']);
        }
        if (strpos($_GPC['total'], '|')) {
            $total    = rtrim($_GPC['total'], '|');
            $total = explode('|', $total);
        } else {
            $total    = intval($_GPC['total']);
        }

        $ischannelpay = intval($_GPC['ischannelpay']);
        $ids      = '';
        if ($total < 1) {
            $total = 1;
        }

        if (is_array($total)) {
            $buytotal  = 1;
        } else {
            $buytotal  = $total;
        }

        $isverify  = false;
        $isvirtual = false;
        $changenum = false;
        $goods     = array();

        if (empty($id)) {   //购物车,否则是直接购买的
            $condition = '';
            //check var. cart store in db.
            $cartids   = $_GPC['cartids'];
            if (!empty($cartids)) {
                $condition = ' and c.id in (' . $cartids . ')';
            }

            $suppliers = pdo_fetchall('SELECT distinct g.supplier_uid FROM ' . tablename('sz_yi_member_cart') . ' c ' . ' left join ' . tablename('sz_yi_goods') . ' g on c.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on c.optionid = o.id ' . " where c.openid=:openid and  c.deleted=0 and c.uniacid=:uniacid {$condition} order by g.supplier_uid asc", array(
                ':uniacid' => $uniacid,
                ':openid' => $openid
            ), 'supplier_uid');


            $sql   = 'SELECT c.goodsid, c.total, g.maxbuy, g.type, g.issendfree, g.isnodiscount, g.weight, o.weight as optionweight, g.title, g.thumb, ifnull(o.marketprice, g.marketprice) as marketprice, o.title as optiontitle,c.optionid,g.storeids,g.isverify,g.isverifysend,g.dispatchsend, g.deduct,g.deduct2, g.virtual, o.virtual as optionvirtual, discounts, discounts2, discounttype, discountway, g.supplier_uid, g.dispatchprice, g.dispatchtype, g.dispatchid, g.yunbi_deduct, g.isforceyunbi, o.option_ladders, g.plugin FROM ' . tablename('sz_yi_member_cart') . ' c ' . ' left join ' . tablename('sz_yi_goods') . ' g on c.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on c.optionid = o.id ' . " where c.openid=:openid and  c.deleted=0 and c.uniacid=:uniacid {$condition} order by g.supplier_uid asc";

            $goods = pdo_fetchall($sql, array(
                ':uniacid' => $uniacid,
                ':openid' => $openid
            ));
            if (empty($goods)) {
                return show_json(-1, array(
                    'url' => $this->createMobileUrl('shop/cart')
                ));
            } else {
                foreach ($goods as $k => $v) {
                    if (!empty($v["optionvirtual"])) {
                        $goods[$k]["virtual"] = $v["optionvirtual"];
                    }
                    if (!empty($v["optionweight"])) {
                        $goods[$k]["weight"] = $v["optionweight"];
                    }
                    //阶梯价格
                    if ($isladder) {
                        if ($v['option_ladders']) {
                            $ladders = unserialize($v['option_ladders']);
                        }else{
                            $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                                ':id' => $v['goodsid']
                            ));
                            $ladders = unserialize($ladders['ladders']);
                        }
                        
                        if ($ladders) {
                            $laddermoney = m('goods')->getLaderMoney($ladders,$v['total']);
                            $goods[$k]['marketprice'] = $laddermoney > 0 ? $laddermoney : $v['marketprice'];
                        }
                    } 
                }
            }
            $fromcart = 1;
        } else {
            if(p('hotel')){
                $sql = "SELECT id as goodsid,type,title,weight,deposit,issendfree,isnodiscount, thumb,marketprice,storeids,isverify,isverifysend,dispatchsend,deduct,virtual,maxbuy,usermaxbuy,discounts,discounts2,discounttype,discountway,total as stock, deduct2, ednum, edmoney, edareas, diyformtype, diyformid, diymode, dispatchtype, dispatchid, dispatchprice, supplier_uid, yunbi_deduct, plugin FROM " . tablename("sz_yi_goods") . " where id=:id and uniacid=:uniacid  limit 1";
            }else{
                $sql = "SELECT id as goodsid,type,title,weight,issendfree,isnodiscount, thumb,marketprice,storeids,isverify,isverifysend,dispatchsend,deduct,virtual,maxbuy,usermaxbuy,discounts,discounts2,discounttype,discountway,total as stock, deduct2, ednum, edmoney, edareas, diyformtype, diyformid, diymode, dispatchtype, dispatchid, dispatchprice, supplier_uid, yunbi_deduct, plugin FROM " . tablename("sz_yi_goods") . " where id=:id and uniacid=:uniacid  limit 1";
            }
            $data = pdo_fetch($sql, array(
                ':uniacid' => $uniacid,
                ':id' => $id
            ));
            //阶梯价格
            if ($isladder) {
                $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                        ':id' => $data['goodsid']
                    ));
                if ($ladders) {
                    $ladders = unserialize($ladders['ladders']);
                    $laddermoney = m('goods')->getLaderMoney($ladders,$total);
                    $data['marketprice'] = $laddermoney > 0 ? $laddermoney : $data['marketprice'];
                }
            }
            $suppliers = array($data['supplier_uid'] => array("supplier_uid" => $data['supplier_uid']));

            //新规格
            if (is_int($total) && is_int($optionid)) {
                $data['total']    = $total;
                $data['optionid'] = $optionid;
                if (!empty($optionid)) {
                    $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,virtual,stock,weight,option_ladders from ' . tablename('sz_yi_goods_option') . ' WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1', array(
                        ':uniacid' => $uniacid,
                        ':goodsid' => $id,
                        ':id' => $optionid
                    ));
                    //阶梯价格
                    if ($isladder) {
                        $ladders = unserialize($option['option_ladders']);
                        if ($ladders) {
                            $laddermoney = m('goods')->getLaderMoney($ladders,$total);
                            $option['marketprice'] = $laddermoney > 0 ? $laddermoney : $option['marketprice'];
                        }
                    }
                    if (!empty($option)) {
                        $data['optionid']    = $optionid;
                        $data['optiontitle'] = $option['title'];
                        if (p('supplier')) {
                            if ($option['marketprice'] != 0) {
                                $data['marketprice'] = $option['marketprice'];
                            }
                        } else {
                            $data['marketprice'] = $option['marketprice'];
                        }
                        $data['virtual']     = $option['virtual'];
                        $data['stock']       = $option['stock'];
                        if (!empty($option['weight'])) {
                            $data['weight'] = $option['weight'];
                        }
                    }
                }
                $changenum   = true;
                $totalmaxbuy = $data['stock'];
                if ($data['maxbuy'] > 0) {
                    if ($totalmaxbuy != -1) {
                        if ($totalmaxbuy > $data['maxbuy']) {
                            $totalmaxbuy = $data['maxbuy'];
                        }
                    } else {
                        $totalmaxbuy = $data['maxbuy'];
                    }
                }
                if ($data['usermaxbuy'] > 0) {
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' WHERE og.goodsid=:goodsid AND  o.status>=1 AND o.openid=:openid  AND og.uniacid=:uniacid ', array(
                        ':goodsid' => $data['goodsid'],
                        ':uniacid' => $uniacid,
                        ':openid' => $openid
                    ));
                    $last = $data['usermaxbuy'] - $order_goodscount;
                    if ($last <= 0) {
                        $last = 0;
                    }
                    if ($totalmaxbuy != -1) {
                        if ($totalmaxbuy > $last) {
                            $totalmaxbuy = $last;
                        }
                    } else {
                        $totalmaxbuy = $last;
                    }
                }
                $data['totalmaxbuy'] = $totalmaxbuy;
                if (p('hotel')) {
                    if ($data['type']=='99') {
                        $btime =  $_SESSION['data']['btime'];
                        $bdate =  $_SESSION['data']['bdate'];
                        // 住几天
                        $days = intval($_SESSION['data']['day']);
                        // 离店
                        $etime =  $_SESSION['data']['etime'];
                        $edate =  $_SESSION['data']['edate'] ;
                        $date_array = array();
                        $date_array[0]['date'] = $bdate;
                        $date_array[0]['day'] = date('j', $btime);
                        $date_array[0]['time'] = $btime;
                        $date_array[0]['month'] = date('m', $btime);

                    if ($days > 1) {
                        for ($i = 1; $i < $days; $i++) {
                            $date_array[$i]['time'] = $date_array[$i-1]['time'] + 86400;
                            $date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
                            $date_array[$i]['day'] = date('j', $date_array[$i]['time']);
                            $date_array[$i]['month'] = date('m', $date_array[$i]['time']);
                        }
                    }
                    $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                    $params2 = array(':goodsid' => $id);
                    $room = pdo_fetch($sql2, $params2);

                    $sql = 'SELECT `id`, `roomdate`, `num`, `status` FROM ' . tablename('sz_yi_hotel_room_price') . ' WHERE `roomid` = :roomid
                    AND `roomdate` >= :btime AND `roomdate` < :etime AND `status` = :status';

                        $params = array(':roomid' => $room['id'], ':btime' => $btime, ':etime' => $etime, ':status' => '1');
                        $room_date_list = pdo_fetchall($sql, $params);
                        $flag = intval($room_date_list);
                        $list = array();
                        $max_room = 5;//最大预约房间数
                        $is_order = 1;
                        if ($flag == 1) {
                            for ($i = 0; $i < $days; $i++) {
                                $k = $date_array[$i]['time'];
                                foreach ($room_date_list as $p_key => $p_value) {
                                    // 判断价格表中是否有当天的数据
                                    if ($p_value['roomdate'] == $k) {
                                        $room_num = $p_value['num'];
                                        if (empty($room_num)) {
                                            $is_order = 0;
                                            $max_room = 0;
                                            $list['num'] = 0;
                                            $list['date'] =  $date_array[$i]['date'];
                                        } else if ($room_num > 0 && $room_num < $max_room) {
                                            $max_room = $room_num;
                                            $list['num'] =  $room_num;
                                            $list['date'] =  $date_array[$i]['date'];
                                        } else {
                                            $list['num'] =  $max_room;
                                            $list['date'] =  $date_array[$i]['date'];
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                        $data['totalmaxbuy']= $list['num'];
                    }
                }
                $goods[] = $data;
            } else {
                if (count($total) != count($optionid)) {
                    return show_json(0);
                }
                foreach ($optionid as $key => $val) {
                    $data['total']    = $total[$key];
                    $data['optionid'] = $optionid[$key];
                    if (!empty($data['optionid'])) {
                        $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,virtual,stock,weight from ' . tablename('sz_yi_goods_option') . ' WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1', array(
                            ':uniacid' => $uniacid,
                            ':goodsid' => $id,
                            ':id' => $data['optionid']
                        ));
                        if (!empty($option)) {
                            $data['optionid']    = $data['optionid'];
                            $data['optiontitle'] = $option['title'];
                            if (p('supplier')) {
                                if ($option['marketprice'] != 0) {
                                    $data['marketprice'] = $option['marketprice'];
                                }
                            } else {
                                $data['marketprice'] = $option['marketprice'];
                            }
                            $data['virtual']     = $option['virtual'];
                            $data['stock']       = $option['stock'];
                            if (!empty($option['weight'])) {
                                $data['weight'] = $option['weight'];
                            }
                        }
                    }
                    $changenum   = true;
                    $totalmaxbuy = $data['stock'];
                    if ($data['maxbuy'] > 0) {
                        if ($totalmaxbuy != -1) {
                            if ($totalmaxbuy > $data['maxbuy']) {
                                $totalmaxbuy = $data['maxbuy'];
                            }
                        } else {
                            $totalmaxbuy = $data['maxbuy'];
                        }
                    }
                    if ($data['usermaxbuy'] > 0) {
                        $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' WHERE og.goodsid=:goodsid AND  o.status>=1 AND o.openid=:openid  AND og.uniacid=:uniacid ', array(
                            ':goodsid' => $data['goodsid'],
                            ':uniacid' => $uniacid,
                            ':openid' => $openid
                        ));
                        $last = $data['usermaxbuy'] - $order_goodscount;
                        if ($last <= 0) {
                            $last = 0;
                        }
                        if ($totalmaxbuy != -1) {
                            if ($totalmaxbuy > $last) {
                                $totalmaxbuy = $last;
                            }
                        } else {
                            $totalmaxbuy = $last;
                        }
                    }

                    $data['totalmaxbuy'] = $totalmaxbuy;

                    $goods[$key] = $data;
                }
            }


        }

        $goods = set_medias($goods, 'thumb');

        foreach ($goods as &$g) {
            if ($g['isverify'] == 2) {
                $isverify = true;
            }
            if ($g['isverifysend'] == 1) {
                $isverifysend = true;
            }
            if ($g['dispatchsend'] == 1) {
                $dispatchsend = true;
            }
            if (!empty($g['virtual']) || $g['type'] == 2) {
                $isvirtual = true;
            }
            if (p('channel')) {
                if ($ischannelpay == 1 && empty($ischannelpick)) {
                    $isvirtual = true;
                }
            }
            /*if (p('yunbi')) {
                if (!empty($isyunbipay) && !empty($yunbiset['isdeduct'])) {
                    $g['marketprice'] -= $g['yunbi_deduct'];
                }
            }*/

            if($g['plugin'] == 'fund'){
                $issale = false;
                $hascouponplugin = false;
                $g['url'] = $this->createPluginMobileUrl('fund/detail', array('id' => $g['goodsid']));
            }else{
                $g['url'] = $this->createMobileUrl('shop/detail', array('id' => $g['goodsid']));
            }

        }

        //多店值分开初始化
        foreach ($suppliers as $key => $val) {
            $order_all[$val['supplier_uid']]['weight']         = 0;
            $order_all[$val['supplier_uid']]['total']          = 0;
            $order_all[$val['supplier_uid']]['goodsprice']     = 0;
            $order_all[$val['supplier_uid']]['realprice']      = 0;
            $order_all[$val['supplier_uid']]['deductprice']    = 0;
            $order_all[$val['supplier_uid']]['yunbideductprice']= 0;
            $order_all[$val['supplier_uid']]['discountprice']  = 0;
            $order_all[$val['supplier_uid']]['deductprice2']   = 0;
            $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
            $order_all[$val['supplier_uid']]['storeids']       = array();
            $order_all[$val['supplier_uid']]['dispatch_array'] = array();
            $order_all[$val['supplier_uid']]['supplier_uid'] = $val['supplier_uid'];
            if ($val['supplier_uid']==0) {
                $order_all[$val['supplier_uid']]['supplier_name'] = $shopset['name'];
            } else {
                $supplier_names = pdo_fetch('select username, brandname from ' . tablename('sz_yi_perm_user') . ' where uid='. $val['supplier_uid'] . " and uniacid=" . $_W['uniacid']);
                if (!empty($supplier_names)) {
                    $order_all[$val['supplier_uid']]['supplier_name'] = $supplier_names['brandname'] ? $supplier_names['brandname'] : "";
                } else {
                    $order_all[$val['supplier_uid']]['supplier_name'] = '';
                }
            }
        }
        $member        = m('member')->getMember($openid);
        $level         = m("member")->getLevel($openid);
        //$weight         = 0;
        //$total          = 0;
        //$goodsprice     = 0;
        //$realprice      = 0;
        //$deductprice    = 0;
        //$discountprice  = 0;
        //$deductprice2   = 0;
        $stores        = array();
        $stores_send   = array();
        $address       = false;
        $carrier       = false;
        $carrier_list  = array();
        $dispatch_list = false;

        //$dispatch_price = 0;
        //$dispatch_array = array();

        //$carrier_list = pdo_fetchall("select * from " . tablename("sz_yi_store") . " where  uniacid=:uniacid and status=1 and type in(1,3)", array(
        $carrier_list = pdo_fetchall("select * from " . tablename("sz_yi_store") . " where  uniacid=:uniacid and status=1 AND myself_support=1 ", array(
            ":uniacid" => $_W["uniacid"]
        ));

        if (!empty($carrier_list)) {
            $carrier = $carrier_list[0];
        }
        if (p('channel')) {
            $my_info = p('channel')->getInfo($openid);
        }

        foreach ($goods as &$g) {
            if (empty($g["total"]) || intval($g["total"]) == "-1") {
                $g["total"] = 1;
            }
            if (p('channel')) {
                if ($ischannelpay == 1) {
                    $g['marketprice'] = $g['marketprice'] * $my_info['my_level']['purchase_discount']/100;
                }
            }
            $gprice    = $g["marketprice"] * $g["total"];

            $discounts = json_decode($g["discounts"], true);

            $discountway = $g['discountway'];
            $discounttype = $g['discounttype'];
            if ($discountway == 1) {
                //折扣
                if ($g["discounttype"] == 1) {
                    //会员等级折扣
                    $level          = m("member")->getLevel($openid);
                    $discounts = json_decode($g["discounts"], true);
                    if (is_array($discounts)) {
                        if (!empty($level["id"])) {
                            if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                                $level["discount"] = floatval($discounts["level" . $level["id"]]);
                            } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                                $level["discount"] = floatval($level["discount"]);
                            } else {
                                $level["discount"] = 0;
                            }
                        } else {
                            if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                                $level["discount"] = floatval($discounts["default"]);
                            } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                                $level["discount"] = floatval($level["discount"]);
                            } else {
                                $level["discount"] = 0;
                            }
                        }
                    }
                } else {
                    //分销商等级折扣
                    $level     = p("commission")->getLevel($openid);
                    $discounts = json_decode($g['discounts2'], true);
                    //是分销商
                    $level["discount"] = 0;
                    if ($member['isagent'] == 1 && $member['status'] == 1) {
                        if (is_array($discounts)) {
                            if (!empty($level["id"])) {
                                if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                                    $level["discount"] = floatval($discounts["level" . $level["id"]]);
                                }
                            } else {
                                if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                                    $level["discount"] = floatval($discounts["default"]);
                                }
                            }
                        }
                    }
                }
                if (p('channel') && $ischannelpay == 1) {
                    $level["discount"] = 10;
                }
                if (empty($g["isnodiscount"]) && floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                    $price = round(floatval($level["discount"]) / 10 * $gprice, 2);
                    $order_all[$g['supplier_uid']]['discountprice'] += $gprice - $price;
                } else {
                    $price = $gprice;
                }
            } else {
                //立减
                if ($g["discounttype"] == 1) {
                    //会员等级立减
                    $level = m("member")->getLevel($openid);
                    $level['discount'] = 0;
                    $discounts = json_decode($g["discounts"], true);
                    if (is_array($discounts)) {
                        if (!empty($level["id"])) {
                            if (floatval($discounts["level" . $level["id"]]) < $g['marketprice']) {
                                $level["discount"] = floatval($discounts["level" . $level["id"]]);
                            } elseif (floatval($level["discount"]) < $g['marketprice']) {
                                $level["discount"] = floatval($level["discount"]);
                            }
                        } else {
                            if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < $g['marketprice']) {
                                $level["discount"] = floatval($discounts["default"]);
                            } elseif (floatval($level["discount"]) > 0 && floatval($level["discount"]) < $g['marketprice']) {
                                $level["discount"] = floatval($level["discount"]);
                            }
                        }
                    }
                } else {
                    //分销商等级立减
                    $level     = p("commission")->getLevel($openid);
                    $discounts = json_decode($g['discounts2'], true);
                    //是分销商
                    $level["discount"] = 0;
                    if ($member['isagent'] == 1 && $member['status'] == 1) {
                        if (is_array($discounts)) {
                            if (!empty($level["id"])) {
                                if (floatval($discounts["level" . $level["id"]]) < $g['marketprice']) {
                                    $level["discount"] = floatval($discounts["level" . $level["id"]]);
                                }
                            } else {
                                if (floatval($discounts["default"]) < $g['marketprice']) {
                                    $level["discount"] = floatval($discounts["default"]);
                                }
                            }
                        }
                    }
                }

                if (empty($g["isnodiscount"]) && floatval($level["discount"]) < $g['marketprice']) {
                    $price = round(floatval($gprice - $level["discount"] * $g["total"]), 2);
                    $order_all[$g['supplier_uid']]['discountprice'] += $gprice - $price;
                } else {
                    $price = $gprice;
                }
                if (p('channel') && $ischannelpay == 1) {
                    $price = $gprice;
                }
            }

            $g["discount"] = $level["discount"];
            $g["ggprice"] = $price;

            $order_all[$g['supplier_uid']]['realprice'] += $price;
            $order_all[$g['supplier_uid']]['goodsprice'] += $gprice;
            //商品为酒店时候的价格
            if(p('hotel') && $data['type']=='99'){
                $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                $params2 = array(':goodsid' => $id);
                $room = pdo_fetch($sql2, $params2);
                $pricefield ='oprice';
                $r_sql = 'SELECT `roomdate`, `num`, `oprice`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('sz_yi_hotel_room_price') .
                    ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                    ' `roomdate` < :etime';
                $params = array(':roomid' => $room['id'],':btime' => $btime, ':etime' => $etime);
                $price_list = pdo_fetchall($r_sql, $params);
                $this_price = $old_price =  $pricefield == 'cprice' ?  $room['oprice']*$member_p[$_W['member']['groupid']] : $room['roomprice'];
                if ($this_price == 0) {
                    $this_price = $old_price = $room['oprice'] ;
                }
                $totalprice =  $old_price * $days;
                //价格表中存在
                if ($price_list) {
                    $check_date = array();
                    foreach ($price_list as $k => $v) {
                        $price_list[$k]['time']=date('Y-m-d', $v['roomdate']);

                        $new_price = $pricefield == 'mprice' ? $this_price : $v['m_price'];
                        $roomdate = $v['roomdate'];
                        if ($v['status'] == 0 || $v['num'] == 0 ) {
                            $has = 0;
                        } else {
                            if ($new_price && $roomdate) {
                                if (!in_array($roomdate, $check_date)) {
                                    $check_date[] = $roomdate;
                                    if ($old_price != $new_price) {
                                        $totalprice = $totalprice - $old_price + $new_price;
                                    }
                                }
                            }
                        }
                    }
                    $goodsprice = round($totalprice);

                }else{
                    $goodsprice = round($goods[0]['marketprice']) * $days;
                }
                $order_all[$g['supplier_uid']]['realprice'] = $goodsprice;
                $order_all[$g['supplier_uid']]['goodsprice'] = $goodsprice;
                $price = $goodsprice;
            }
            $order_all[$g['supplier_uid']]['total'] += $g["total"];
            $order_all[$g['supplier_uid']]['deductprice'] += $g["deduct"] * $g["total"];
            //虚拟币抵扣
            if ($g["yunbi_deduct"]) {
                $order_all[$g['supplier_uid']]['yunbideductprice'] += $g["yunbi_deduct"] * $g["total"];
            } else {
                $order_all[$g['supplier_uid']]['yunbideductprice'] += $g["yunbi_deduct"];
            }
            if ($g["deduct2"] == 0) {
                $order_all[$g['supplier_uid']]['deductprice2'] += $price;
            } elseif ($g["deduct2"] > 0) {
                if ($g["deduct2"] > $price) {
                    $order_all[$g['supplier_uid']]['deductprice2'] += $price;
                } else {
                    $order_all[$g['supplier_uid']]['deductprice2'] += $g["deduct2"];
                }
            }
            $order_all[$g['supplier_uid']]['goods'][] = $g;
        }
        unset($g);
        //核销
        if ($isverify) {
            $storeids = array();
            foreach ($goods as $g) {
                if (!empty($g['storeids'])) {
                    $order_all[$g['supplier_uid']]['storeids'] = array_merge(explode(',', $g['storeids']), $order_all[$g['supplier_uid']]['storeids']);
                }
            }

            foreach ($suppliers as $key => $val) {
                if (empty($order_all[$val['supplier_uid']]['storeids'])) {
                    $order_all[$val['supplier_uid']]['stores'] = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1 and myself_support=1', array(
                        ':uniacid' => $_W['uniacid']
                    ));
                } else {
                    $order_all[$val['supplier_uid']]['stores'] = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $order_all[$val['supplier_uid']]['storeids']) . ') and uniacid=:uniacid and status=1 and myself_support=1', array(
                        ':uniacid' => $_W['uniacid']
                    ));
                }
                if (empty($order_all[$val['supplier_uid']]['storeids'])) {
                    $order_all[$val['supplier_uid']]['stores_send'] = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1 ', array(
                        ':uniacid' => $_W['uniacid']
                    ));
                } else {
                    $order_all[$val['supplier_uid']]['stores_send'] = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $order_all[$val['supplier_uid']]['storeids']) . ') and uniacid=:uniacid and status=1 ', array(
                        ':uniacid' => $_W['uniacid']
                    ));
                }
                $stores = $order_all[$val['supplier_uid']]['stores'];
                $stores_send = $order_all[$val['supplier_uid']]['stores_send'];
            }
            //是否开启街道联动
            if ($trade['is_street'] == '1') {
                $address      = pdo_fetch('select id,realname,mobile,address,province,city,area,street from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
            } else {
                $address      = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
            }

        } else {
            //是否开启街道联动
            if ($trade['is_street'] == '1') {
                $address      = pdo_fetch('select id,realname,mobile,address,province,city,area,street from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
            } else {
                $address      = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
            }
        }

        //如果开启核销并且不支持配送，则没有运费
        $isDispath = true;
        if ($isverify && !$isverifysend && !$dispatchsend) {
            $isDispath = false;
        }

        if (!$isvirtual && $isDispath) {
            //购买的商品是否都是统一运费的,如果是,取最低统一运费价
            foreach ($goods as $g) {
                $sendfree = false;
                if (!empty($g["issendfree"])) { //包邮
                    $sendfree = true;
                } else {
                    $gareas = explode(";", $g["edareas"]);  //不参加包邮地区
                    if ($g["total"] >= $g["ednum"] && $g["ednum"] > 0) {    //单品满xx件包邮

                        if (empty($gareas)) {
                            $sendfree = true;
                        } else {
                            if (!empty($address)) {
                                if (!in_array($address["city"], $gareas)) {
                                    $sendfree = true;
                                }
                            } else if (!empty($member["city"])) {
                                if (!in_array($member["city"], $gareas)) {
                                    $sendfree = true;
                                }
                            } else {
                                $sendfree = true;
                            }
                        }
                    }

                    if ($g["ggprice"] >= floatval($g["edmoney"]) && floatval($g["edmoney"]) > 0) {  //满额包邮
                        if (empty($gareas)) {
                            $sendfree = true;
                        } else {
                            if (!empty($address)) {
                                if (!in_array($address["city"], $gareas)) {
                                    $sendfree = true;
                                }
                            } else if (!empty($member["city"])) {
                                if (!in_array($member["city"], $gareas)) {
                                    $sendfree = true;
                                }
                            } else {
                                $sendfree = true;
                            }
                        }
                    }
                }

                if (!$sendfree) {   //计算运费
                    if ($g["dispatchtype"] == 1) {  //统一邮费
                        if ($g["dispatchprice"] > 0) {
                            //$order_all[$g['supplier_uid']]['dispatch_price'] += $g["dispatchprice"] * $g["total"];
                            //$order_all[$g['supplier_uid']]['dispatch_price'] += $g["dispatchprice"];
                            //改为统一运费同一个商品只收取一次运费
                            if (!isset($order_all[$g['supplier_uid']]['minDispathPrice'])) {
                                $order_all[$g['supplier_uid']]['minDispathPrice'] = $g["dispatchprice"];
                            }
                            $order_all[$g['supplier_uid']]['dispatch_price'] = ($order_all[$g['supplier_uid']]['minDispathPrice'] > $g["dispatchprice"]) ? $g["dispatchprice"] : $order_all[$g['supplier_uid']]['minDispathPrice'];
                        }
                    } else if ($g["dispatchtype"] == 0) {   //运费模板
                        //$order_all[$g['supplier_uid']]['isAllSameDispath'] = false;
                        if (empty($g["dispatchid"])) {
                            $order_all[$g['supplier_uid']]['dispatch_data'] = m("order")->getDefaultDispatch($g['supplier_uid']);
                        } else {
                            $order_all[$g['supplier_uid']]['dispatch_data'] = m("order")->getOneDispatch($g["dispatchid"], $g['supplier_uid']);
                        }
                        if (empty($order_all[$g['supplier_uid']]['dispatch_data'])) {
                            $order_all[$g['supplier_uid']]['dispatch_data'] = m("order")->getNewDispatch($g['supplier_uid']);
                        }
                        if (!empty($order_all[$g['supplier_uid']]['dispatch_data'])) {
                            if ($order_all[$g['supplier_uid']]['dispatch_data']["calculatetype"] == 1) {
                                $order_all[$g['supplier_uid']]['param'] = $g["total"];
                            } else {
                                $order_all[$g['supplier_uid']]['param'] = $g["weight"] * $g["total"];
                            }
                            $dkey = $order_all[$g['supplier_uid']]['dispatch_data']["id"];
                            if (array_key_exists($dkey, $order_all[$g['supplier_uid']]['dispatch_array'])) {
                                $order_all[$g['supplier_uid']]['dispatch_array'][$dkey]["param"] += $order_all[$g['supplier_uid']]['param'];
                            } else {
                                $order_all[$g['supplier_uid']]['dispatch_array'][$dkey]["data"]  = $order_all[$g['supplier_uid']]['dispatch_data'];
                                $order_all[$g['supplier_uid']]['dispatch_array'][$dkey]["param"] = $order_all[$g['supplier_uid']]['param'];
                            }
                        }
                    }
                }
            }

            foreach ($suppliers as $key => $val) {
                if (!empty($order_all[$val['supplier_uid']]['dispatch_array'])) {
                    foreach ($order_all[$val['supplier_uid']]['dispatch_array'] as $k => $v) {
                        $order_all[$val['supplier_uid']]['dispatch_data'] = $order_all[$val['supplier_uid']]['dispatch_array'][$k]["data"];
                        $param         = $order_all[$val['supplier_uid']]['dispatch_array'][$k]["param"];
                        $areas         = unserialize($order_all[$val['supplier_uid']]['dispatch_data']["areas"]);
                        if (!empty($address)) {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getCityDispatchPrice($areas, $address["city"], $param, $order_all[$val['supplier_uid']]['dispatch_data'], $val['supplier_uid']);
                        } else if (!empty($member["city"])) {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getCityDispatchPrice($areas, $member["city"], $param, $order_all[$val['supplier_uid']]['dispatch_data'], $val['supplier_uid']);
                        } else {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getDispatchPrice($param, $order_all[$val['supplier_uid']]['dispatch_data'], -1, $val['supplier_uid']);
                        }
                    }
                }
            }
        }

        $sale_plugin   = p('sale');
        $saleset       = false;

        if ($sale_plugin && $issale) {
            $saleset = $sale_plugin->getSet();
            $saleset["enoughs"] = $sale_plugin->getEnoughs();
        }


        //订单总价
        $realprice_total = 0;
        foreach ($suppliers as $key => $val) {
            if ($saleset) {
                //满额包邮
                if (!empty($saleset["enoughfree"])) {
                    if (floatval($saleset["enoughorder"]) <= 0) {
                        $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
                    } else {
                        if ($order_all[$val['supplier_uid']]['realprice'] >= floatval($saleset["enoughorder"])) {
                            if (empty($saleset["enoughareas"])) {
                                $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
                            } else {
                                $areas = explode(";", $saleset["enoughareas"]);
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $areas)) {
                                        $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
                if(p('hotel') &&  $data['type']=='99'){
                    $order_all[$val['supplier_uid']]['dispatch_price']  = 0;
                }
                $order_all[$val['supplier_uid']]['saleset'] = $saleset;
                if (p('channel') && $ischannelpay == 1) {
                    $saleset = array();
                }
                if (!empty($saleset["enoughs"])) {
                    //取满额条件值最大的1个条件
                    $tmp_money = 0;

                    foreach ($saleset["enoughs"] as $e) {
                        if ($order_all[$val['supplier_uid']]['realprice'] >= floatval($e["enough"]) && floatval($e["money"]) > 0) {
                            if ($e["enough"] > $tmp_money) {
                                $tmp_money = $e["enough"];

                                $order_all[$val['supplier_uid']]['saleset']["showenough"]   = true;
                                $order_all[$val['supplier_uid']]['saleset']["enoughmoney"]  = $e["enough"];
                                $order_all[$val['supplier_uid']]['saleset']["enoughdeduct"] = number_format($e["money"], 2);
                                $final_money = $e["money"];

                                //确定匹配的满额条件,页面显示
                                $saleset['enoughmoney'] = $e["enough"];
                                $saleset['enoughdeduct'] = number_format($e["money"], 2);
                            }
                        }
                    }

                    $order_all[$val['supplier_uid']]['realprice'] -= floatval($final_money);
                }

                if (empty($saleset["dispatchnodeduct"])) {
                    $order_all[$val['supplier_uid']]['deductprice2'] += $order_all[$val['supplier_uid']]['dispatch_price'];
                }
            }
            $order_all[$val['supplier_uid']]['hascoupon'] = false;
            if ($hascouponplugin) {
                $order_all[$val['supplier_uid']]['couponcount'] = $plugc->consumeCouponCount($openid, $order_all[$val['supplier_uid']]['goodsprice'], $val['supplier_uid'], 0, 0, $goodid, $cartid);
                $order_all[$val['supplier_uid']]['hascoupon']   = $order_all[$val['supplier_uid']]['couponcount'] > 0;
            }
            if ($hascard) {
                $order_all[$val['supplier_uid']]['cardcount'] = $plugincard->consumeCardCount($openid);
            }
            $order_all[$val['supplier_uid']]['realprice'] += $order_all[$val['supplier_uid']]['dispatch_price'];
            $realprice_total += $order_all[$val['supplier_uid']]['realprice'];
            $order_all[$val['supplier_uid']]['deductcredit']  = 0;
            $order_all[$val['supplier_uid']]['deductmoney']   = 0;
            $order_all[$val['supplier_uid']]['deductcredit2'] = 0;
            if ($sale_plugin) {
                $credit = m('member')->getCredit($openid, 'credit1');
                if (!empty($saleset['creditdeduct'])) {
                    $pcredit = intval($saleset['credit']);
                    $pmoney  = round(floatval($saleset['money']), 2);
                    if ($pcredit > 0 && $pmoney > 0) {
                        if ($credit % $pcredit == 0) {
                            $order_all[$val['supplier_uid']]['deductmoney'] = round(intval($credit / $pcredit) * $pmoney, 2);
                        } else {
                            $order_all[$val['supplier_uid']]['deductmoney'] = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                        }
                    }
                    if ($order_all[$val['supplier_uid']]['deductmoney'] >$order_all[$val['supplier_uid']]['deductprice']) {
                        $order_all[$val['supplier_uid']]['deductmoney'] = $order_all[$val['supplier_uid']]['deductprice'];
                    }
                    if ($order_all[$val['supplier_uid']]['deductmoney'] > $order_all[$val['supplier_uid']]['realprice']) {
                        $order_all[$val['supplier_uid']]['deductmoney'] = $order_all[$val['supplier_uid']]['realprice'];
                    }
                    $order_all[$val['supplier_uid']]['deductcredit'] = $order_all[$val['supplier_uid']]['deductmoney'] / $pmoney * $pcredit;
                }
                if (!empty($saleset['moneydeduct'])) {
                    $order_all[$val['supplier_uid']]['deductcredit2'] = m('member')->getCredit($openid, 'credit2');
                    if ($order_all[$val['supplier_uid']]['deductcredit2'] > $order_all[$val['supplier_uid']]['realprice']) {
                        $order_all[$val['supplier_uid']]['deductcredit2'] = $order_all[$val['supplier_uid']]['realprice'];
                    }
                    if ($order_all[$val['supplier_uid']]['deductcredit2'] > $order_all[$val['supplier_uid']]['deductprice2']) {
                        $order_all[$val['supplier_uid']]['deductcredit2'] = $order_all[$val['supplier_uid']]['deductprice2'];
                    }
                }
            }

            //虚拟币抵扣
            $order_all[$val['supplier_uid']]['deductyunbi'] = 0;
            $order_all[$val['supplier_uid']]['deductyunbimoney'] = 0;
            if ($yunbi_plugin && $yunbiset['isdeduct']) {
                $virtual_currency = $member['virtual_currency'];//m('member')->getCredit($openid, 'virtual_currency');
                $ycredit = 1;
                $ymoney  = round(floatval($yunbiset['money']), 2);
                if ($ycredit > 0 && $ymoney > 0) {
                    if ($virtual_currency % $ycredit == 0) {
                        $order_all[$val['supplier_uid']]['deductyunbimoney'] = round(intval($virtual_currency / $ycredit) * $ymoney, 2);
                    } else {
                        $order_all[$val['supplier_uid']]['deductyunbimoney'] = round((intval($virtual_currency / $ycredit) + 1) * $ymoney, 2);
                    }
                }
                if ($order_all[$val['supplier_uid']]['deductyunbimoney'] >$order_all[$val['supplier_uid']]['yunbideductprice']) {
                    $order_all[$val['supplier_uid']]['deductyunbimoney'] = $order_all[$val['supplier_uid']]['yunbideductprice'];
                }
                if ($order_all[$val['supplier_uid']]['deductyunbimoney'] > $order_all[$val['supplier_uid']]['realprice']) {
                    $order_all[$val['supplier_uid']]['deductyunbimoney'] = $order_all[$val['supplier_uid']]['realprice'];
                }

                $order_all[$val['supplier_uid']]['deductyunbi'] = $order_all[$val['supplier_uid']]['deductyunbimoney'] / $ymoney * $ycredit;

            }
            $order_all[$val['supplier_uid']]['goodsprice'] = number_format($order_all[$val['supplier_uid']]['goodsprice'], 2);
            $order_all[$val['supplier_uid']]['totalprice'] = number_format($order_all[$val['supplier_uid']]['totalprice'], 2);
            if (p('channel') && $ischannelpay == 1) {
                $order_all[$val['supplier_uid']]['discountprice'] = 0;
            }
            $order_all[$val['supplier_uid']]['discountprice'] = number_format($order_all[$val['supplier_uid']]['discountprice'], 2);
            $order_all[$val['supplier_uid']]['realprice'] = number_format($order_all[$val['supplier_uid']]['realprice'], 2);
            $order_all[$val['supplier_uid']]['dispatch_price'] = number_format($order_all[$val['supplier_uid']]['dispatch_price'], 2);

        }
        $supplierids = implode(',', array_keys($suppliers));
        if(p('hotel')){
            if($data['type']=='99'){
                $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                $params2 = array(':goodsid' => $id);
                $room = pdo_fetch($sql2, $params2);
                $pricefield ='oprice';
                $r_sql = 'SELECT `roomdate`, `num`, `oprice`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('sz_yi_hotel_room_price') .
                    ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                    ' `roomdate` < :etime';
                $btime =  $_SESSION['data']['btime'];
                $etime =  $_SESSION['data']['etime'];
                $params = array(':roomid' => $room['id'],':btime' => $btime, ':etime' => $etime);
                $price_list = pdo_fetchall($r_sql, $params);
                $this_price = $old_price =  $pricefield == 'cprice' ?  $room['oprice']*$member_p[$_W['member']['groupid']] : $room['roomprice'];
                if ($this_price == 0) {
                    $this_price = $old_price = $room['oprice'] ;
                }
                $totalprice =  $old_price * $days;
                if ($price_list) {//价格表中存在
                    $check_date = array();
                    foreach($price_list as $k => $v) {
                        $price_list[$k]['time']=date('Y-m-d',$v['roomdate']);
                        $new_price = $pricefield == 'mprice' ? $this_price : $v['m_price'];
                        $roomdate = $v['roomdate'];
                        if ($v['status'] == 0 || $v['num'] == 0 ) {
                            $has = 0;
                        } else {
                            if ($new_price && $roomdate) {
                                if (!in_array($roomdate, $check_date)) {
                                    $check_date[] = $roomdate;
                                    if ($old_price != $new_price) {
                                        $totalprice = $totalprice - $old_price + $new_price;
                                    }
                                }
                            }
                        }
                    }
                    $goodsprice = round($totalprice);
                }else{
                    $goodsprice = round($goods[0]['marketprice']) * $days;
                }
                $realprice  = $goodsprice+$goods[0]['deposit'];
                $deposit = $goods[0]['deposit'];
                $order_all[$g['supplier_uid']]['realprice'] = $goodsprice;
                $order_all[$g['supplier_uid']]['goodsprice'] = $goodsprice;

            }}
        if (p('recharge') && !empty($telephone)) {
            $member['realname'] = $telephone;
            $member['membermobile'] = $telephone;
            $changenum = false;
        }
        //echo "<pre>".print_r($changenum);exit;
        $variable = array(
            'show'=>$show,
            'diyform_flag'=>$diyform_flag,
            'goods'=>$goods,
        );

        return show_json(1, array(
            'member' => $member,
            //'deductcredit' => $deductcredit,
            'deductmoney' => $deductmoney,
            'deductcredit2' => $deductcredit2,
            'saleset' => $saleset,
            'goods' => $goods,
            'has'=>$has,
            'weight' => $weight / $buytotal,
            'set' => $shopset,
            'fromcart' => $fromcart,
            'haslevel' => !empty($level) && $level['discount'] > 0 && $level['discount'] < 10,
            'total' => $total,
            //"dispatchprice" => number_format($dispatch_price, 2),
            'totalprice' => number_format($totalprice, 2),
            'goodsprice' => number_format($goodsprice, 2),
            'discountprice' => number_format($discountprice, 2),
            'discount' => $level['discount'],
            'realprice_total' => number_format($realprice_total, 2),
            'address' => $address,
            //'carrier' => $carrier,
            //'carrier_list' => $carrier_list,
            'carrier' => $stores[0],
            'carrier_list' => $stores,
            'carrier_send' => $stores_send[0],
            'carrier_list_send' => $stores_send,
            'dispatch_list' => $dispatch_list,
            'isverify' => $isverify,
            'isverifysend' => $isverifysend,
            'dispatchsend' => $dispatchsend,
            'stores' => $stores,
            'stores_send' => $stores_send,
            'isvirtual' => $isvirtual,
            'changenum' => $changenum,
            //'hascoupon' => $hascoupon,
            //'couponcount' => $couponcount,
            'order_all' => $order_all,
            'supplierids' => $supplierids,
            "deposit" => number_format($deposit, 2),
            'price_list' => $price_list,
            'realprice' => number_format($realprice, 2),
            'hascouponplugin' => $hascouponplugin,
            'type'=>$goods[0]['type'],
        ),$variable);
    }
    elseif ($operation == 'getdispatchprice') {
        $isverify       = false;
        $isvirtual      = false;
        $isverifysend   = false;
        $deductprice    = 0;
        $deductprice2   = 0;
        $deductcredit2  = 0;
        $dispatch_array = array();
        $totalprice = floatval($_GPC['totalprice']);
        $dflag          = $_GPC["dflag"];
        $hascoupon      = false;
        $couponcount    = 0;
        $pc             = p("coupon");
        $plucard        = p('card');
        $supplier_uid   = $_GPC["supplier_uid"];
        $coupon_carrierid = intval($_GPC['carrierid']);
        $goodid = $_GPC['id'] ? intval($_GPC['id']) : 0;
        $cartids = $_GPC['cartids'] ? $_GPC['cartids'] : 0;
        $storeid = intval($_GPC['carrierid']);
        $addressid           = intval($_GPC["addressid"]);
        $address     = pdo_fetch('select id,realname,mobile,address,province,city,area,street from ' . tablename('sz_yi_member_address') . ' WHERE  id=:id AND openid=:openid AND uniacid=:uniacid limit 1', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid,
            ':id' => $addressid
        ));
        $member              = m("member")->getMember($openid);
        $level               = m("member")->getLevel($openid);
        $weight              = $_GPC["weight"];
        $dispatch_price      = 0;
        $deductenough_money  = 0;
        $deductenough_enough = 0;
        $sale_plugin = p('sale');
        $saleset     = false;
        if ($sale_plugin) {
            $saleset = $sale_plugin->getSet();
            $saleset["enoughs"] = $sale_plugin->getEnoughs();
        }

        //总价-优惠
        if (empty($g["isnodiscount"]) && floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
            $totalprice = round(floatval($level["discount"]) / 10 * $totalprice, 2);
        }
        if ($pc) {
           $pset = $pc->getSet();
           if (empty($pset["closemember"])) {
               $couponcount = $pc->consumeCouponCount($openid, $totalprice, $supplier_uid, 0, 0, $goodsid, $cartids,$coupon_carrierid);
               $hascoupon   = $couponcount > 0;
           }
        }

        if ($plucard) {
            $card_set   = $plucard->getSet();
            $cardcount  = $plucard->consumeCardCount($openid);
            $hascard    = $cardcount > 0;
        }

        if ($sale_plugin) {
            if ($saleset) {
                foreach ($saleset["enoughs"] as $e) {
                    if ($totalprice >= floatval($e["enough"]) && floatval($e["money"]) > 0 && floatval($e["enough"]) >= $deductenough_enough) {
                        $deductenough_money  = floatval($e["money"]);
                        $deductenough_enough = floatval($e["enough"]);
                    }
                }
                if (!empty($saleset['enoughfree'])) {
                    if (floatval($saleset['enoughorder']) <= 0) {
                        return show_json(1, array(
                            'price' => 0,
                            "hascoupon" => $hascoupon,
                            "couponcount" => $couponcount,
                            "hascard"   => $hascard,
                            "cardcount" => $cardcount,
                            "deductenough_money" => $deductenough_money,
                            "deductenough_enough" => $deductenough_enough,
                            "supplier_uid" => $supplier_uid
                        ));
                    }
                }
                if (!empty($saleset['enoughfree']) && $totalprice >= floatval($saleset['enoughorder'])) {
                    if (!empty($saleset['enoughareas'])) {
                        $areas = explode(";", $saleset['enoughareas']);
                        if (!in_array($address['city'], $areas)) {
                            return show_json(1, array(
                                "price" => 0,
                                "hascoupon" => $hascoupon,
                                "couponcount" => $couponcount,
                                "hascard"   => $hascard,
                                "cardcount" => $cardcount,
                                "deductenough_money" => $deductenough_money,
                                "deductenough_enough" => $deductenough_enough,
                                "supplier_uid" => $supplier_uid
                            ));
                        }
                    } else {
                        return show_json(1, array(
                            "price" => 0,
                            "hascoupon" => $hascoupon,
                            "couponcount" => $couponcoun,
                            "hascard"   => $hascard,
                            "cardcount" => $cardcount,
                            "deductenough_money" => $deductenough_money,
                            "deductenough_enough" => $deductenough_enough,
                            "supplier_uid" => $supplier_uid
                        ));
                    }
                }
            }
        }
        $goods = trim($_GPC["goods"]);
        if (!empty($goods)) {
            $weight   = 0;
            $allgoods = array();
            $goodsarr = explode("|", $goods);
            foreach ($goodsarr as &$g) {
                if (empty($g)) {
                    continue;
                }
                $goodsinfo  = explode(",", $g);
                $goodsid    = !empty($goodsinfo[0]) ? intval($goodsinfo[0]) : '';
                $optionid   = !empty($goodsinfo[1]) ? intval($goodsinfo[1]) : 0;
                $goodstotal = !empty($goodsinfo[2]) ? intval($goodsinfo[2]) : "1";
                if ($goodstotal < 1) {
                    $goodstotal = 1;
                }
                if (empty($goodsid)) {
                    return show_json(1, array(
                        "price" => 0
                    ));
                }
                $sql  = "SELECT id as goodsid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,goodssn,productsn,sales,istime,timestart,timeend,usermaxbuy,maxbuy,unit,buylevels,buygroups,deleted,status,deduct,virtual,discounts,deduct2,ednum,edmoney,edareas,diyformid,diyformtype,diymode,dispatchtype,dispatchid,dispatchprice,yunbi_deduct FROM " . tablename("sz_yi_goods") . " WHERE id=:id AND uniacid=:uniacid  limit 1";
                $data = pdo_fetch($sql, array(
                    ":uniacid" => $uniacid,
                    ":id" => $goodsid
                ));
                if (empty($data)) {
                    return show_json(1, array(
                        "price" => 0
                    ));
                }
                $data["stock"] = $data["total"];
                $data["total"] = $goodstotal;
                if (!empty($optionid)) {
                    $option = pdo_fetch("select id,title,marketprice,goodssn,productsn,stock,virtual,weight from " . tablename("sz_yi_goods_option") . " WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1", array(
                        ":uniacid" => $uniacid,
                        ":goodsid" => $goodsid,
                        ":id" => $optionid
                    ));
                    if (!empty($option)) {
                        $data["optionid"]    = $optionid;
                        $data["optiontitle"] = $option["title"];
                        $data["marketprice"] = $option["marketprice"];
                        if (!empty($option["weight"])) {
                            $data["weight"] = $option["weight"];
                        }
                    }
                }
                $discounts = json_decode($data["discounts"], true);
                if (is_array($discounts)) {
                    if (!empty($level["id"])) {
                        if ($discounts["level" . $level["id"]] > 0 && $discounts["level" . $level["id"]] < 10) {
                            $level["discount"] = $discounts["level" . $level["id"]];
                        } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                            $level["discount"] = floatval($level["discount"]);
                        } else {
                            $level["discount"] = 0;
                        }
                    } else {
                        if ($discounts["default"] > 0 && $discounts["default"] < 10) {
                            $level["discount"] = $discounts["default"];
                        } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                            $level["discount"] = floatval($level["discount"]);
                        } else {
                            $level["discount"] = 0;
                        }
                    }
                }
                $gprice  = $data["marketprice"] * $goodstotal;
                $ggprice = 0;
                if (empty($data["isnodiscount"]) && $level["discount"] > 0 && $level["discount"] < 10) {
                    $dprice = round($gprice * $level["discount"] / 10, 2);
                    $discountprice += $gprice - $dprice;
                    $ggprice = $dprice;
                } else {
                    $ggprice = $gprice;
                }
                $data["ggprice"] = $ggprice;
                $allgoods[]      = $data;
            }
            unset($g);
            foreach ($allgoods as $g) {
                if ($g["isverify"] == 2) {
                    $isverify = true;
                }
                if (!empty($g["virtual"]) || $g["type"] == 2) {
                    $isvirtual = true;
                }
                if ($g['isverifysend'] == 1) {
                    $isverifysend = true;
                }
                $deductprice += $g["deduct"] * $g["total"];

                //虚拟币抵扣
                if ($data["yunbi_deduct"]) {
                    $yunbideductprice += $g["yunbi_deduct"] * $g["total"];
                } else {
                    $yunbideductprice += $g["yunbi_deduct"];
                }

                if ($g["deduct2"] == 0) {
                    $deductprice2 += $g["ggprice"];
                } else if ($g["deduct2"] > 0) {
                    if ($g["deduct2"] > $g["ggprice"]) {
                        $deductprice2 += $g["ggprice"];
                    } else {
                        $deductprice2 += $g["deduct2"];
                    }
                }
                if (p('channel')) {
                    if ($ischannelpay == 1 && empty($ischannelpick)) {
                        $isvirtual = true;
                    }
                }
            }
            //仅判断核销了，还需要判断支持配送
            //如果开启核销并且不支持配送，则没有运费
            $isDispath = true;
            if ($isverify && !$isverifysend) {
                $isDispath = false;
            }

            if ($isverify && $isDispath) {
                return show_json(1, array(
                    "price" => 0,
                    "hascoupon" => $hascoupon,
                    "couponcount" => $couponcount,
                    "hascard"   => $hascard,
                    "cardcount" => $cardcount,
                    "supplier_uid" => $supplier_uid
                ));
            }
            if (!empty($allgoods)) {
                foreach ($allgoods as $g) {

                    $sendfree = false;
                    if (!empty($g["issendfree"])) {
                        $sendfree = true;
                    }
                    if ($g["type"] == 2 || $g["type"] == 3) {
                        $sendfree = true;
                    } else {
                        $gareas = explode(";", $g["edareas"]);
                        if ($g["total"] >= $g["ednum"] && $g["ednum"] > 0) {
                            if (empty($gareas)) {
                                $sendfree = true;
                            } else {
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else if (!empty($member["city"])) {
                                    if (!in_array($member["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else {
                                    $sendfree = true;
                                }
                            }
                        }
                        if ($g["ggprice"] >= floatval($g["edmoney"]) && floatval($g["edmoney"]) > 0) {
                            if (empty($gareas)) {
                                $sendfree = true;
                            } else {
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else if (!empty($member["city"])) {
                                    if (!in_array($member["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else {
                                    $sendfree = true;
                                }
                            }
                        }
                    }
                    if (!$sendfree) {
                        //统一运费
                        if ($g["dispatchtype"] == 1) {
                            if ($g["dispatchprice"] > 0) {
                                //$dispatch_price += $g["dispatchprice"] * $g["total"];
                                //$dispatch_price += $g["dispatchprice"];
                                if (!isset($minDispathPrice)) {
                                    $minDispathPrice = $g["dispatchprice"];
                                }
                                $dispatch_price = ($minDispathPrice > $g["dispatchprice"]) ? $g["dispatchprice"] : $minDispathPrice;
                            }
                        } else if ($g["dispatchtype"] == 0) {
                            if (empty($g["dispatchid"])) {
                                $dispatch_data = m("order")->getDefaultDispatch($supplier_uid);
                            } else {
                                $dispatch_data = m("order")->getOneDispatch($g["dispatchid"], $supplier_uid);
                            }
                            if (empty($dispatch_data)) {
                                $dispatch_data = m("order")->getNewDispatch($supplier_uid);
                            }
                            if (!empty($dispatch_data)) {
                                $areas = unserialize($dispatch_data["areas"]);
                                if ($dispatch_data["calculatetype"] == 1) {
                                    $param = $g["total"];
                                } else {
                                    $param = $g["weight"] * $g["total"];
                                }
                                $dkey = $dispatch_data["id"];
                                if (array_key_exists($dkey, $dispatch_array)) {
                                    $dispatch_array[$dkey]["param"] += $param;
                                } else {
                                    $dispatch_array[$dkey]["data"]  = $dispatch_data;
                                    $dispatch_array[$dkey]["param"] = $param;
                                }
                            }
                        }
                    }
                }

                if (!empty($dispatch_array)) {
                    foreach ($dispatch_array as $k => $v) {
                        $dispatch_data = $dispatch_array[$k]["data"];
                        $param         = $dispatch_array[$k]["param"];
                        $areas         = unserialize($dispatch_data["areas"]);
                        if (!empty($address)) {
                            $dispatch_price += m("order")->getCityDispatchPrice($areas, $address["city"], $param, $dispatch_data, $supplier_uid);
                        } else if (!empty($member["city"])) {
                            $dispatch_price += m("order")->getCityDispatchPrice($areas, $member["city"], $param, $dispatch_data, $supplier_uid);
                        } else {
                            $dispatch_price += m("order")->getDispatchPrice($param, $dispatch_data, -1, $supplier_uid);
                        }
                    }
                }
            }
            if ($dflag != "true") {
                if (empty($saleset["dispatchnodeduct"])) {
                    $deductprice2 += $dispatch_price;
                }
            }
            $deductcredit = 0;
            $deductmoney  = 0;
            if ($sale_plugin) {
                $credit = m("member")->getCredit($openid, "credit1");
                if (!empty($saleset["creditdeduct"])) {
                    $pcredit = intval($saleset["credit"]);
                    $pmoney  = round(floatval($saleset["money"]), 2);
                    if ($pcredit > 0 && $pmoney > 0) {
                        if ($credit % $pcredit == 0) {
                            $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                        } else {
                            $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                        }
                    }
                    if ($deductmoney > $deductprice) {
                        $deductmoney = $deductprice;
                    }
                    if ($deductmoney > $totalprice) {
                        $deductmoney = $totalprice;
                    }
                    $deductcredit = $deductmoney / $pmoney * $pcredit;
                }
                if (!empty($saleset["moneydeduct"])) {
                    $deductcredit2 = m("member")->getCredit($openid, "credit2");
                    if ($deductcredit2 > $totalprice) {
                        $deductcredit2 = $totalprice;
                    }
                    if ($deductcredit2 > $deductprice2) {
                        $deductcredit2 = $deductprice2;
                    }
                }
            }

            //虚拟币抵扣
            $deductyunbi = 0;
            $deductyunbimoney = 0;
            if ($yunbi_plugin && $yunbiset['isdeduct']) {
                $virtual_currency = $member['virtual_currency'];//m('member')->getCredit($openid, 'virtual_currency');
                $ycredit = 1;
                $ymoney  = round(floatval($yunbiset['money']), 2);
                if ($ycredit > 0 && $ymoney > 0) {
                    if ($virtual_currency % $ycredit == 0) {
                        $deductyunbimoney = round(intval($virtual_currency / $ycredit) * $ymoney, 2);
                    } else {
                        $deductyunbimoney = round((intval($virtual_currency / $ycredit) + 1) * $ymoney, 2);
                    }
                }

                if ($deductyunbimoney >$yunbideductprice) {
                    $deductyunbimoney = $yunbideductprice;
                }
                if ($deductyunbimoney > $totalprice) {
                    $deductyunbimoney = $totalprice;
                }

                $deductyunbi = $deductyunbimoney / $ymoney * $ycredit;

            }

        }

        return show_json(1, array(
            "price" => $dispatch_price,
            "hascoupon" => $hascoupon,
            "couponcount" => $couponcount,
            "hascard"   => $hascard,
            "cardcount" => $cardcount,
            "deductenough_money" => $deductenough_money,
            "deductenough_enough" => $deductenough_enough,
            "deductcredit2" => $deductcredit2,
            "deductcredit" => $deductcredit,
            "deductmoney" => $deductmoney,
            "deductyunbi" => $deductyunbi,
            "deductyunbimoney" => $deductyunbimoney,
            "supplier_uid" => $supplier_uid
        ));

    }
    elseif ($operation == 'create' && $_W['ispost']) {
        $ischannelpay = intval($_GPC['ischannelpay']);
        $ischannelpick = intval($_GPC['ischannelpick']);
        //$isyunbipay = intval($_GPC['isyunbipay']);
        $order_data = $_GPC['order'];
        if(p('hotel')){
            if($_GPC['type']=='99'){
                $order_data[] = $_GPC;
            }
        }

        //通用订单号，支付用
        $ordersn_general    = m('common')->createNO('order', 'ordersn', 'SH');
        $member       = m('member')->getMember($openid);
        $level         = m('member')->getLevel($openid);
        //判断所有商品有没有不支持此配送方式的情况
        $can_buy = array();
        $can_buy = m('order')->isSupportDelivery($order_data);
        if ($can_buy['status'] == -1) {
            return show_json(-2,'您的订单中，商品标题为 ‘'.$can_buy['title'].'’ 的商品不支持配送核销，请更换配送方式或者剔除此商品！');

        } else if ($can_buy['status'] == -2) {
            return show_json(-2,'您的订单中，商品标题为 ‘'.$can_buy['title'].'’ 的商品不支持快递配送，请更换配送方式或者剔除此商品！');

        }
        $yunbiprice = 0;
        //判断结束
        foreach ($order_data as $key => $order_row) {
            unset($minDispathPrice);
            $dispatchtype = intval($order_row['dispatchtype']);
            $addressid    = intval($order_row['addressid']);
            $address      = false;
            if (!empty($addressid) && ($dispatchtype == 0 || $dispatchtype == 2)) {
                $address = pdo_fetch('select id,realname,mobile,address,province,city,area,street from ' . tablename('sz_yi_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(

                    ':uniacid' => $uniacid,
                    ':openid' => $openid,
                    ':id' => $addressid
                ));
                if (empty($address)) {
                    return show_json(0, '未找到地址');
                }
            }
            $carrierid = intval($order_row["carrierid"]);
            $goods = $order_row['goods'];
            if (empty($goods)) {
                return show_json(0, '未找到任何商品');
            }
            $allgoods      = array();
            $totalprice    = 0;
            $goodsprice    = 0;
            $redpriceall   = 0;
            $weight        = 0;
            $discountprice = 0;
            $goodsarr      = explode('|', $goods);
            $cash          = 1;
            $deductprice   = 0;
            $deductprice2   = 0;
            $virtualsales  = 0;
            $dispatch_price = 0;
            $dispatch_array = array();
            $sale_plugin   = p('sale');
            $saleset       = false;
            if ($sale_plugin) {
                $saleset = $sale_plugin->getSet();
                $saleset["enoughs"] = $sale_plugin->getEnoughs();
            }
            $isvirtual = false;
            $isverify  = false;
            $isverifysend  = false;

            foreach ($goodsarr as $g) {
                if (empty($g)) {
                    continue;
                }
                $goodsinfo  = explode(',', $g);
                $goodsid    = !empty($goodsinfo[0]) ? intval($goodsinfo[0]) : '';
                $optionid   = !empty($goodsinfo[1]) ? intval($goodsinfo[1]) : 0;
                $goodstotal = !empty($goodsinfo[2]) ? intval($goodsinfo[2]) : '1';
                if ($goodstotal < 1) {
                    $goodstotal = 1;
                }
                if ($store_total) {
                    $storegoodstotal = pdo_fetchcolumn("SELECT total FROM " .tablename('sz_yi_store_goods'). " WHERE goodsid=:goodsid and uniacid=:uniacid and storeid=:storeid and optionid=:optionid", array(':goodsid' => $goodsid, ':uniacid' => $uniacid, ':storeid' => $carrierid, ':optionid' => $optionid));
                    if ($goodstotal > $storegoodstotal && !empty($carrierid)) {
                        return show_json(-2,'抱歉，此门店库存不足！');
                    }
                }

                if (empty($goodsid)) {
                    return show_json(0, '参数错误，请刷新重试');
                }

                $channel_condtion = '';
                $yunbi_condtion = '';
                if (p('channel')) {
                    $channel_condtion = 'isopenchannel,';
                }
                if (p('yunbi')) {
                    $yunbi_condtion = 'isforceyunbi,yunbi_deduct,';
                }
                $sql  = 'SELECT id as goodsid,costprice,' . $channel_condtion . 'supplier_uid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,goodssn,productsn,sales,istime,timestart,timeend,usermaxbuy,maxbuy,unit,buylevels,buygroups,deleted,status,deduct,virtual,discounts,discounts2,discountway,discounttype,deduct2,ednum,edmoney,edareas,diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,redprice, yunbi_deduct,bonusmoney,plugin FROM ' . tablename('sz_yi_goods') . ' where id=:id and uniacid=:uniacid  limit 1';

                $data = pdo_fetch($sql, array(
                    ':uniacid' => $uniacid,
                    ':id' => $goodsid
                ));
                //阶梯价格
                if ($isladder) {
                    $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                            ':id' => $goodsid
                        ));
                    if ($ladders) {
                        $ladders = unserialize($ladders['ladders']);
                        $laddermoney = m('goods')->getLaderMoney($ladders,$goodstotal);
                        $data['marketprice'] = $laddermoney > 0 ? $laddermoney : $data['marketprice'];
                    }
                } 
                if (p('channel')) {
                    if ($ischannelpay == 1) {
                        if (empty($data['isopenchannel'])) {
                            return show_json(-1, $data['title'] . '<br/> 不支持采购!请前往购物车移除该商品！');
                        }
                    }
                }
                if($data['plugin'] == 'fund'){
                    $issale = false;
                }
                if (empty($data['status']) || !empty($data['deleted'])) {
                    return show_json(-1, $data['title'] . '<br/> 已下架!');
                }
                $virtualid     = $data['virtual'];
                $data['stock'] = $data['total'];
                $data['total'] = $goodstotal;
                if ($data['cash'] != 2) {
                    $cash = 0;
                }
                $unit = empty($data['unit']) ? '件' : $data['unit'];
                if ($data['maxbuy'] > 0) {
                    if ($goodstotal > $data['maxbuy']) {
                        return show_json(-1, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . $unit . "!");

                    }
                }
                if ($data['usermaxbuy'] > 0) {
                    $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(
                        ':goodsid' => $data['goodsid'],
                        ':uniacid' => $uniacid,
                        ':openid' => $openid
                    ));
                    if (($order_goodscount > 0 && $order_goodscount > $data['usermaxbuy'])
                        || ($order_goodscount == 0 && $goodstotal > $data['usermaxbuy'])) {
                        return show_json(-1, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . $unit . "!");
                    }
                }
                if ($data['istime'] == 1) {
                    if (time() < $data['timestart']) {
                        return show_json(-1, $data['title'] . '<br/> 限购时间未到!');
                    }
                    if (time() > $data['timeend']) {
                        return show_json(-1, $data['title'] . '<br/> 限购时间已过!');
                    }
                }
                $levelid = intval($member['level']);
                $groupid = intval($member['groupid']);
                if ($data['buylevels'] != '') {
                    $buylevels = explode(',', $data['buylevels']);
                    if (!in_array($levelid, $buylevels)) {
                        return show_json(-1, '您的会员等级无法购买<br/>' . $data['title'] . '!');
                    }
                }
                if ($data['buygroups'] != '') {
                    $buygroups = explode(',', $data['buygroups']);
                    if (!in_array($groupid, $buygroups)) {
                        return show_json(-1, '您所在会员组无法购买<br/>' . $data['title'] . '!');
                    }
                }
                if (!empty($optionid)) {
                    $option = pdo_fetch('select * from ' . tablename('sz_yi_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(
                        ':uniacid' => $uniacid,
                        ':goodsid' => $goodsid,
                        ':id' => $optionid

                    ));

                    //阶梯价格
                    if ($isladder) {
                        $ladders = unserialize($option['option_ladders']);
                        if ($ladders) {
                            $laddermoney = m('goods')->getLaderMoney($ladders,$goodstotal);
                            $option['marketprice'] = $laddermoney > 0 ? $laddermoney : $option['marketprice'];
                        }
                    }
                    if (p('channel') && !empty($ischannelpick)) {
                        $my_option_stock = p('channel')->getMyOptionStock($openid,$goodsid,$optionid);
                        $option['stock'] = $my_option_stock;
                    }
                    if (!empty($option)) {
                        if ($option['stock'] != -1) {
                            if (empty($option['stock'])) {
                                return show_json(-1, $data['title'] . "<br/>" . $option['title'] . " 库存不足!");
                            }
                        }
                        $data['optionid']    = $optionid;
                        $data['optiontitle'] = $option['title'];
                        $data['marketprice'] = $option['marketprice'];
                        if (!empty($option['costprice'])) {
                            $data['costprice']   = $option['costprice'];
                        }
                        $virtualid           = $option['virtual'];
                        if (!empty($option['goodssn'])) {
                            $data['goodssn'] = $option['goodssn'];
                        }
                        if (!empty($option['productsn'])) {
                            $data['productsn'] = $option['productsn'];
                        }
                        if (!empty($option['weight'])) {
                            $data['weight'] = $option['weight'];
                        }
                        if (!empty($option['redprice'])) {
                            $data['redprice'] = $option['redprice'];
                        }
                    }
                } else {
                    if (p('channel') && !empty($ischannelpick)) {
                        $channel_stock = p('channel')->getMyOptionStock($openid, $data['goodsid'], 0);
                        $data['stock'] = $channel_stock;
                    }
                    if ($data['stock'] != -1) {
                        if (empty($data['stock'])) {
                            return show_json(-1, $data['title'] . "<br/>库存不足!");
                        }
                    }
                }
                /*if (p('yunbi')) {
                    if (!empty($isyunbipay) && !empty($yunbiset['isdeduct'])) {
                        $data['marketprice'] -= $data['yunbi_deduct'];
                    }
                }*/
                $data["diyformdataid"] = 0;
                $data["diyformdata"]   = iserializer(array());
                $data["diyformfields"] = iserializer(array());
                if ($order_row["fromcart"] == 1) {
                    if ($diyform_plugin) {
                        $cartdata = pdo_fetch("select id,diyformdataid,diyformfields,diyformdata from " . tablename("sz_yi_member_cart") . " " . " where goodsid=:goodsid and optionid=:optionid and openid=:openid and deleted=0 order by id desc limit 1", array(
                            ":goodsid" => $data["goodsid"],
                            ":optionid" => $data["optionid"],
                            ":openid" => $openid
                        ));
                        if (!empty($cartdata)) {
                            $data["diyformdataid"] = $cartdata["diyformdataid"];
                            $data["diyformdata"]   = $cartdata["diyformdata"];
                            $data["diyformfields"] = $cartdata["diyformfields"];
                        }
                    }
                } else {
                    if (!empty($diyformtype) && !empty($data["diyformid"])) {
                        $temp_data             = $diyform_plugin->getOneDiyformTemp($goods_data_id, 0);
                        $data["diyformfields"] = $temp_data["diyformfields"];
                        $data["diyformdata"]   = $temp_data["diyformdata"];
                        $data["declaration_mid"]= $temp_data["declaration_mid"];
                        $data["diyformid"]     = $formInfo["id"];
                    }
                }

                /**
                 *  红包价格计算
                 */
                if (strpos($data['redprice'], "%") === false) {
                    if (strpos($data['redprice'], "-") === false) {
                        $redprice = $data['redprice'];

                    } else {
                        $rprice = explode("-", $data['redprice']);
                        if ($rprice[1]>200) {
                            $redprice = rand($rprice[0]*100, 200*100)/100;
                        } else if ($rprice[0]<0) {
                            $redprice = rand(0, $rprice[1]*100)/100;
                        } else {
                            $redprice = rand($rprice[0]*100, $rprice[1]*100)/100;
                        }
                    }
                } else {
                    $rprice = explode("%", $data['redprice']);
                    $redprice = ($rprice[0] * $data['marketprice']) / 100;
                }
                $redprice = $redprice * $goodstotal;
                $redpriceall += $redprice;
                if (p('channel')) {
                    $my_info = p('channel')->getInfo($openid);
                    if ($ischannelpay == 1) {
                        $data['marketprice'] = $data['marketprice'] * $my_info['my_level']['purchase_discount']/100;
                    }
                }
                $gprice = $data['marketprice'] * $goodstotal;
                $goodsprice += $gprice;

                $ggprice = 0;

                if(p('hotel') && $_GPC['type']=='99'){
                    $gprice =$_GPC['goodsprice'];
                }

                if ($data['discountway'] == 1) {
                    //折扣
                    if ($data['discounttype'] == 1) {
                        //会员等级折扣
                        $discounts      = json_decode($data['discounts'], true);
                        $level          = m('member')->getLevel($openid);
                        if (is_array($discounts)) {
                            if (!empty($level["id"])) {
                                if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                                    $level["discount"] = floatval($discounts["level" . $level["id"]]);
                                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                                    $level["discount"] = floatval($level["discount"]);
                                } else {
                                    $level["discount"] = 0;
                                }
                            } else {
                                if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                                    $level["discount"] = floatval($discounts["default"]);
                                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < 10) {
                                    $level["discount"] = floatval($level["discount"]);
                                } else {
                                    $level["discount"] = 0;
                                }
                            }
                            if (p('channel') && $ischannelpay == 1) {
                                $level["discount"] = 10;
                            }
                        }


                    } else {
                        //分销商等级折扣
                        $discounts      = json_decode($data['discounts2'], true);
                        $level     = p("commission")->getLevel($openid);

                        //是分销商
                        $level["discount"] = 0;
                        if ($member['isagent'] == 1 && $member['status'] == 1) {

                            if (is_array($discounts)) {
                                if (!empty($level["id"])) {
                                    if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < 10) {
                                        $level["discount"] = floatval($discounts["level" . $level["id"]]);

                                    }
                                } else {
                                    if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < 10) {
                                        $level["discount"] = floatval($discounts["default"]);

                                    }
                                }
                            }
                        }

                    }
                    if (p('channel') && $ischannelpay == 1) {
                        $level['discount'] = 10;
                    }
                    if (empty($data['isnodiscount']) && $level['discount'] > 0 && $level['discount'] < 10) {
                        $dprice = round($gprice * $level['discount'] / 10, 2);
                        $discountprice += $gprice - $dprice;
                        $ggprice = $dprice;
                    } else {
                        $ggprice = $gprice;
                    }
                } else {
                    //立减
                    if ($data['discounttype'] == 1) {
                        //会员等级立减
                        $discounts      = json_decode($data['discounts'], true);
                        $level          = m('member')->getLevel($openid);
                        $level['discount'] = 0;
                        if (is_array($discounts)) {
                            if (!empty($level["id"])) {
                                if (floatval($discounts["level" . $level["id"]]) > 0 && floatval($discounts["level" . $level["id"]]) < $data['marketprice']) {
                                    $level["discount"] = floatval($discounts["level" . $level["id"]]);
                                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < $data['marketprice']) {
                                    $level["discount"] = floatval($level["discount"]);
                                } else {
                                    $level["discount"] = 0;
                                }
                            } else {
                                if (floatval($discounts["default"]) > 0 && floatval($discounts["default"]) < $data['marketprice']) {
                                    $level["discount"] = floatval($discounts["default"]);
                                } else if (floatval($level["discount"]) > 0 && floatval($level["discount"]) < $data['marketprice']) {
                                    $level["discount"] = floatval($level["discount"]);
                                } else {
                                    $level["discount"] = 0;
                                }
                            }
                        }
                    } else {
                        //分销商等级立减
                        $discounts      = json_decode($data['discounts2'], true);
                        $level     = p("commission")->getLevel($openid);

                        //是分销商
                        $level["discount"] = 0;
                        if ($member['isagent'] == 1 && $member['status'] == 1) {

                            if (is_array($discounts)) {
                                if (!empty($level["id"])) {
                                    if (floatval($discounts["level" . $level["id"]]) < $data['marketprice']) {
                                        $level["discount"] = floatval($discounts["level" . $level["id"]]);
                                    }
                                } else {
                                    if (floatval($discounts["default"]) < $data['marketprice']) {
                                        $level["discount"] = floatval($discounts["default"]);
                                    }
                                }
                            }
                        }
                    }
                    if (empty($data['isnodiscount']) && $level['discount'] < $data['marketprice']) {
                        $dprice = round($gprice - $level['discount'] * $goodstotal, 2);
                        $discountprice += $gprice - $dprice;
                        $ggprice = $dprice;
                    } else {
                        $ggprice = $gprice;
                    }
                    if (p('channel') && $ischannelpay == 1) {
                        $ggprice = $gprice;
                    }

                }
                $data["realprice"] = $ggprice;
                $totalprice += $ggprice;
                $dispatchsend = false;
                if ($dispatchtype == '2') {
                    $dispatchtype = '0';
                    $dispatchsend = true;
                }

                if ($data['isverify'] == 2 && !$dispatchsend) {
                    $isverify = true;
                }


                if (empty($dispatchtype) && $isverify) {
                    $isverifysend = true;
                }



                if (!empty($data["virtual"]) || $data["type"] == 2) {
                    $isvirtual = true;
                }
                if (p('channel')) {
                    if ($ischannelpay == 1 && empty($ischannelpick)) {
                        $isvirtual = true;
                    }
                }

                $deductprice += $data["deduct"] * $data["total"];

                //虚拟币抵扣
                if ($data["yunbi_deduct"]) {
                    $yunbiprice += $data["yunbi_deduct"] * $data["total"];
                    $yunbideductprice = $data["yunbi_deduct"] * $data["total"];
                }
                //虚拟币抵扣
                $deductyunbi = 0;
                $deductyunbimoney = 0;

                if ($yunbi_plugin && $yunbiset['isdeduct']) {

                        if (isset($_GPC['order']) && !empty($_GPC['order'][0]['yunbi'])) {
                            $virtual_currency  = $member['virtual_currency'];//m('member')->getCredit($openid, 'virtual_currency');
                            $ycredit = 1;
                            $ymoney  = round(floatval($yunbiset['money']), 2);
                            if ($ycredit > 0 && $ymoney > 0) {
                                if ($virtual_currency % $ycredit == 0) {
                                    $deductyunbimoney = round(intval($virtual_currency / $ycredit) * $ymoney * $data["total"], 2);
                                } else {
                                    $deductyunbimoney = round((intval($virtual_currency / $ycredit) + 1) * $ymoney * $data["total"], 2);
                                }
                            }
                            if ($deductyunbimoney > $yunbideductprice) {
                                $deductyunbimoney = $yunbideductprice;
                            }
                            if ($deductyunbimoney > $totalprice) {
                                $deductyunbimoney = $totalprice;
                            }
                            $deductyunbi = round($deductyunbimoney / $ymoney * $ycredit, 2);

                        }

                    $totalprice -= $deductyunbimoney;
                }
                $virtualsales += $data["sales"];
                if ($data["deduct2"] == 0.00) {
                    $deductprice2 += $ggprice;
                } else if ($data["deduct2"] > 0) {
                    if ($data["deduct2"] > $ggprice) {
                        $deductprice2 += $ggprice;
                    } else {
                        $deductprice2 += $data["deduct2"];
                    }
                }
                $allgoods[] = $data;
            }
            if (empty($allgoods)) {
                return show_json(0, '未找到任何商品');
            }
            $deductenough = 0;
            /*获取满额队列中符合条件的最大值*/
            $tmp_money = 0;
            if (p('channel') && $ischannelpay == 1) {
                $saleset = array();
            }

            if ($saleset && $issale) {
                foreach ($saleset["enoughs"] as $e) {
                    if ($totalprice >= floatval($e["enough"]) && floatval($e["money"]) > 0) {
                        if ($e["enough"] > $tmp_money) {
                            $tmp_money = $e["enough"];
                            $deductenough = floatval($e["money"]);
                            if ($deductenough > $totalprice) {
                                $deductenough = $totalprice;
                            }
                        }
                    }
                }
            }

            //如果开启核销并且不支持配送，则没有运费
            $isDispath = true;
            if ($isverify && !$isverifysend && !$dispatchsend) {
                $isDispath = false;
            }

            if (!$isvirtual && $isDispath && $dispatchtype == 0) {
                //购买的商品是否都是统一运费的,如果是,取最低统一运费价
                $isAllSameDispath = true;
                //print_r($allgoods);
                foreach ($allgoods as $g) {
                    $g["ggprice"] = $g['realprice'];
                    $sendfree = false;
                    if (!empty($g["issendfree"])) {
                        $sendfree = true;
                    } else {
                        $gareas = explode(";", $g["edareas"]);
                        if ($g["total"] >= $g["ednum"] && $g["ednum"] > 0) {
                            if (empty($gareas)) {
                                $sendfree = true;
                            } else {
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else if (!empty($member["city"])) {
                                    if (!in_array($member["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else {
                                    $sendfree = true;
                                }
                            }
                        }

                        if ($g["ggprice"] >= floatval($g["edmoney"]) && floatval($g["edmoney"]) > 0) {
                            if (empty($gareas)) {
                                $sendfree = true;
                            } else {
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else if (!empty($member["city"])) {
                                    if (!in_array($member["city"], $gareas)) {
                                        $sendfree = true;
                                    }
                                } else {
                                    $sendfree = true;
                                }
                            }
                        }
                    }
                    if (!$sendfree) {
                        if ($g["dispatchtype"] == 1) {
                            if ($g["dispatchprice"] > 0) {
                                //$dispatch_price += $g["dispatchprice"] * $g["total"];
                                //多个商品不同统一运费时，取最低价统一运费收取
                                if (!isset($minDispathPrice)) {
                                    $minDispathPrice = $g["dispatchprice"];
                                }
                                $dispatch_price = ($minDispathPrice > $g["dispatchprice"]) ? $g["dispatchprice"] : $minDispathPrice;

                            }
                        } else if ($g["dispatchtype"] == 0) {
                            //$isAllSameDispath = false;
                            if (empty($g["dispatchid"])) {
                                $dispatch_data = m("order")->getDefaultDispatch($g['supplier_uid']);
                            } else {
                                $dispatch_data = m("order")->getOneDispatch($g["dispatchid"], $g['supplier_uid']);
                            }
                            if (empty($dispatch_data)) {
                                $dispatch_data = m("order")->getNewDispatch($g['supplier_uid']);
                            }
                            if (!empty($dispatch_data)) {
                                $areas = unserialize($dispatch_data["areas"]);
                                if ($dispatch_data["calculatetype"] == 1) {
                                    $param = $g["total"];
                                } else {
                                    $param = $g["weight"] * $g["total"];
                                }
                                $dkey = $dispatch_data["id"];
                                if (array_key_exists($dkey, $dispatch_array)) {
                                    $dispatch_array[$dkey]["param"] += $param;
                                } else {
                                    $dispatch_array[$dkey]["data"]  = $dispatch_data;
                                    $dispatch_array[$dkey]["param"] = $param;
                                }
                            }
                        }
                    }
                }
                if (!empty($dispatch_array)) {
                    foreach ($dispatch_array as $k => $v) {
                        $dispatch_data = $dispatch_array[$k]["data"];
                        $param         = $dispatch_array[$k]["param"];
                        $areas         = unserialize($dispatch_data["areas"]);
                        if (!empty($address)) {
                            $dispatch_price += m("order")->getCityDispatchPrice($areas, $address["city"], $param, $dispatch_data, $order_row['supplier_uid']);
                        } else if (!empty($member["city"])) {
                            $dispatch_price += m("order")->getCityDispatchPrice($areas, $member["city"], $param, $dispatch_data, $order_row['supplier_uid']);
                        } else {
                            $dispatch_price += m("order")->getDispatchPrice($param, $dispatch_data, -1, $order_row['supplier_uid']);
                        }
                    }
                }
            }

            if ($saleset) {
                if (!empty($saleset["enoughfree"])) {
                    //enoughorder为0则全场包邮
                    if (floatval($saleset["enoughorder"]) <= 0) {
                        $dispatch_price = 0;
                    } else {
                        if ($totalprice >= floatval($saleset["enoughorder"])) {
                            if (empty($saleset["enoughareas"])) {
                                $dispatch_price = 0;
                            } else {
                                $areas = explode(";", $saleset["enoughareas"]);
                                if (!empty($address)) {
                                    if (!in_array($address["city"], $areas)) {
                                        $dispatch_price = 0;
                                    }
                                }
                                //去掉下面else,没有用的代码,根本不会执行, By RainYang.
                            }
                        }
                    }
                }
            }

            $couponprice = 0;
            $couponid    = intval($order_row["couponid"]);
            if ($plugc) {
                $coupon = $plugc->getCouponByDataID($couponid);
                if (!empty($coupon)) {
                    if ($totalprice >= $coupon["enough"] && empty($coupon["used"])) {
                        if ($coupon["backtype"] == 0) {
                            if ($coupon["deduct"] > 0) {
                                $couponprice = $coupon["deduct"];
                            }
                        } else if ($coupon["backtype"] == 1) {
                            if ($coupon["discount"] > 0) {
                                $couponprice = $totalprice * (1 - $coupon["discount"] / 10);
                            }
                        }
                        if ($couponprice > 0) {
                            $totalprice -= $couponprice;
                        }
                    }
                }
            }
            $totalprice -= $deductenough;
            $totalprice += $dispatch_price;

            $cardid = 0;
            $cardid = intval($order_row['cardid']);
            //使用金额
            $cardprice = 0;
            if ($plugincard) {
                $cardinfo = $plugincard->getCradInfo($cardid);
                if (!empty($cardinfo)) {
                    if ($cardinfo['balance'] >= $totalprice) {
                        $cardprice = $totalprice;
                        $balance = $cardinfo['balance'] - $totalprice;
                        $totalprice -= $cardinfo['balance'];
                        if ($totalprice < 0) {
                            $totalprice = 0;
                        }
                    } else {
                        $cardprice = $cardinfo['balance'];
                    }
                    //代金卡剩余金额
                    $balance = $cardinfo['balance'] - $cardprice;
                    pdo_update('sz_yi_card_data', 
                        array('balance' => $balance), 
                        array('uniacid' => $_W['uniacid'], 'id' => $cardid)
                    );
                    $totalprice -= $cardprice;
                }
            }

            if ($saleset && empty($saleset["dispatchnodeduct"])) {
                $deductprice2 += $dispatch_price;
            }
            $deductcredit  = 0;
            $deductmoney   = 0;
            $deductcredit2 = 0;
            if ($sale_plugin) {
                if (isset($_GPC['order']) && !empty($_GPC['order'][0]['deduct'])) {
                    $credit  = m('member')->getCredit($openid, 'credit1');
                    $saleset = $sale_plugin->getSet();
                    if (!empty($saleset['creditdeduct'])) {
                        $pcredit = intval($saleset['credit']);
                        $pmoney  = round(floatval($saleset['money']), 2);
                        if ($pcredit > 0 && $pmoney > 0) {
                            if ($credit % $pcredit == 0) {
                                $deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
                            } else {
                                $deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                            }
                        }
                        if ($deductmoney > $deductprice) {
                            $deductmoney = $deductprice;
                        }
                        if ($deductmoney > $totalprice) {
                            $deductmoney = $totalprice;
                        }
                        $deductcredit = round($deductmoney / $pmoney * $pcredit, 2);
                    }
                }
                $totalprice -= $deductmoney;
                if (!empty($order_row['deduct2'])) {
                    $deductcredit2 = m('member')->getCredit($openid, 'credit2');
                    if ($deductcredit2 > $totalprice) {
                        $deductcredit2 = $totalprice;
                    }
                    if ($deductcredit2 > $deductprice2) {
                        $deductcredit2 = $deductprice2;
                    }
                }
                $totalprice -= $deductcredit2;
            }
            $ordersn    = m('common')->createNO('order', 'ordersn', 'SH');
            $verifycode = "";
            if ($isverify) {
                $verifycode = random(8, true);
                while (1) {
                    $count = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(
                        ':verifycode' => $verifycode,
                        ':uniacid' => $_W['uniacid']
                    ));
                    if ($count <= 0) {
                        break;
                    }
                    $verifycode = random(8, true);
                }
            }
            $carrier  = $_GPC['order'][0]['carrier'];
            $carriers = is_array($carrier) ? iserializer($carrier) : iserializer(array());
            if ($totalprice <= 0) {
                $totalprice = 0;
            }
            if ($redpriceall > 200) {
                $redpriceall = 200;
            }
            if(p('hotel')){//判断如果安装酒店插件订单金额计算
                if($_GPC['type']=='99'){
                    $btime =  $_SESSION['data']['btime'];
                    // 住几天
                    $days =intval( $_SESSION['data']['day']);
                    // 离店
                    $etime =  $_SESSION['data']['etime'];
                    $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                    $params2 = array(':goodsid' =>$_GPC['id']);
                    $room = pdo_fetch($sql2, $params2);
                    if( $discountprice!='0'){
                        $totalprice =$_GPC['totalprice'] -$discountprice;
                    }else{
                        $totalprice =$_GPC['totalprice'];
                    }
                    $goodsprice =$_GPC['goodsprice'];
                }
            }
            $order   = array(
                'supplier_uid' => $order_row['supplier_uid'],
                'uniacid' => $uniacid,
                'openid' => $openid,
                'ordersn' => $ordersn,
                'ordersn_general' => $ordersn_general,
                'price' => $totalprice,
                'cash' => $cash,
                'discountprice' => $discountprice,
                'deductprice' => $deductmoney,
                'deductcredit' => $deductcredit,
                'deductyunbimoney' => $deductyunbi > 0 ? $yunbiprice : 0,
                'deductyunbi' => $deductyunbi,
                'deductcredit2' => $deductcredit2,
                'deductenough' => $deductenough,
                'status' => 0,
                'paytype' => 0,
                'transid' => '',
                'remark' => $order_row['remark'],
                'addressid' => empty($dispatchtype) ? $addressid : 0,
                'goodsprice' => $goodsprice,
                'dispatchprice' => $dispatch_price,
                'dispatchtype' => $dispatchtype,
                'dispatchid' => $dispatchid,
                "storeid" => $carrierid,
                'carrier' => $carriers,
                'createtime' => time(),
                'isverify' => $isverify ? 1 : 0,
                'verifycode' => $verifycode,
                'virtual' => $virtualid,
                'isvirtual' => $isvirtual ? 1 : 0,
                'oldprice' => $totalprice,
                'olddispatchprice' => $dispatch_price,
                "couponid" => $couponid,
                "couponprice" => $couponprice,
                'redprice' => $redpriceall,
            );
            if ($plugincard) {
                $order['cardid']    = $cardid;
                $order['cardprice'] = $cardprice;
            }
            if (p('channel')) {
                if (!empty($ischannelpick)) {
                    $order['ischannelself'] = 1;
                    $order['status']        = 1;
                }
            }
            if(p('hotel')){
                if($_GPC['type']=='99'){
                    $order['order_type']='3';
                    $order['addressid']='9999999';
                    $order['checkname']=$_GPC['realname'];//以下为酒店订单
                    $order['realmobile']=$_GPC['realmobile'];
                    $order['realsex']=$_GPC['realsex'];
                    $order['invoice']=$_GPC['invoice'];
                    $order['invoiceval']=$_GPC['invoiceval'];
                    $order['invoicetext']=$_GPC['invoicetext'];
                    $order['num']=$_GPC['goodscount'];
                    $order['btime']=$btime;
                    $order['etime']=$etime;
                    $order['depositprice']=$_GPC['depositprice'];
                    $order['depositpricetype']=$_GPC['depositpricetype'];
                    $order['roomid']=$room['id'];
                    $order['days']=$days;
                    $order['dispatchprice']=0;
                    $order['olddispatchprice']=0;
                    $order['deductcredit2']=$_GPC['deductcredit2'];
                    $order['deductcredit']=$_GPC['deductcredit'];
                    $order['deductprice']=$_GPC['deductcredit'];
                }
            }
            if ($diyform_plugin) {
                if (is_array($order_row["diydata"]) && !empty($order_formInfo)) {
                    $diyform_data           = $diyform_plugin->getInsertData($fields, $order_row["diydata"]);
                    $idata                  = $diyform_data["data"];
                    $order["diyformfields"] = iserializer($fields);
                    $order["diyformdata"]   = $idata;
                    $order["diyformid"]     = $order_formInfo["id"];
                }
            }

            if($issale == false){
                $order["plugin"]   = 'fund';
            }

            if (!empty($address)) {
                $order['address'] = iserializer($address);
            }
            pdo_insert('sz_yi_order',$order);
            $orderid = pdo_insertid();
            //渠道商推荐员
            if (p('channel')) {
                p('channel')->isChannelMerchant($orderid);
            }
            if(p('hotel')){
                if($_GPC['type']=='99'){
                    //像订单管理房间信息表插入数据
                    $r_sql = 'SELECT * FROM ' . tablename('sz_yi_hotel_room_price') .
                        ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                        ' `roomdate` < :etime';
                    $params = array(':roomid' => $room['id'],':btime' => $btime, ':etime' => $etime);
                    $price_list = pdo_fetchall($r_sql, $params);
                    if($price_list!=''){
                        foreach ($price_list as $key => $value) {
                            $order_room = array(
                                'orderid'=>$orderid ,
                                'roomid'=>$room['id'],
                                'roomdate'=>$value['roomdate'],
                                'thisdate'=>$value['thisdate'],
                                'oprice'=>$value['oprice'],
                                'cprice'=>$value['cprice'],
                                'mprice'=>$value['mprice'],
                            );
                            pdo_insert('sz_yi_order_room', $order_room);
                        }
                    }
                    //减去房量
                    $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                    $params2 = array(':goodsid' =>  $allgoods[0]['goodsid']);
                    $room = pdo_fetch($sql2, $params2);
                    $starttime = $btime;
                    for ($i = 0; $i <  $days; $i++) {
                        $sql = 'SELECT * FROM '. tablename('sz_yi_hotel_room_price'). ' WHERE  roomid = :roomid AND roomdate = :roomdate';
                        $day = pdo_fetch($sql, array(':roomid' => $room['id'], ':roomdate' => $btime));
                        pdo_update('sz_yi_hotel_room_price', array('num' => $day['num'] - $_GPC['goodscount']), array('id' => $day['id']));
                        $btime += 86400;
                    }

                }
            }

            if (is_array($carrier)) {
                //todo, carrier_realname和carrier_mobile字段表里有么? 没有，是序列化存进去的。
                $up = array(
                    'realname' => $carrier['carrier_realname'],
                    'membermobile' => $carrier['carrier_mobile']
                );
                $up_mc = array(
                    'realname' => $carrier['carrier_realname'],
                    'mobile' => $carrier['carrier_mobile']
                );

                pdo_update('sz_yi_member', $up, array(
                    'id' => $member['id'],
                    'uniacid' => $_W['uniacid']
                ));
                if (!empty($member['uid'])) {
                    pdo_update('mc_members', $up_mc, array(
                        'uid' => $member['uid'],
                        'uniacid' => $_W['uniacid']
                    ));
                }
            }
            if ($order_row['fromcart'] == 1) {
                $cartids = $order_row['cartids'];
                $cartids = implode(',',$cartids);
                if (!empty($cartids)) {
                    pdo_query('update ' . tablename('sz_yi_member_cart') . ' set deleted=1 where id in (' . $cartids . ') and openid=:openid and goodsid=:goodsid and optionid=:optionid and uniacid=:uniacid ', array(
                        ':uniacid' => $uniacid,
                        ':openid' => $openid,
                        ":goodsid" => $data["goodsid"],
                        ":optionid" => $data["optionid"]
                    ));
                } else {
                    pdo_query('update ' . tablename('sz_yi_member_cart') . ' set deleted=1 where openid=:openid and goodsid=:goodsid and optionid=:optionid and uniacid=:uniacid ', array(
                        ':uniacid' => $uniacid,
                        ':openid' => $openid,
                        ":goodsid" => $data["goodsid"],
                        ":optionid" => $data["optionid"]
                    ));
                }
            }
            $supplier_or_merchant_price = 0;
            $supplier_or_merchant_basis = 0;
            foreach ($allgoods as $goods) {
                $order_goods = array(
                    'uniacid' => $uniacid,
                    'orderid' => $orderid,
                    'goodsid' => $goods['goodsid'],
                    'price' => $goods['marketprice'] * $goods['total'],
                    'total' => $goods['total'],
                    'optionid' => $goods['optionid'],
                    'createtime' => time(),
                    'optionname' => $goods['optiontitle'],
                    'goodssn' => $goods['goodssn'],
                    'productsn' => $goods['productsn'],
                    "realprice" => $goods["realprice"],
                    "oldprice" => $goods["realprice"],
                    "openid" => $openid,
                    'goods_op_cost_price' => $goods['costprice']
                );
                if (p('supplier') || p('merchant')) {
                    $supplier_or_merchant_price += ($goods['costprice']*$goods['total']);
                    $supplier_or_merchant_basis += ($goods['bonusmoney']*$goods['total']);
                }
                //修改全返插件中房价
                if(p('hotel') && $_GPC['type']=='99'){
                    $order_goods['price'] = $goodsprice ;
                    $order_goods['realprice'] = $goodsprice-$discountprice;
                    $order_goods['oldprice'] = $goodsprice-$discountprice;
                }
                if ($diyform_plugin) {
                    $order_goods["diyformid"]     = $goods["diyformid"];
                    $order_goods["diyformdata"]   = $goods["diyformdata"];
                    $order_goods["declaration_mid"]   = $goods["declaration_mid"];
                    $order_goods["diyformfields"] = $goods["diyformfields"];
                }
                if (p('supplier')) {
                    $order_goods['supplier_uid'] = $goods['supplier_uid'];
                }
                if (p('channel')) {
                    $my_info = p('channel')->recursive_access_to_superior($openid,$goods['goodsid'],$goods['optionid'],$goods['total']);
                    if ($ischannelpay == 1 && empty($ischannelpick)) {
                        $order_goods['ischannelpay']  = $ischannelpay;
                    }
                    $order_goods['channel_id'] = 0;
                    if (!empty($my_info)) {
                        $mi_member = m('member')->getInfo($my_info['openid']);
                        $order_goods['channel_id'] = $mi_member['id'];
                    }
                }
                pdo_insert('sz_yi_order_goods', $order_goods);
                if (p('channel')) {
                    if (!empty($order_goods['channel_id']) && empty($order_goods['ischannelpay'])) {
                        $order_goods_id = pdo_insertid();
                        p('channel')->addChannelProfit($my_info,$order_goods,$order_goods_id);
                    }
                }
            }
            if (p('supplier')) {
                $supplier_set = p('supplier')->getSet();
                $supplier_order = array(
                    'uniacid' => $_W['uniacid'],
                    'orderid' => $orderid
                );
                if (empty($supplier_set['isopenbonus'])) {
                    $supplier_order['money'] = $supplier_or_merchant_price + $dispatch_price;
                    $supplier_order['isopenbonus'] = 0;
                } else {
                    $supplier_order['money'] = $supplier_or_merchant_basis + $dispatch_price;
                    $supplier_order['isopenbonus'] = 1;
                }
                pdo_insert('sz_yi_supplier_order', $supplier_order);
            }
            if (p('merchant')) {
                $merchant_set = p('merchant')->getSet();
                $merchant_order = array(
                    'uniacid' => $_W['uniacid'],
                    'orderid' => $orderid
                );
                if (empty($merchant_set['isopenbonus'])) {
                    $merchant_order['money'] = $totalprice;
                    $merchant_order['isopenbonus'] = 0;
                } else {
                    $merchant_order['money'] = $supplier_or_merchant_basis;
                    $merchant_order['isopenbonus'] = 1;
                }
                pdo_insert('sz_yi_merchant_order', $merchant_order);
            }
            $store_info = pdo_fetch(" SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:id and uniacid=:uniacid ", array(':id' => $carrierid, ':uniacid' => $_W['uniacid']));
            //门店真实结算价格
            $order_goods_store = pdo_fetchall(" SELECT * FROM ".tablename('sz_yi_order_goods')." WHERE orderid=:id and uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':id' => $orderid));
            $goods_realprice = 0;
            foreach ($order_goods_store as $val) {
                $goods_store = pdo_fetch(" SELECT * FROM ".tablename('sz_yi_goods')." WHERE uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $val['goodsid']));

                if (empty($goods_store['balance_with_store']) || $goods_store['balance_with_store'] == '0') {
                    $goods_realprice += $val['price'] * (100 - $goods_store['goods_balance'])/100;

                } elseif (!empty($store_info['balance'])) {
                    $goods_realprice += $val['price'] * (100 - $store_info['balance'])/100;
                } else {
                    $goods_realprice += $val['price'];
                }
            }
            $realprice = $goods_realprice - ($goodsprice-$totalprice) * (100 - $store_info['balance'])/100;
            pdo_update('sz_yi_order', array('realprice' => $realprice), array('id' => $orderid, 'uniacid' => $_W['uniacid']));

            if(p('hotel')){
                //打印订单
                $set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
                //订单信息
                $print_order = $order;
                //商品信息
                $ordergoods = pdo_fetchall("select * from " . tablename('sz_yi_order_goods') . " where uniacid=".$_W['uniacid']." and orderid=".$orderid);
                foreach ($ordergoods as $key =>$value) {
                    $ordergoods[$key]['price'] = pdo_fetchcolumn("select marketprice from " . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
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
                    if(!empty($print_detail) &&  $print_detail['status']=='1'){//是否存在打印机，以及判断订单在支付前打印
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

            if ($deductcredit > 0) {
                $shop = m('common')->getSysset('shop');
                m('member')->setCredit($openid, 'credit1', -$deductcredit, array(
                    '0',
                    $shop['name'] . "购物积分抵扣 消费积分: {$deductcredit} 抵扣金额: {$deductmoney} 订单号: {$ordersn}"
                ));
            }

            if ($deductyunbi > 0) {
                $shop = m('common')->getSysset('shop');

                p('yunbi')->setVirtualCurrency($openid,-$deductyunbi);
                //虚拟币抵扣记录
                $data_log = array(
                    'id'           => $member['id'],
                    'openid'        => $openid,
                    'credittype'    => 'virtual_currency',
                    'money'         => $deductyunbi,
                    'remark'        => "购物".$yunbiset['yunbi_title']."抵扣 消费".$yunbiset['yunbi_title'].": {$deductyunbi} 抵扣金额: {$deductyunbimoney} 订单号: {$ordersn}"
                );
                p('yunbi')->addYunbiLog($uniacid,$data_log,'3');
            }

            if (p('channel') && !empty($ischannelpick)) {
                p('channel')->deductChannelStock($orderid);
            } else {
                if (empty($virtualid)) {
                    m('order')->setStocksANDCredits($orderid, 0);
                } else {
                    if (isset($allgoods[0])) {
                        $vgoods = $allgoods[0];
                        pdo_update('sz_yi_goods', array(
                            'sales' => $vgoods['sales'] + $vgoods['total']
                        ), array(
                            'id' => $vgoods['goodsid']
                        ));
                    }
                }
            }
            /*if (empty($virtualid)) {
                m('order')->setStocksAndCredits($orderid, 0);
            } else {
                if (isset($allgoods[0])) {
                    $vgoods = $allgoods[0];
                    pdo_update('sz_yi_goods', array(
                        'sales' => $vgoods['sales'] + $vgoods['total']
                    ), array(
                        'id' => $vgoods['goodsid']
                    ));
                }
            }*/
            $plugincoupon = p("coupon");
            if ($plugincoupon) {
                $plugincoupon->useConsumeCoupon($orderid);
            }
            m('notice')->sendOrderMessage($orderid);
            if (p('channel')) {
                if (empty($ischannelpay)) {
                    $pluginc = p('commission');
                    if ($pluginc) {
                        $pluginc->checkOrderConfirm($orderid);
                    }
                }
            } else {
                $pluginc = p('commission');
                if ($pluginc) {
                    $pluginc->checkOrderConfirm($orderid);
                }
            }
            /*$pluginc = p('commission');
            if ($pluginc) {
                $pluginc->checkOrderConfirm($orderid);
            }*/

        }
        /*if ($pluginc) {
            $pluginc->checkOrderConfirm($orderid);
        }*/
        return show_json(1, array(
            'orderid' => $orderid,
            'ischannelpay' => $ischannelpay,
            'ischannelpick' => $ischannelpick
        ));
    }else if ($operation == 'date') {
        global $_GPC, $_W;
        $id = $_GPC['id'];
        if ($search_array && !empty($search_array['bdate']) && !empty($search_array['day'])) {
            $bdate = $search_array['bdate'];
            $day = $search_array['day'];
        } else {
            $bdate = date('Y-m-d');
            $day = 1;
        }
        load()->func('tpl');
        include $this->template('order/date');
    }
}
if(p('hotel') && $goods_data['type']=='99'){ //判断是否开启酒店插件
    if(empty($_SESSION['data'])){
        $btime = strtotime(date('Y-m-d'));
        $day=1;
        $etime = $btime + $day * 86400;
        //$weekarray = array("日", "一", "二", "三", "四", "五", "六");
        $arr['btime'] = $btime;
        $arr['etime'] = $etime;
        $arr['bdate'] = date('Y-m-d');
        $arr['edate'] = date('Y-m-d', $etime);
        //$arr['bweek'] = '星期' . $weekarray[date("w", $btime)];
        //$arr['eweek'] = '星期' . $weekarray[date("w", $etime)];
        $arr['day'] = $day;
        $_SESSION['data']=$arr;
    }
    include $this->template('order/confirm_hotel');
}else{
    $hascouponplugin = $hascouponplugin && $issale ? true : false;
    include $this->template('order/confirm');
}
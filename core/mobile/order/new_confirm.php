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
if (isset($allset['verify']) && $allset['verify']['store_total'] == 1) {
    $store_total = true;
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
}

$yunbi_plugin   = p('yunbi');
if ($yunbi_plugin) {
    $yunbiset = $yunbi_plugin->getSet();
}

if ($_W['isajax']) {
    $ischannelpick = intval($_GPC['ischannelpick']);
    $isyunbipay = intval($_GPC['isyunbipay']);
    if ($operation == 'date') {
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
    include $this->template('order/confirm');
}
<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation      = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid         = m('user')->getOpenid();
$uniacid        = $_W['uniacid'];
$orderid        = intval($_GPC['id']);
$shopset   = m('common')->getSysset('shop');
$diyform_plugin = p('diyform');
$orderisyb = pdo_fetch("select ordersn_general,status from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
            ':id' => $orderid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
//通过openid判断是否是店长  store_manager  存在为店长
$member_id = pdo_fetch('select id from ' . tablename('sz_yi_member') . ' where openid =:openid and uniacid=:uniacid limit 1', array(
        ':openid' => $openid,
        ':uniacid' => $uniacid
    ));
$store_manager = pdo_fetch('select id from ' . tablename('sz_yi_store') . ' where member_id =:member_id and uniacid=:uniacid limit 1', array(
        ':member_id' => $member_id,
        ':uniacid' => $uniacid
    ));
if (empty($store_manager)) {
    $order          = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
        ':id' => $orderid,
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));
} else {
    $order          = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(
        ':id' => $orderid,
        ':uniacid' => $uniacid
    ));
}
$yunbi_plugin   = p('yunbi');
if ($yunbi_plugin) {
    $yunbiset = $yunbi_plugin->getSet();
}
if(!empty($orderisyb['ordersn_general']) && $orderisyb['status']==0){
    $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid and openid=:openid', array(
        ':ordersn_general' => $orderisyb['ordersn_general'],
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));
    $orderids = array();
    $order['goodsprice'] = 0;
    $order['olddispatchprice'] = 0;
    $order['discountprice'] = 0;
    $order['deductprice'] = 0;
    $order['deductcredit2'] = 0;
    $order['deductenough'] = 0;
    $order['changeprice'] = 0;
    $order['changedispatchprice'] = 0;
    $order['couponprice'] = 0;
    $order['price'] = 0;
    foreach ($order_all as $k => $v) {
        $orderids[] = $v['id'];
        $order['goodsprice'] += $v['goodsprice'];
        $order['olddispatchprice'] += $v['olddispatchprice'];
        $order['discountprice'] += $v['discountprice'];
        $order['deductprice'] += $v['deductprice'];
        $order['deductcredit2'] += $v['deductcredit2'];
        $order['deductcommission'] += $v['deductcommission'];
        $order['deductenough'] += $v['deductenough'];
        $order['changeprice'] += $v['changeprice'];
        $order['changedispatchprice'] += $v['changedispatchprice'];
        $order['couponprice'] += $v['couponprice'];
        $order['price'] += $v['price'];
    }
    
    $order['ordersn'] = $orderisyb['ordersn_general'];
    $orderid_where_in = implode(',', $orderids);
    $order_where = "og.orderid in ({$orderid_where_in})";
}else{
    $order_where = "og.orderid = ".$orderid;
}

if(p('cashier') && $order['cashier'] == 1){
    $order['name'] = set_medias(pdo_fetch('select * from ' .tablename('sz_yi_cashier_store'). ' where id=:id and uniacid=:uniacid', array(':id' => $order['cashierid'],':uniacid'=>$_W['uniacid'])), 'thumb');
}

if (!empty($order)) {
    $order['virtual_str'] = str_replace("\n", "<br/>", $order['virtual_str']);
    /*
     * 虚拟物品插件链接跳转 begin
     */
    $order['virtual_url'] = '';
    $virtual_strs = explode("<br/>",$order['virtual_str']);
    foreach ($virtual_strs as $key => $value) {
        if(strstr($value,'http://')){
            $str = explode(": ",$value);
            $order['virtual_strs'][$key]['con'] = $str[0];
            $order['virtual_strs'][$key]['url'] = $str[1];
            if(count($virtual_strs) == 1)
            {
                $order['virtual_url'] = $str[1];
            }
        } elseif (strstr($value,'https://')) {
            $str = explode(": ",$value);
            $order['virtual_strs'][$key]['con'] = $str[0];
            $order['virtual_strs'][$key]['url'] = $str[1];
            if(count($virtual_strs) == 1)
            {
                $order['virtual_url'] = $str[1];
            }
        }
    }
    /*
     * 虚拟物品插件链接跳转 end
     */
    $diyformfields        = "";
    if ($diyform_plugin) {
        $diyformfields = ",og.diyformfields,og.diyformdata";
    }
    $goods        = pdo_fetchall("select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids{$diyformfields}  from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where {$order_where} and og.uniacid=:uniacid ", array(
        ':uniacid' => $uniacid
    ));
    $show         = 1;
    $diyform_flag = 0;
    foreach ($goods as &$g) {
        $g['unit_price'] = $g['price'] / $g['total'];
        $g['thumb'] = tomedia($g['thumb']);
        if($order['plugin'] == 'fund'){
            $g['url'] = $this->createPluginMobileUrl('fund/detail', array('id' => $g['goodsid']));
        }else{
            $g['url'] = $this->createMobileUrl('shop/detail', array('id' => $g['goodsid']));
        }
        if ($diyform_plugin) {
            $diyformdata   = iunserializer($g['diyformdata']);
            $fields        = iunserializer($g['diyformfields'])?:array();
            $diyformfields = array();
            foreach ($fields as $key => $value) {
                $tp_value = "";
                $tp_css   = "";
                if ($value['data_type'] == 1 || $value['data_type'] == 3) {
                    $tp_css .= " dline1";
                }
                if ($value['data_type'] == 5) {
                    $tp_css .= " dline2";
                }
                if ($value['data_type'] == 0 || $value['data_type'] == 1 || $value['data_type'] == 2 || $value['data_type'] == 6 || $value['data_type'] == 7) {
                    $tp_value = str_replace("\n", "<br/>", $diyformdata[$key]);
                } else if ($value['data_type'] == 3 || $value['data_type'] == 8) {
                    if (is_array($diyformdata[$key])) {
                        foreach ($diyformdata[$key] as $k1 => $v1) {
                            $tp_value .= $v1 . " ";
                        }
                    }
                } else if ($value['data_type'] == 5) {
                    if (is_array($diyformdata[$key])) {
                        foreach ($diyformdata[$key] as $k1 => $v1) {
                            $tp_value .= "<img style='height:25px;padding:1px;border:1px solid #ccc'  src='" . tomedia($v1) . "'/>";
                        }
                    }
                } else if ($value['data_type'] == 9) {
                    $tp_value = ($diyformdata[$key]['province'] != '请选择省份' ? $diyformdata[$key]['province'] : '') . " - " . ($diyformdata[$key]['city'] != '请选择城市' ? $diyformdata[$key]['city'] : '');
                }
                $diyformfields[] = array(
                    'tp_name' => $value['tp_name'],
                    "tp_value" => $tp_value,
                    'tp_css' => $tp_css
                );
            }
            $g['diyformfields'] = $diyformfields;
            $g['diyformdata']   = $diyformdata;
            if (!empty($g['diyformdata'])) {
                $diyform_flag = 1;
            }
        } else {
            $g['diyformfields'] = array();
            $g['diyformdata']   = array();
        }
        unset($g);
    }
}
if ($_W['isajax']) {
    if (empty($order)) {
        return show_json(0);
    }
    $order['virtual_str']     = str_replace("\n", "<br/>", $order['virtual_str']);
    $order['goodstotal']      = count($goods);
    $order['createtime']      = date('Y-m-d H:i:s', $order['createtime']);
    $order['paytime']         = date('Y-m-d H:i:s', $order['paytime']);
    $order['sendtime']        = date('Y-m-d H:i:s', $order['sendtime']);
    $order['finishtimevalue'] = $order['finishtime'];
    $order['finishtime']      = date('Y-m-d H:i:s', $order['finishtime']);
    $address                  = false;
    $carrier                  = false;
    $stores                   = array();
    if ($order['isverify'] == 1) {
        $storeids = array();
        foreach ($goods as $g) {
            if (!empty($g['storeids'])) {
                $storeids = array_merge(explode(',', $g['storeids']), $storeids);
            }
        }
        if (empty($storeids)) {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        if ($order['dispatchtype'] == 0) {
            $address = iunserializer($order['address']);
            if (!is_array($address)) {
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(
                    ':id' => $order['addressid']
                ));
            }
        }
    } else {
        if ($order['dispatchtype'] == 0) {
            $address = iunserializer($order['address']);
            if (!is_array($address)) {
                $address = pdo_fetch('select realname,mobile,address from ' . tablename('sz_yi_member_address') . ' where id=:id limit 1', array(
                    ':id' => $order['addressid']
                ));
            }
        }
    }
    if ($order['dispatchtype'] == 1 || $order['isverify'] == 1 || !empty($order['virtual'])) {
        $carrier = unserialize($order['carrier']);
    }
    $set       = set_medias(m('common')->getSysset('shop'), 'logo');
    $canrefund = false;
    $tradeset   = m('common')->getSysset('trade');
    $refunddays = intval($tradeset['refunddays']);
    if (($order['status'] == 1 || $order['status'] == 2) && $order['plugin'] == "") {
        if ($refunddays > 0 || $order['status'] == 1) {
            $canrefund = true;
        }
    } else if ($order['status'] == 3 && $order['plugin'] == "") {
        if ($order['isverify'] != 1 && empty($order['virtual'])) {
            
            if ($refunddays > 0) {
                $days = intval((time() - $order['finishtimevalue']) / 3600 / 24);
                if ($days <= $refunddays) {
                    $canrefund = true;
                }
            }
        }
    }
    if ($order['cashier']){
        $canrefund = false;
    }
    $order['canrefund'] = $canrefund;
    if ($canrefund == true) {
        if ($order['status'] == 1) {
            $order['refund_button'] = '申请退款';
        } else {
            $order['refund_button'] = '申请售后';
        }
        if (!empty($order['refundstate'])) {
            $order['refund_button'] .= '中';
        }
    }
    $variable = array(
        'show'=>$show,
        'diyform_flag'=>$diyform_flag,
        'goods'=>$goods
    );
    return show_json(1, array(

        'order' => $order,
        'goods' => $goods,
        'address' => $address,
        'carrier' => $carrier,
        'stores' => $stores,
        'isverify' => $isverify,
        'set' => $set
    ),$variable);}
if(p('hotel')){
    if($order['order_type']=='3'){
          include $this->template('order/detail_hotel');
      }else{
        include $this->template('order/detail');
      }
}else{
  include $this->template('order/detail');  
}

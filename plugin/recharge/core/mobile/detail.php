<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
@session_start();
setcookie('preUrl', $_W['siteurl']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid         = m('user')->getOpenid();
$popenid        = m('user')->islogin();
$openid         = $openid?$openid:$popenid;
$member         = m('member')->getMember($openid);
$uniacid        = $_W['uniacid'];
$goodsid        = intval($_GPC['id']);
$rechargeset =    p('recharge')->getSet();
$advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('sz_yi_recharge_adv') . ' where uniacid=:uniacid and isshow=1 order by displayorder desc', array(':uniacid' => $uniacid));
foreach($advs as $key => $adv){
    if(!empty($advs[$key]['thumb'])){
        $adv[] = $advs[$key];
    }
}
$advs = set_medias($advs, 'thumb');
    //print_R($advs);exit;
$params = array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid);
$sql = 'SELECT count(id) FROM ' . tablename('sz_yi_order_comment') . ' where 1 and uniacid = :uniacid and goodsid=:goodsid and deleted=0 ORDER BY `id` DESC';
$commentcount = pdo_fetchcolumn($sql, $params);
$goods          = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :id limit 1", array(
    ':id' => $goodsid
));
$options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('sz_yi_goods_option') . " where goodsid=:id order by id asc", array(
    ':id' => $goodsid
));
//print_R($options);exit;
$shop           = set_medias(m('common')->getSysset('shop'), 'logo');
$shop['url']    = $this->createMobileUrl('shop');
$mid            = intval($_GPC['mid']);
$shopset = m('common')->getSysset('shop');
$levelid           = $member['level'];
$groupid           = $member['groupid'];
//if(!is_weixin()){
//禁止浏览的商品
if ($goods['showlevels'] != '') {
    $showlevels = explode(',', $goods['showlevels']);
    if (!in_array($levelid, $showlevels)) {
        message('当前商品禁止访问，请联系客服……', $this->createMobileUrl('shop/index'), 'error');
    }
}
if ($goods['showgroups'] != '') {
    $showgroups = explode(',', $goods['showgroups']);
    if (!in_array($groupid, $showgroups)) {
        message('当前商品禁止访问，请联系客服……', $this->createMobileUrl('shop/index'), 'error');
    }
}
//}
//分销佣金
$commissionprice = p('commission')->getCommission($goods);
if ($_W['isajax']) {
    if ($operation == 'can_buy') {
        $id = intval($_GPC['id']);
        $can_buy_goods = pdo_fetch(" SELECT id,dispatchsend,isverifysend,storeids,isverify FROM " .tablename('sz_yi_goods'). " WHERE id=:id and uniacid=:uniacid", array(':id' => $id, ':uniacid' => $_W['uniacid']));
        if ($can_buy_goods['isverify'] == 2) {
            $a = 0;
            if ($can_buy_goods['isverifysend'] == 1) {
                $a += 1;
            }
            if ($can_buy_goods['dispatchsend'] == 1) {
                $a += 1;
            }
            if (empty($can_buy_goods['storeids'])) {
                $store_all = pdo_fetchall(" SELECT id FROM " .tablename('sz_yi_store'). " WHERE uniacid=:uniacid and myself_support=1", array(':uniacid' => $_W['uniacid']));
                if (!empty($store_all) && is_array($store_all)) {
                    $a += 1;
                }
            } else {
                $storeids = explode(',', $can_buy_goods['storeids']);
                $store = pdo_fetchall(" SELECT id FROM " .tablename('sz_yi_store'). " WHERE uniacid=:uniacid and myself_support=1 and id in (".implode(',', $storeids).")", array(':uniacid' => $_W['uniacid']));
                if (!empty($store) && is_array($store)) {
                    $a += 1;
                }

            }
            if ($a == 0) {
                show_json(0, '抱歉！因为此商品不支持任何配送方式，故暂不支持购买，请联系运营人员了解详情');
            } else {
                show_json(1);
            }
        }

    }
    if (p('channel')) {
        $ischannelpay   = intval($_GPC['ischannelpay']);
        $ischannelpick  = intval($_GPC['ischannelpick']);
    }
    if (empty($goods)) {
        show_json(0);
    }
    $goods              = set_medias($goods, 'thumb');
    if (p('yunbi')) {
        $yunbi_set = p('yunbi')->getSet();
        if (!empty($yunbi_set['isdeduct']) && !empty($goods['isforceyunbi']) && $member['virtual_currency'] < $goods['yunbi_deduct']) {
            $goods['isforce'] = '';
        } else {
            $goods['isforce'] = '1';
        }
        if (!empty($goods['yunbi_deduct'])) {
            $goods['yunbi_num'] = $goods['yunbi_deduct']/$yunbi_set['money'];
        }
    } else {
        $goods['isforce'] = '1';
    }
    $goods['canbuy']    = !empty($goods['status']) && empty($goods['deleted']);
    $goods['timestate'] = '';
    $goods['userbuy']   = '1';
    if ($goods['usermaxbuy'] > 0) {
        $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ', array(
            ':goodsid' => $goodsid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        if ($order_goodscount >= $goods['usermaxbuy']) {
            $goods['userbuy'] = 0;
        }
    }


    $goods['levelbuy'] = '1';
    if ($goods['buylevels'] != '') {
        $buylevels = explode(',', $goods['buylevels']);
        if (!in_array($levelid, $buylevels)) {
            $goods['levelbuy'] = 0;
        }
    }
    $goods['groupbuy'] = '1';
    if ($goods['buygroups'] != '') {
        $buygroups = explode(',', $goods['buygroups']);
        if (!in_array($groupid, $buygroups)) {
            $goods['groupbuy'] = 0;
        }
    }
    $goods['timebuy'] = '0';
    if ($goods['istime'] == 1) {
        if (time() < $goods['timestart']) {
            $goods['timebuy']   = '-1';
            $goods['timestate'] = "before";
            $goods['buymsg']    = "限时购活动未开始";
        } else if (time() > $goods['timeend']) {
            $goods['timebuy'] = '1';
            $goods['buymsg']  = '限时购活动已经结束';
        } else {
            $goods['timestate'] = 'after';
        }
    }
    $goods['canaddcart'] = true;
    if ($goods['isverify'] == 2 || $goods['type'] == 2 || $goods['type'] == 3) {
        $goods['canaddcart'] = false;
    }
    $pics     = array(
        $goods['thumb']
    );
    $thumburl = unserialize($goods['thumb_url']);
    if (is_array($thumburl)) {
        $pics = array_merge($pics, $thumburl);
    }
    unset($thumburl);
    $pics         = set_medias($pics);
    $marketprice  = $goods['marketprice'];
    $productprice = $goods['productprice'];
    $maxprice     = $marketprice;
    $minprice     = $marketprice;
    $stock        = $goods['total'];
    $allspecs     = array();
    if (!empty($goods['hasoption'])) {
        $allspecs = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec') . " where goodsid=:id order by displayorder asc", array(
            ':id' => $goodsid
        ));
        foreach ($allspecs as &$s) {
            $items      = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(
                ":specid" => $s['id']
            ));
            if (!empty($ischannelpick) && p('channel')) {
                $items = array();
                $my_stock = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND goodsid={$goodsid}");
                if (!empty($my_stock)) {
                    $items = array();
                    foreach ($my_stock as $op) {
                        if (!empty($op['optionid'])) {
                            $my_option = m('goods')->getOption($goodsid, $op['optionid']);
                            //$spec = pdo_fetch('select * from ' . tablename('sz_yi_goods_spec') . " where uniacid={$_W['uniacid']} and goodsid={$goodsid} and id={$my_option['specs']}");
                            $items[] = pdo_fetch("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and id=:id order by displayorder asc", array(
                                ":id" => $my_option['specs']
                            ));
                        }
                    }
                }
            }
            $s['items'] = set_medias($items, 'thumb');
        }
        unset($s);
    }
    $options = array();
    if (!empty($goods['hasoption'])) {
        $options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('sz_yi_goods_option') . " where goodsid=:id order by id asc", array(
            ':id' => $goodsid
        ));
        print_R($options);exit;
        if (!empty($ischannelpay) && p('channel')) {
            foreach ($options as &$value) {
                $superior_stock = p('channel')->getSuperiorStock($openid, $goodsid, $value['id']);
                if (!empty($superior_stock['stock_total'])) {
                    $value['stock'] = $superior_stock['stock_total'];
                }
            }
            unset($value);
        }

        if (!empty($ischannelpick) && p('channel')) {
            $options = array();
            $my_stock = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND goodsid={$goodsid}");
            foreach ($my_stock as $val) {
                $my_option          = m('goods')->getOption($goodsid, $val['optionid']);
                $stock_total        = pdo_fetchcolumn("SELECT stock_total FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND goodsid={$goodsid} AND optionid={$val['optionid']}");
                $my_option['stock'] = $stock_total;
                $options[]          = $my_option;
            }
            /*foreach ($options as &$value) {
                $my_stock = pdo_fetch("SELECT * FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}' AND goodsid={$goodsid} AND optionid={$value['id']}");
                if (empty($my_stock)) {
                    unset($options[$k]);
                } else {
                    $value['stock'] = $my_stock['stock_total'];
                }
            }
            unset($value);*/
        } elseif (!empty($_GPC['storeid'])) {
            $options = array();
            $my_stock = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_store_goods') . " WHERE uniacid=:uniacid AND storeid=:storeid AND goodsid=:goodsid", array(':uniacid' => $_W['uniacid'], ':storeid' => intval($_GPC['storeid']), ':goodsid' => $goodsid));
            foreach ($my_stock as $val) {
                $my_option          = m('goods')->getOption($goodsid, $val['optionid']);
                $stock_total        = pdo_fetchcolumn("SELECT total FROM " . tablename('sz_yi_store_goods') . " WHERE uniacid=:uniacid AND goodsid=:goodsid AND optionid=:optionid and storeid=:storeid", array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid, ':optionid' => $val['optionid'], ':storeid' => intval($_GPC['storeid'])));
                $my_option['stock'] = $stock_total;
                $options[]          = $my_option;
            }
        }
        $options = set_medias($options, 'thumb');
        foreach ($options as $o) {
            if ($maxprice < $o['marketprice']) {
                $maxprice = $o['marketprice'];
            }
            if ($minprice > $o['marketprice'] && $o['marketprice'] > 0) {
                $minprice = $o['marketprice'];
            }
        }
        $goods['maxprice'] = $maxprice;
        $goods['minprice'] = $minprice;
    }

    $specs  = $allspecs;
    $params = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods_param') . " WHERE uniacid=:uniacid and goodsid=:goodsid order by displayorder asc", array(
        ':uniacid' => $uniacid,
        ":goodsid" => $goods['id']
    ));
    $fcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_favorite') . ' where uniacid=:uniacid and openid=:openid and goodsid=:goodsid and deleted=0 ', array(
        ':uniacid' => $uniacid,
        ':openid' => $openid,
        ':goodsid' => $goods['id']
    ));
    pdo_query('update ' . tablename('sz_yi_goods') . " set viewcount=viewcount+1 where id=:id and uniacid='{$uniacid}' ", array(
        ":id" => $goodsid
    ));
    $history = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_history') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0 limit 1', array(
        ':goodsid' => $goodsid,
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));

    //我的足迹
    $history_goods = set_medias(pdo_fetchall('select g.* from ' . tablename('sz_yi_member_history') . ' h '.' left join '.tablename('sz_yi_goods').' g on h.goodsid = g.id  where  h.uniacid=:uniacid and h.openid=:openid and h.deleted=0 and g.deleted = 0  order by h.createtime desc limit 5', array(
        ':uniacid' => $uniacid,
        ':openid' => $openid
    )),'thumb');
    if ($history <= 0) {
        $history = array(
            'uniacid' => $uniacid,
            'openid' => $openid,
            'goodsid' => $goodsid,
            'deleted' => 0,
            'createtime' => time()
        );
        pdo_insert('sz_yi_member_history', $history);
    }

    //是否折扣权限
    if ($goods['discountway'] && $goods['discounttype']) {
        $comp_value = ($goods['discountway'] == 1) ? 10 : $goods['marketprice'];

        //会员OR分销商
        if ($goods['discounttype'] == 1) {
            $level     = m('member')->getLevel($openid);

            $levelname = "普通会员";
            $discounts = json_decode($goods['discounts'], true);
            $level['discounttxt'] = ($goods['discountway'] == 1) ? "会员折扣" : "会员立减";
        } else {
            $level     = p("commission")->getLevel($openid);
            $levelname = "普通等级";
            $discounts = json_decode($goods['discounts2'], true);
            $level['discounttxt'] = ($goods['discountway'] == 1) ? "分销商折扣" : "分销商立减";
        }

        $level['discount'] = 0;
        if ($goods['discountway'] == 1) {
            $level['discount'] = 10;
        }

        $level['levelname'] = empty($level['levelname']) ? $levelname : $level['levelname'];
        //会员等级折扣
        if (($member['isagent'] == 1 && $member['status'] == 1) || $goods['discounttype'] == 1) {
            if (is_array($discounts)) {
                if (!empty($level['id'])) {
                    if ($discounts['level' . $level['id']] > 0 && $discounts['level' . $level['id']] < $comp_value) {
                        $level['discount'] = $discounts['level' . $level['id']];
                    }
                } else {
                    if ($discounts['default'] > 0 && $discounts['default'] < $comp_value) {
                        $level['discount'] = $discounts['default'];
                    }
                }
            }
        }
    }
    $level['discountway'] = $goods['discountway'];

    $comment = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods_comment')." where goodsid=:id and uniacid=:uniacid",array(':id' => $goodsid , ':uniacid' => $uniacid)),'headimgurl');
    $commentcount = pdo_fetchcolumn("select count(id) from ".tablename('sz_yi_goods_comment')." where goodsid=:id and uniacid=:uniacid",array(':id' => $goodsid , ':uniacid' => $uniacid));

    //热卖商品
    if($goods['tcate']){
        $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where tcate=:tcate and pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0   order by sales desc limit 10",array(':uniacid' => $uniacid , ':tcate' => $goods['tcate'] , ':pcate' => $goods['pcate'] , ':ccate' => $goods['ccate'])),'thumb');
    }else if ($goods['ccate']){
        $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0 order by sales desc limit 10",array(':uniacid' => $uniacid , ':pcate' => $goods['pcate'] , ':ccate' => $goods['ccate'])),'thumb');
    }else if ($goods['pcate']){
        $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where pcate=:pcate  and uniacid=:uniacid and deleted = 0 order by sales desc limit 10",array(':uniacid' => $uniacid , ':pcate' => $goods['pcate'] )),'thumb');
    }else{
        $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where uniacid=:uniacid and deleted = 0 order by sales desc limit 10",array(':uniacid' => $uniacid )),'thumb');
    }

    $category = m('shop')->getCategory();

    $stores = array();
    if ($goods['isverify'] == 2) {
        $storeids = array();
        if (!empty($goods['storeids'])) {
            $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
        }
        if (empty($storeids)) {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1 and myself_support=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and myself_support=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
    }
    $followed    = m('user')->followed($openid);
    $followurl   = empty($goods['followurl']) ? $shop['followurl'] : $goods['followurl'];
    $followtip   = empty($goods['followtip']) ? '如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~' : $goods['followtip'];
    $sale_plugin = p('sale');
    $saleset     = false;
    if ($sale_plugin) {
        $saleset            = $sale_plugin->getSet();
        $saleset['enoughs'] = $sale_plugin->getEnoughs();
    }
    
    $ret        = array(
        'is_admin' => $_GPC['is_admin'],
        'goods' => $goods,
        'indiana' => $indiana,
        'followed' => $followed ? 1 : 0,
        'followurl' => $followurl,
        'followtip' => $followtip,
        'saleset' => $saleset,
        'shopset' => $shopset,
        'pics' => $pics,
        'options' => $options,
        'specs' => $specs,
        'params' => $params,
        'commission' => $opencommission,
        'commission_text' => $commission_text,
        'level' => $level,
        'shop' => $shop,
        'goodscount' => pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and status=1 and deleted=0 ', array(
            ':uniacid' => $uniacid
        )),
        'cartcount' => pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        )),
        'isfavorite' => $fcount > 0,
        'stores' => $stores,
        'comment' => $comment,
        'commentcount' => $commentcount,
        'ishot' => $ishot,
        'history' => $history_goods,
        'category' => $category
    );
    $commission = p('commission');
    if ($commission) {
        $shopid = $shop['mid'];
        if (!empty($shopid)) {
            $myshop = set_medias($commission->getShop($shopid), array(
                'img',
                'logo'
            ));
        }
    }
    if (!empty($myshop['selectgoods']) && !empty($myshop['goodsids'])) {
        $ret['goodscount'] = count(explode(",", $myshop['goodsids']));
    }
    $ret['detail'] = array(
        'logo' => !empty($goods['detail_logo']) ? tomedia($goods['detail_logo']) : $shop['logo'],
        'shopname' => !empty($goods['detail_shopname']) ? $goods['detail_shopname'] : $shop['name'],
        'totaltitle' => trim($goods['detail_totaltitle']),
        'btntext1' => trim($goods['detail_btntext1']),
        'btnurl1' => !empty($goods['detail_btnurl1']) ? $goods['detail_btnurl1'] : $this->createMobileUrl('shop/list'),
        'btntext2' => trim($goods['detail_btntext2']),
        'btnurl2' => !empty($goods['detail_btnurl2']) ? $goods['detail_btnurl2'] : $shop['url']
    );

    show_json(1, $ret);

}

$this->setHeader();

include $this->template('detail');


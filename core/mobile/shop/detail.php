<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
@session_start();
setcookie('preUrl', $_W['siteurl']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$popenid = m('user')->islogin();
$openid = $openid ? $openid : $popenid;
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];
$goodsid = intval($_GPC['id']);
$params = array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodsid);
$sql = 'SELECT count(id) FROM ' . tablename('sz_yi_order_comment') . ' where 1 and uniacid = :uniacid and goodsid=:goodsid and deleted=0 ORDER BY `id` DESC';
$commentcount = pdo_fetchcolumn($sql, $params);
$goods = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :id limit 1", array(
    ':id' => $goodsid
));

if ($goods['pcate']) {
    $pcate = pdo_fetchcolumn(" select name from " . tablename('sz_yi_category') . " where id =" . $goods['pcate'] . " and uniacid=" . $uniacid);
}
if ($goods['ccate']) {
    $ccate = pdo_fetchcolumn(" select name from " . tablename('sz_yi_category') . " where id =" . $goods['ccate'] . " and uniacid=" . $uniacid);
}
if ($goods['tcate']) {
    $tcate = pdo_fetchcolumn(" select name from " . tablename('sz_yi_category') . " where id =" . $goods['tcate'] . " and uniacid=" . $uniacid);
}
$yunbi = 0;
if (p('yunbi')) {
    $yunbi = 1;
    $yunbi_set = p('yunbi')->getSet();
}

$goods['isladder'] = false;
if (p('ladder')) {
    $ladder_set = p('ladder')->getSet();
    if ($ladder_set['isladder']) {
        $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
            ':id' => $goodsid
        ));
        if ($ladders) {
            $goods['ladders'] = unserialize($ladders['ladders']);
            $goods['isladder'] = true; 
        }
    }
}
if (p('hotel')) {//开启酒店插件后 判断当前时间是否有剩余房间可预约
    $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
    $params2 = array(':goodsid' => $goods['id']);
    $room = pdo_fetch($sql2, $params2);
//入店
    $btime = $_SESSION['data']['btime'];
    $bdate = $_SESSION['data']['bdate'];
// 住几天
    $days = intval($_SESSION['data']['day']);
// 离店
    $etime = $_SESSION['data']['etime'];
    $edate = $_SESSION['data']['edate'];

    $r_sql = 'SELECT * FROM ' . tablename('sz_yi_hotel_room_price') .
        ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
        ' `roomdate` < :etime';
    $params = array(':roomid' => $room['id'], ':btime' => $btime, ':etime' => $etime);
    $price_list = pdo_fetch($r_sql, $params);
    $goods['has'] = 0;
    if ($price_list) {
        if (is_array($price_list[0])) {
            foreach ($price_list as $k => $v) {
                if ($v['status'] == 0 || $v['num'] == 0) {
                    $goods['has'] += 1;   //不可预约
                }
            }
        } else {
            if ($price_list['status'] == 0 || $price_list['num'] == 0) {
                $goods['has'] += 1;   //不可预约
            }
            if ($price_list['cprice'] != '0.00') {
                $goods['marketprice'] = $price_list['oprice'];
            }
        }
    }
}
$shop = set_medias(m('common')->getSysset('shop'), 'logo');
$shop['url'] = $this->createMobileUrl('shop');
$mid = intval($_GPC['mid']);
$shopset = m('common')->getSysset('shop');
$opencommission = false;
if (p('commission')) {
    if (empty($member['agentblack'])) {
        $cset = p('commission')->getSet();
        $opencommission = intval($cset['level']) > 0;
        if ($opencommission) {
            if (empty($mid)) {
                if ($member['isagent'] == 1 && $member['status'] == 1) {
                    $mid = $member['id'];
                }
            }
            if (!empty($mid)) {
                if (empty($cset['closemyshop'])) {
                    $shop = set_medias(p('commission')->getShop($mid), 'logo');
                    $shop['url'] = $this->createPluginMobileUrl('commission/myshop', array(
                        'mid' => $mid
                    ));
                }
            }
            $commission_text = empty($cset['buttontext']) ? '我要分销' : $cset['buttontext'];
        }
    }
}
$showdiyform = 0;
$diyform_plugin = p('diyform');
if ($diyform_plugin) {
    $diyformtype = $goods['diyformtype'];
    $diyformid = $goods['diyformid'];
    $diymode = $goods['diymode'];
    if (!empty($diyformtype) && !empty($diyformid)) {
        $formInfo = $diyform_plugin->getDiyformInfo($diyformid);
        $fields = $formInfo['fields'];
        $f_data = $diyform_plugin->getLastData(3, $diymode, $diyformid, $goodsid, $fields, $member);
    }
    if ($_W['isajax'] && $_GPC['op'] == 'create') {
        if (!empty($_GPC['diydata'])) {
            $insert_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);
            $idata = $insert_data['data'];

        }
        $goods_temp = $diyform_plugin->getGoodsTemp($goodsid, $diyformid, $openid);
        if (!empty($_GPC['declaration_mid'])) {
            $declaration_mid = $_GPC['declaration_mid'];
        }
        $insert = array(
            'cid' => $goodsid,
            'openid' => $openid,
            'diyformid' => $diyformid,
            'type' => 3,
            'diyformfields' => iserializer($fields),
            'diyformdata' => $idata,
            'declaration_mid' => $declaration_mid,
            'uniacid' => $_W['uniacid']
        );
        if (empty($goods_temp)) {
            pdo_insert('sz_yi_diyform_temp', $insert);
            $gdid = pdo_insertid();
        } else {
            pdo_update('sz_yi_diyform_temp', $insert, array(
                'id' => $goods_temp['id']
            ));
            $gdid = $goods_temp['id'];
        }
        return show_json(1, array(
            'goods_data_id' => $gdid
        ));
    }

    if ($_W['isajax'] && $_GPC['op'] == 'getmember') {
        if (!empty($_GPC['mid'])) {
            $condition = ' and ( id = :mid or realname like :mid or nickname like :mid or mobile like :mid )';
            $declaration = pdo_fetch("SELECT * FROM " . tablename('sz_yi_member') . " WHERE 1 {$condition}",
                array(
                    ':mid' => $_GPC['mid']
                ));
            if ($declaration) {
                return show_json(1, array('mid' => $declaration['id']));
            } else {
                return show_json(0, '用户信息不存在');
            }

        } else {
            return show_json(1, array('mid' => ''));
        }
    }
}
$pindiana = p('indiana');
$indiana = array();

// $pindiana->test('20161018949435241998');
// echo "<pre>";print_r(2);exit;
if ($pindiana && $_GPC['indiana']) {
    $periodnum = $_GPC['periodnum'];
    $condition = ' and ig.uniacid = :uniacid AND ig.status = 2 AND ig.good_id = :goodsid AND ip.period_num = :periodnum';
    $params    = array(
        ':uniacid' => $_W['uniacid'],
        ':goodsid' => $goodsid,
        ':periodnum' => $periodnum
    );
    $indiana = set_medias(pdo_fetch("SELECT ig.*, g.thumb, ip.period, ip.shengyu_codes, ip.zong_codes, ip.period_num, ip.init_money as initmoney , ip.status  as ipstatus, ip.avatar, ip.nickname, ip.mid, ip.code, ip.partakes, ip.jiexiao_time FROM " . tablename('sz_yi_indiana_goods') . " ig 
        left join " . tablename('sz_yi_goods') . " g on (ig.good_id = g.id) 
        left join " . tablename('sz_yi_indiana_period') . " ip on (ig.id = ip.ig_id)
        where 1 {$condition} " , $params),'thumb');
        if ($indiana) {
            $indiana['shengyu'] = $indiana['shengyu_codes']/$indiana['zong_codes']*100;
            $indiana['jiexiao'] =  $indiana['jiexiao_time'];//$indiana['jiexiao_time']?date("Y-m-d H:i:s",$indiana['jiexiao_time']):'';
            $indiana['now_time'] = time();
        }
        //下一期
        $next = $indiana['period'];
        $next_phase = pdo_fetch("SELECT goodsid, period_num FROM " . tablename('sz_yi_indiana_period') . " where goodsid = '".$goodsid."' and period > '" . $next . "' ORDER BY period desc limit 1");  

        $indiana['dz'] = "选择地址1";

        $indiana['address']      = pdo_fetch('select id,realname,mobile,address,province,city,area from ' . tablename('sz_yi_member_address') . ' WHERE openid=:openid AND deleted=0 AND isdefault=1  AND uniacid=:uniacid limit 1', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
}
$html = $goods['content'];
preg_match_all("/<img.*?src=[\'| \"](.*?(?:[\.gif|\.jpg]?))[\'|\"].*?[\/]?>/", $html, $imgs);
if (isset($imgs[1])) {
    foreach ($imgs[1] as $img) {
        $im = array(
            "old" => $img,
            "new" => tomedia($img)
        );
        $images[] = $im;
    }
    if (isset($images)) {
        foreach ($images as $img) {
            $html = str_replace($img['old'], $img['new'], $html);
        }
    }
    $goods['content'] = $html;
}
$levelid = $member['level'];
$groupid = $member['groupid'];
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
if(p('commission')){
    $commissionprice = p('commission')->getCommission($goods);
}
$_W['shopshare'] = array(
    'title' => !empty($goods['share_title']) ? $goods['share_title'] : $goods['title'],
    'imgUrl' => !empty($goods['share_icon']) ? tomedia($goods['share_icon']) : tomedia($goods['thumb']),
    'desc' => !empty($goods['description']) ? $goods['description'] : $shop['name'],
    'link' => $this->createMobileUrl('shop/detail', array(
        'id' => $goods['id']
    ))
);
if ($_W['isajax']) {
    if ($operation == 'can_buy') {
        $id = intval($_GPC['id']);
        $can_buy_goods = pdo_fetch(" SELECT id,dispatchsend,isverifysend,storeids,isverify FROM " . tablename('sz_yi_goods') . " WHERE id=:id and uniacid=:uniacid",
            array(':id' => $id, ':uniacid' => $_W['uniacid']));
        if ($can_buy_goods['isverify'] == 2) {
            $a = 0;
            if ($can_buy_goods['isverifysend'] == 1) {
                $a += 1;
            }
            if ($can_buy_goods['dispatchsend'] == 1) {
                $a += 1;
            }
            if (empty($can_buy_goods['storeids'])) {
                $store_all = pdo_fetchall(" SELECT id FROM " . tablename('sz_yi_store') . " WHERE uniacid=:uniacid and myself_support=1",
                    array(':uniacid' => $_W['uniacid']));
                if (!empty($store_all) && is_array($store_all)) {
                    $a += 1;
                }
            } else {
                $storeids = explode(',', $can_buy_goods['storeids']);
                $store = pdo_fetchall(" SELECT id FROM " . tablename('sz_yi_store') . " WHERE uniacid=:uniacid and myself_support=1 and id in (" . implode(',',
                        $storeids) . ")", array(':uniacid' => $_W['uniacid']));
                if (!empty($store) && is_array($store)) {
                    $a += 1;
                }

            }
            if ($a == 0) {
                return show_json(0, '抱歉！因为此商品不支持任何配送方式，故暂不支持购买，请联系运营人员了解详情');
            } else {
                return show_json(1);
            }
        }

    }
    if (p('channel')) {
        $ischannelpay = intval($_GPC['ischannelpay']);
        $ischannelpick = intval($_GPC['ischannelpick']);
    }
    if (empty($goods)) {
        return show_json(0);
    }
    $goods = set_medias($goods, 'thumb');
    if (p('yunbi')) {
        $yunbi_set = p('yunbi')->getSet();
        if (!empty($yunbi_set['isdeduct']) && !empty($goods['isforceyunbi']) && $member['virtual_currency'] < $goods['yunbi_deduct']) {
            $goods['isforce'] = '';
        } else {
            $goods['isforce'] = '1';
        }
        if (!empty($goods['yunbi_deduct']) && !empty($yunbi_set['money'])) {
            $goods['yunbi_num'] = $goods['yunbi_deduct'] / $yunbi_set['money'];
        }
    } else {
        $goods['isforce'] = '1';
    }

    //阶梯价格计算
    if ($goods['isladder']) {
        $laddermoney = m('goods')->getLaderMoney($goods['ladders'],'1');
        $goods['marketprice'] = $laddermoney > 0 ? $laddermoney : $goods['marketprice'];
    }

    $goods['canbuy'] = !empty($goods['status']) && empty($goods['deleted']);
    $goods['timestate'] = '';
    $goods['userbuy'] = '1';
    if ($goods['usermaxbuy'] > 0) {
        $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=1 and o.openid=:openid  and og.uniacid=:uniacid ',
            array(
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
            $goods['timebuy'] = '-1';
            $goods['timestate'] = "before";
            $goods['buymsg'] = "限时购活动未开始";
        } else {
            if (time() > $goods['timeend']) {
                $goods['timebuy'] = '1';
                $goods['buymsg'] = '限时购活动已经结束';
            } else {
                $goods['timestate'] = 'after';
            }
        }
    }
    $goods['current'] = time();//限时购倒计时 当前时间
    $goods['canaddcart'] = true;
    if ($goods['isverify'] == 2 || $goods['type'] == 2 || $goods['type'] == 3) {
        $goods['canaddcart'] = false;
    }
    $pics = array(
        $goods['thumb']
    );
    $thumburl = unserialize($goods['thumb_url']);
    if (is_array($thumburl)) {
        $pics = array_merge($pics, $thumburl);
    }
    unset($thumburl);
    $pics = set_medias($pics);
    $marketprice = $goods['marketprice'];
    $productprice = $goods['productprice'];
    $maxprice = $marketprice;
    $minprice = $marketprice;
    $stock = $goods['total'];
    $allspecs = array();
    if (!empty($goods['hasoption'])) {
        $allspecs = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec') . " where goodsid=:id order by displayorder asc",
            array(
                ':id' => $goodsid
            ));
        foreach ($allspecs as &$s) {
            $items = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc",
                array(
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
                            $items[] = pdo_fetch("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and id=:id order by displayorder asc",
                                array(
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
        $options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs,option_ladders from " . tablename('sz_yi_goods_option') . " where goodsid=:id order by id asc",
            array(
                ':id' => $goodsid
            ));
        //echo "<pre>";print_r($options);exit;
            //阶梯价格计算
            if ($goods['isladder']) {
                foreach ($options as &$value) {
                    $laddermoney = m('goods')->getLaderMoney(unserialize($value['option_ladders']),'1');
                    $value['marketprice'] = $laddermoney > 0 ? $laddermoney : $value['marketprice'];
                    
                }
                unset($value);           
            }  

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
                $my_option = m('goods')->getOption($goodsid, $val['optionid']);
                $stock_total = pdo_fetchcolumn("SELECT stock_total FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} AND goodsid={$goodsid} AND optionid={$val['optionid']}");
                $my_option['stock'] = $stock_total;
                $options[] = $my_option;
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
            $my_stock = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_store_goods') . " WHERE uniacid=:uniacid AND storeid=:storeid AND goodsid=:goodsid",
                array(':uniacid' => $_W['uniacid'], ':storeid' => intval($_GPC['storeid']), ':goodsid' => $goodsid));
            foreach ($my_stock as $val) {
                $my_option = m('goods')->getOption($goodsid, $val['optionid']);
                $stock_total = pdo_fetchcolumn("SELECT total FROM " . tablename('sz_yi_store_goods') . " WHERE uniacid=:uniacid AND goodsid=:goodsid AND optionid=:optionid and storeid=:storeid",
                    array(
                        ':uniacid' => $_W['uniacid'],
                        ':goodsid' => $goodsid,
                        ':optionid' => $val['optionid'],
                        ':storeid' => intval($_GPC['storeid'])
                    ));
                $my_option['stock'] = $stock_total;
                $options[] = $my_option;
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
    $specs = $allspecs;
    $params = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods_param') . " WHERE uniacid=:uniacid and goodsid=:goodsid order by displayorder asc",
        array(
            ':uniacid' => $uniacid,
            ":goodsid" => $goods['id']
        ));
    $fcount = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_favorite') . ' where uniacid=:uniacid and openid=:openid and goodsid=:goodsid and deleted=0 ',
        array(
            ':uniacid' => $uniacid,
            ':openid' => $openid,
            ':goodsid' => $goods['id']
        ));
    pdo_query('update ' . tablename('sz_yi_goods') . " set viewcount=viewcount+1 where id=:id and uniacid='{$uniacid}' ",
        array(
            ":id" => $goodsid
        ));
    $history_info = pdo_fetch('select id from ' . tablename('sz_yi_member_history') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0',
        array(
            ':goodsid' => $goodsid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));

    //我的足迹
    $history_goods = set_medias(pdo_fetchall('select g.* from ' . tablename('sz_yi_member_history') . ' h ' . ' left join ' . tablename('sz_yi_goods') . ' g on h.goodsid = g.id  where  h.uniacid=:uniacid and h.openid=:openid and h.deleted=0 and g.deleted = 0  order by h.createtime desc limit 5',
        array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        )), 'thumb');
    if (empty($history_info)) {
        $history = array(
            'uniacid' => $uniacid,
            'openid' => $openid,
            'goodsid' => $goodsid,
            'deleted' => 0,
            'createtime' => time()
        );
        pdo_insert('sz_yi_member_history', $history);
    }else{
        pdo_update('sz_yi_member_history', array("utime" => time()), array("id" => $history_info['id']));
    }

    //是否折扣权限
    if ($goods['discountway'] && $goods['discounttype']) {
        $comp_value = ($goods['discountway'] == 1) ? 10 : $goods['marketprice'];

        //会员OR分销商
        if ($goods['discounttype'] == 1) {
            $level = m('member')->getLevel($openid);

            $levelname = "普通会员";
            $discounts = json_decode($goods['discounts'], true);
            $level['discounttxt'] = ($goods['discountway'] == 1) ? "会员折扣" : "会员立减";
        } else {
            $level = p("commission")->getLevel($openid);
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

    $comment = set_medias(pdo_fetchall("select * from " . tablename('sz_yi_goods_comment') . " where goodsid=:id and uniacid=:uniacid",
        array(':id' => $goodsid, ':uniacid' => $uniacid)), 'headimgurl');
    $commentcount = pdo_fetchcolumn("select count(id) from " . tablename('sz_yi_goods_comment') . " where goodsid=:id and uniacid=:uniacid",
        array(':id' => $goodsid, ':uniacid' => $uniacid));

    //热卖商品
    if ($goods['tcate']) {
        $ishot = set_medias(pdo_fetchall("select * from " . tablename('sz_yi_goods') . " where tcate=:tcate and pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0   order by sales desc limit 10",
            array(
                ':uniacid' => $uniacid,
                ':tcate' => $goods['tcate'],
                ':pcate' => $goods['pcate'],
                ':ccate' => $goods['ccate']
            )), 'thumb');
    } else {
        if ($goods['ccate']) {
            $ishot = set_medias(pdo_fetchall("select * from " . tablename('sz_yi_goods') . " where pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0 order by sales desc limit 10",
                array(':uniacid' => $uniacid, ':pcate' => $goods['pcate'], ':ccate' => $goods['ccate'])), 'thumb');
        } else {
            if ($goods['pcate']) {
                $ishot = set_medias(pdo_fetchall("select * from " . tablename('sz_yi_goods') . " where pcate=:pcate  and uniacid=:uniacid and deleted = 0 order by sales desc limit 10",
                    array(':uniacid' => $uniacid, ':pcate' => $goods['pcate'])), 'thumb');
            } else {
                $ishot = set_medias(pdo_fetchall("select * from " . tablename('sz_yi_goods') . " where uniacid=:uniacid and deleted = 0 order by sales desc limit 10",
                    array(':uniacid' => $uniacid)), 'thumb');
            }
        }
    }

    $category = m('shop')->getCategory();

    $stores = array();
    if ($goods['isverify'] == 2) {
        $storeids = array();
        if (!empty($goods['storeids'])) {
            $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
        }
        if (empty($storeids)) {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where  uniacid=:uniacid and status=1 and myself_support=1',
                array(
                    ':uniacid' => $_W['uniacid']
                ));
        } else {
            $stores = pdo_fetchall('select * from ' . tablename('sz_yi_store') . ' where id in (' . implode(',',
                    $storeids) . ') and uniacid=:uniacid and status=1 and myself_support=1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
    }
    $followed = m('user')->followed($openid);
    $followurl = empty($goods['followurl']) ? $shop['followurl'] : $goods['followurl'];
    $followtip = empty($goods['followtip']) ? '如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~' : $goods['followtip'];
    $sale_plugin = p('sale');
    $saleset = false;
    if ($sale_plugin) {
        $saleset = $sale_plugin->getSet();
        $saleset['enoughs'] = $sale_plugin->getEnoughs();
    }

    $opt_switch = $goods['opt_switch'];
    $specs_nums = count($specs);
    $isverify = $goods['isverify'];

    if (!empty($goods['hasoption']) && $goods['isverify'] == 1 && $opt_switch == 1 && $specs_nums == 2) {
        foreach ($options as $opt) {
            $prices[] = $opt['marketprice'];
        }

        $min_price = min($prices);
        $max_price = max($prices);
    } else {
        $min_price = 0;
        $max_price = 0;
    }

    //商品购买数量限制
    if ($goods['usermaxbuy'] > 0) {
        $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' WHERE og.goodsid=:goodsid AND  o.status>=1 AND o.openid=:openid  AND og.uniacid=:uniacid ', array(
            ':goodsid' => $goodsid,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        $last = $goods['usermaxbuy'] - $order_goodscount;
        if ($last <= 0) {
            $last = 0;
        }

        $goods['maxbuy'] = $last;
    }

    $ret = array(
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
        'opt_switch' => $opt_switch,
        'specs_nums' => $specs_nums,
        'min_price' => $min_price,
        'max_price' => $max_price,
        'isverify' => $isverify,
        'goodscount' => pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and status=1 and deleted=0 ',
            array(
                ':uniacid' => $uniacid
            )),
        'cartcount' => pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where uniacid=:uniacid and openid=:openid and deleted=0 ',
            array(
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

    return show_json(1, $ret);

}


$com = p('commission');
if ($com) {
    $cset = $com->getSet();
    if (!empty($cset)) {
        if ($member['isagent'] == 1 && $member['status'] == 1) {
            $_W['shopshare']['link'] = $this->createMobileUrl('shop/detail', array(
                'id' => $goods['id'],
                'mid' => $member['id']
            ));
            if (empty($cset['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) {
                $trigger = true;
            }
        } else {
            if (!empty($_GPC['mid'])) {
                $_W['shopshare']['link'] = $this->createMobileUrl('shop/detail', array(
                    'id' => $goods['id'],
                    'mid' => $_GPC['mid']
                ));
            }
        }
    }
}
if ($pindiana && $_GPC['indiana']) {
    $_W['shopshare']['link'] .= "&periodnum=".$periodnum."&indiana=1";
}
$this->setHeader();
if (p('hotel')) { //判断是否开启酒店插件
    if ($goods['type'] == '99') {//判断是否为房间
        include $this->template('shop/detail_hotel');
    } else {
        if ($goods['type'] == '98') {
            include $this->template('shop/detail_appointment');
        } else {
            if ($goods['type'] == '97') {
                include $this->template('shop/detail_appointment');
            } else {
                include $this->template('shop/detail');
            }
        }
    }
} else {
    include $this->template('shop/detail');
}


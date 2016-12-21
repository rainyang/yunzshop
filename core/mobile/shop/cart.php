<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];

$isladder = false;
if (p('ladder')) {
    $ladder_set = p('ladder')->getSet();
    if ($ladder_set['isladder']) {
        $isladder = true;   
    }
}
if ($_W['isajax']) {
    if(empty($openid) || strstr($openid, 'http-equiv=refresh')){
        return show_json(2, array(
                'message' => '请先登录',
                'url' => $this->createMobileUrl('member/login')
            )); 
    }
    if ($operation == 'display') {
        $ischannelpick = intval($_GPC['ischannelpick']);
        $ischannelpay = intval($_GPC['ischannelpay']);
        if (p('channel')) {
            $my_info = p('channel')->getInfo($openid);
        }
        $condition  = ' and f.uniacid= :uniacid and f.openid=:openid and f.deleted=0';
        $params     = array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        );
        $list       = array();
        $total      = 0;
        $totalprice = 0;
        $channel_condtion = '';
        $yunbi_condtion = '';
        $virtual_currency = '1';
        if (p('channel')) {
            $channel_condtion = 'g.isopenchannel,';
        }
        if (p('yunbi')) {
            $yunbi_condtion = 'g.isforceyunbi,g.yunbi_deduct,';
        }
        $sql        = 'SELECT f.id,f.total,' . $channel_condtion . $yunbi_condtion . 'f.goodsid,g.total as stock, o.stock as optionstock, g.maxbuy, g.usermaxbuy, g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,g.productprice,o.title as optiontitle,f.optionid,o.specs,o.option_ladders FROM ' . tablename('sz_yi_member_cart') . ' f ' . ' left join ' . tablename('sz_yi_goods') . ' g on f.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on f.optionid = o.id ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC ';
        $list       = pdo_fetchall($sql, $params);
        //商品购买限制
        foreach ($list as &$row) {
            if ($row['usermaxbuy'] > 0) {
                $order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on og.orderid=o.id ' . ' WHERE og.goodsid=:goodsid AND  o.status>=1 AND o.openid=:openid  AND og.uniacid=:uniacid ', array(
                    ':goodsid' => $row['goodsid'],
                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
                $last = $row['usermaxbuy'] - $order_goodscount;
                if ($last <= 0) {
                    $last = 0;
                }

                $row['maxbuy'] = $last;
            }
        }

        $verify_goods_ischannelpick = '';
        foreach ($list as &$r) {
            if ($isladder) {
                $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                        ':id' => $r['goodsid']
                    ));
                if ($ladders) {
                    $ladders = unserialize($ladders['ladders']);
                    $laddermoney = m('goods')->getLaderMoney($ladders,$r['total']);
                    $r['marketprice'] = $laddermoney > 0 ? $laddermoney : $r['marketprice'];
                }
            }

            if (!empty($r['optionid'])) {
                $r['stock'] = $r['optionstock'];
                if ($isladder) {
                    $ladders = unserialize($r['option_ladders']);
                    if ($ladders) {
                        $laddermoney = m('goods')->getLaderMoney($ladders,$r['total']);
                        $r['marketprice'] = $laddermoney > 0 ? $laddermoney : $r['marketprice'];
                    }
                }
            }


            if (p('channel')) {
                $member = m('member')->getInfo($openid);
                if ($ischannelpay == 1) {
                    if (!empty($member['ischannel']) && !empty($member['channel_level'])) {
                        $r['marketprice'] = $r['marketprice'] * $my_info['my_level']['purchase_discount']/100;
                    }
                }
                //自提库存替换
                if ($ischannelpick == 1) {
                    if (empty($r['isopenchannel'])) {
                        $verify_goods_ischannelpick .= 1;
                    }
                    $my_stock = p('channel')->getMyOptionStock($openid, $r['goodsid'], $r['optionid']);
                    $r['stock'] = $my_stock;
                }
                if ($ischannelpay == 1) {
                    if (empty($r['isopenchannel'])) {
                        $verify_goods_ischannelpay .= 1;
                    }
                }
            }
            $totalprice += $r['marketprice'] * $r['total'];
            $total += $r['total'];
        }
        $difference = '';
        
        if (p('channel')) {
            if (empty($ischannelpick)) {
                //if (!empty($ischannelpay)) {
                    $min_price = $my_info['my_level']['min_price'];
                    $difference = $min_price - $totalprice;
                    if ($difference <= 0) {
                        $difference = '';
                    } else {
                        $difference = number_format($difference,2);
                        $difference = "还差{$difference}元";
                    }
                //}
            }
        }
        unset($r);
        $list       = set_medias($list, 'thumb');
        $totalprice = number_format($totalprice, 2);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'totalprice' => $totalprice,
                'difference' => $difference,
                'ischannelpay' => $ischannelpay,
<<<<<<< HEAD
                'verify_goods_ischannelpick' => $verify_goods_ischannelpick
=======
                'verify_goods_ischannelpick' => $verify_goods_ischannelpick,
                'verify_goods_ischannelpay' => $verify_goods_ischannelpay,
                'virtual_currency' => $virtual_currency,
                'yunbi_title' => $yunbi_title
>>>>>>> xiao_master
            ));
        
    } else if ($operation == 'add' && $_W['ispost']) {
        $id    = intval($_GPC['id']);
        $is    = $_GPC['is'] ? $_GPC['is'] : '';
        $total = $_GPC['total'];
        $type = $_GPC['type'];
        if (!strpos($total, '|')) {
            if ($total <= 0) {
                $old_total = pdo_fetchcolumn( "SELECT total FROM ".tablename('sz_yi_member_cart')." where goodsid=:id and uniacid=:uniacid and openid=:openid",array(':id' => $id, ':uniacid' => $uniacid, ':openid' => $openid) );
                $total = $old_total + $total;
                if ($total <= 0) {
                    pdo_delete('sz_yi_member_cart',array('goodsid' => $id, 'openid' => $openid, 'uniacid' => $uniacid));
                } else {
                    $sql = "update " . tablename('sz_yi_member_cart') . ' set total= '.$total.' where uniacid=:uniacid and openid=:openid and goodsid = :goodsid';
                    pdo_query($sql, array(
                        ':uniacid' => $uniacid,
                        ':goodsid' => $id,
                        ':openid' => $openid
                    ));
                }

            
            return show_json(1, array(
                /*'message' => '添加成功',*/
                'cartcount' => 0
            ));
        }
        empty($total) && $total = 1;
        $optionid = intval($_GPC['optionid']);
        $goods    = pdo_fetch('select id,marketprice,hasoption,`type`, total from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and id=:id limit 1', array(
            ':uniacid' => $uniacid,
            ':id' => $id
        ));
        if (empty($goods)) {
            return show_json(0, '商品未找到');
        }
        $diyform_plugin = p('diyform');
        $datafields     = "id,total";
        if ($diyform_plugin) {
            $datafields .= ",diyformdataid";
        }
        $data          = pdo_fetch("select {$datafields} from " . tablename('sz_yi_member_cart') . ' where openid=:openid and goodsid=:id and  optionid=:optionid and deleted=0 and  uniacid=:uniacid   limit 1', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid,
            ':optionid' => $optionid,
            ':id' => $id
        ));

        if ($goods['hasoption'] == 1) {
              $option_data = pdo_fetch("SELECT `stock` FROM " . tablename('sz_yi_goods_option') . ' WHERE id=:id', array(':id'=> $optionid));
            if (intval($data['total'] + $total) > $option_data['stock']) {
                return show_json(0, array(
                    'message' => '您最多购买' . $option_data['stock'] . '件'
                ));
            }

        } else {
            if (intval($data['total'] + $total) > $goods['total']) {
                return show_json(0, array(
                    'message' => '您最多购买' . $goods['total'] . '件'
                ));
            }
        }

        $diyformdataid = 0;
        $diyformfields = iserializer(array());
        $diyformdata   = iserializer(array());
        if ($diyform_plugin) {
            $diyformdata = $_GPC['diyformdata'];
            if (!empty($diyformdata) && is_array($diyformdata)) {
                $diyformid = intval($diyformdata['diyformid']);
                $diydata   = $diyformdata['diydata'];
                if (!empty($diyformid) && is_array($diydata)) {
                    $formInfo = $diyform_plugin->getDiyformInfo($diyformid);
                    if (!empty($formInfo)) {
                        $diyformfields = $formInfo['fields'];
                        $insert_data   = $diyform_plugin->getInsertData($diyformfields, $diydata);
                        $idata         = $insert_data['data'];
                        $diyformdata   = $idata;
                        $diyformfields = iserializer($diyformfields);
                    }
                }
            }
        }
        $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and uniacid=:uniacid  limit 1', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        $dates= pdo_fetch("select {$datafields} from " . tablename('sz_yi_member_cart') . ' where openid=:openid and goodsid=:id  and deleted=0 and  uniacid=:uniacid   limit 1', array(
        ':uniacid' => $uniacid,
        ':openid' => $openid,
        
        ':id' => $id
        ));

        if (empty($data)) {

            $data = array(
            'uniacid' => $uniacid,
            'openid' => $openid,
            'goodsid' => $id,
            'optionid' => $optionid,
            'marketprice' => $goods['marketprice'],
            'total' => $total,
            'diyformid' => $diyformid,
            'diyformdata' => $diyformdata,
            'diyformfields' => $diyformfields,
            'createtime' => time()
            );
            pdo_insert('sz_yi_member_cart', $data);
            $cartcount += $total;
            return show_json(1, array(
                'message' => '添加成功',
                'cartcount' => $cartcount
            )); 

        } else {
           /* $data['diyformdataid'] = $diyformdataid;
            $data['diyformdata']   = $diyformdata;
            $data['diyformfields'] = $diyformfields;
            pdo_update('sz_yi_member_cart', $data, array(
                'id' => $data['id']
            ));*/
            if ($is == 'choose' || $type == 'propertychange') {
                $data['total'] = $total;
            } else {
                $data['total'] += $total;
            }
            
            pdo_update('sz_yi_member_cart', array(
                    'total' => $data['total']
                ), array(
                    'uniacid' => $uniacid,
                    'goodsid' => $id,
                    'openid' => $openid,
                    'optionid' => $optionid,
                ));
                $cartcount += $total;
                return show_json(1, array(
                    'message' => '添加成功',
                    'cartcount' => $cartcount
                ));
            }
        } else {
            $total = rtrim($_GPC['total'], '|');
            $total = explode('|', $total);
            $optionid = rtrim($_GPC['optionid'], '|');
            $optionid = explode('|', $optionid);

            if (count($total) != count($optionid)) {
                return show_json(0);
            }

            foreach ($optionid as $key => $val) {
                if ($total[$key] <= 0) {
                    $old_total = pdo_fetchcolumn( "SELECT total FROM ".tablename('sz_yi_member_cart')." where goodsid=:id and uniacid=:uniacid and openid=:openid",array(':id' => $id, ':uniacid' => $uniacid, ':openid' => $openid) );
                    $total[$key] = $old_total + $total[$key];
                    if ($total[$key] <= 0) {
                        pdo_delete('sz_yi_member_cart',array('goodsid' => $id, 'openid' => $openid, 'uniacid' => $uniacid));
                    } else {
                        $sql = "update " . tablename('sz_yi_member_cart') . ' set total= '.$total.' where uniacid=:uniacid and openid=:openid and goodsid = :goodsid';
                        pdo_query($sql, array(
                            ':uniacid' => $uniacid,
                            ':goodsid' => $id,
                            ':openid' => $openid
                        ));
                    }


                    return show_json(1, array(
                        /*'message' => '添加成功',*/
                        'cartcount' => 0
                    ));
                }
                empty($total[$key]) && $total[$key] = 1;

                $goods    = pdo_fetch('select id,marketprice,hasoption,`type`, total from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and id=:id limit 1', array(
                    ':uniacid' => $uniacid,
                    ':id' => $id
                ));
                if (empty($goods)) {
                    return show_json(0, '商品未找到');
                }
                $diyform_plugin = p('diyform');
                $datafields     = "id,total";
                if ($diyform_plugin) {
                    $datafields .= ",diyformdataid";
                }
                $data          = pdo_fetch("select {$datafields} from " . tablename('sz_yi_member_cart') . ' where openid=:openid and goodsid=:id and  optionid=:optionid and deleted=0 and  uniacid=:uniacid   limit 1', array(
                    ':uniacid' => $uniacid,
                    ':openid' => $openid,
                    ':optionid' => $optionid[$key],
                    ':id' => $id
                ));

                if ($goods['hasoption'] == 1) {
                    $option_data = pdo_fetch("SELECT `stock` FROM " . tablename('sz_yi_goods_option') . ' WHERE id=:id', array(':id'=> $optionid[$key]));
                    if (intval($data['total'] + $total[$key]) > $option_data['stock']) {
                        return show_json(0, array(
                            'message' => '您最多购买' . $option_data['stock'] . '件'
                        ));
                    }

                } else {
                    if (intval($data['total'] + $total[$key]) > $goods['total']) {
                        return show_json(0, array(
                            'message' => '您最多购买' . $goods['total'] . '件'
                        ));
                    }
                }

                $diyformdataid = 0;
                $diyformfields = iserializer(array());
                $diyformdata   = iserializer(array());
                if ($diyform_plugin) {
                    $diyformdata = $_GPC['diyformdata'];
                    if (!empty($diyformdata) && is_array($diyformdata)) {
                        $diyformid = intval($diyformdata['diyformid']);
                        $diydata   = $diyformdata['diydata'];
                        if (!empty($diyformid) && is_array($diydata)) {
                            $formInfo = $diyform_plugin->getDiyformInfo($diyformid);
                            if (!empty($formInfo)) {
                                $diyformfields = $formInfo['fields'];
                                $insert_data   = $diyform_plugin->getInsertData($diyformfields, $diydata);
                                $idata         = $insert_data['data'];
                                $diyformdata   = $idata;
                                $diyformfields = iserializer($diyformfields);
                            }
                        }
                    }
                }
                $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and uniacid=:uniacid  limit 1', array(
                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
                $dates= pdo_fetch("select {$datafields} from " . tablename('sz_yi_member_cart') . ' where openid=:openid and goodsid=:id  and deleted=0 and  uniacid=:uniacid   limit 1', array(
                    ':uniacid' => $uniacid,
                    ':openid' => $openid,

                    ':id' => $id
                ));

                if (empty($data)) {

                    $data = array(
                        'uniacid' => $uniacid,
                        'openid' => $openid,
                        'goodsid' => $id,
                        'optionid' => $optionid[$key],
                        'marketprice' => $goods['marketprice'],
                        'total' => $total[$key],
                        'diyformid' => $diyformid,
                        'diyformdata' => $diyformdata,
                        'diyformfields' => $diyformfields,
                        'createtime' => time()
                    );
                    pdo_insert('sz_yi_member_cart', $data);
                    $cartcount += $total[$key];


                } else {
                    /* $data['diyformdataid'] = $diyformdataid;
                     $data['diyformdata']   = $diyformdata;
                     $data['diyformfields'] = $diyformfields;
                     pdo_update('sz_yi_member_cart', $data, array(
                         'id' => $data['id']
                     ));*/
                    if ($is == 'choose' || $type == 'propertychange') {
                        $data['total'] = $total[$key];
                    } else {
                        $data['total'] += $total[$key];
                    }

                    pdo_update('sz_yi_member_cart', array(
                        'total' => $data['total']
                    ), array(
                        'uniacid' => $uniacid,
                        'goodsid' => $id,
                        'openid' => $openid,
                        'optionid' => $optionid[$key],
                    ));
                    $cartcount += $total[$key];
                    return show_json(1, array(
                        'message' => '添加成功',
                        'cartcount' => $cartcount
                    ));
                }
            }
        }


        $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and uniacid=:uniacid and goodsid = :goodsid limit 1', array(
            ':uniacid' => $uniacid,
            'goodsid' => $id,
            ':openid' => $openid
        ));
        return show_json(1, array(
            'message' => '添加成功',
            'cartcount' => $cartcount
        ));
    } else if ($operation == 'selectoption') {
        $id         = intval($_GPC['id']);
        $goodsid    = intval($_GPC['goodsid']);
        $cartdata   = pdo_fetch("SELECT id,optionid,total FROM " . tablename('sz_yi_member_cart') . " WHERE id = :id and uniacid=:uniacid and openid=:openid limit 1", array(
            ':id' => $id,
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        $cartoption = pdo_fetch("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('sz_yi_goods_option') . " " . " where uniacid=:uniacid and goodsid=:goodsid and id=:id limit 1 ", array(
            ':id' => $cartdata['optionid'],
            ':uniacid' => $uniacid,
            ':goodsid' => $goodsid
        ));
        $cartoption = set_medias($cartoption, 'thumb');
        $cartspecs  = explode('_', $cartoption['specs']);
        $goods      = pdo_fetch("SELECT id,title,thumb,total,marketprice FROM " . tablename('sz_yi_goods') . " WHERE id = :id", array(
            ':id' => $goodsid
        ));
        $goods      = set_medias($goods, 'thumb');
        $allspecs   = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec') . " where goodsid=:id order by displayorder asc", array(
            ':id' => $goodsid
        ));
        foreach ($allspecs as &$s) {
            $s['items'] = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(
                ":specid" => $s['id']
            ));
        }
        unset($s);
        $options = pdo_fetchall("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('sz_yi_goods_option') . " where goodsid=:id order by id asc", array(
            ':id' => $goodsid
        ));
        $options = set_medias($options, 'thumb');
        $specs   = array();
        if (count($options) > 0) {
            $specitemids = explode("_", $options[0]['specs']);
            foreach ($specitemids as $itemid) {
                foreach ($allspecs as $ss) {
                    $items = $ss['items'];
                    foreach ($items as $it) {
                        if ($it['id'] == $itemid) {
                            $specs[] = $ss;
                            break;
                        }
                    }
                }
            }
        }
        return show_json(1, array(
            'cartdata' => $cartdata,
            'cartoption' => $cartoption,
            'cartspecs' => $cartspecs,
            'goods' => $goods,
            'options' => $options,
            'specs' => $specs
        ));
    } else if ($operation == 'setoption' && $_W['ispost']) {
        $id       = intval($_GPC['id']);
        $goodsid  = intval($_GPC['goodsid']);
        $optionid = intval($_GPC['optionid']);
        $option   = pdo_fetch("select id,title,thumb,marketprice,productprice,costprice, stock,weight,specs from " . tablename('sz_yi_goods_option') . " " . " where uniacid=:uniacid and goodsid=:goodsid and id=:id limit 1 ", array(
            ':id' => $optionid,
            ':uniacid' => $uniacid,
            ':goodsid' => $goodsid
        ));
        $option   = set_medias($option, 'thumb');
        if (empty($option)) {
            return show_json(0, '规格未找到');
        }
        pdo_update('sz_yi_member_cart', array(
            'optionid' => $optionid
        ), array(
            'id' => $id,
            'uniacid' => $uniacid,
            'goodsid' => $goodsid
        ));
        return show_json(1, array(
            'optionid' => $optionid,
            'optiontitle' => $option['title']
        ));
    } else if ($operation == 'updatenum' && $_W['ispost']) {
        $id      = intval($_GPC['id']);
        $goodsid = intval($_GPC['goodsid']);
        $total   = intval($_GPC['total']);
        empty($total) && $total = 1;
        $data = pdo_fetch("select f.id,f.total,f.marketprice,o.option_ladders from " . tablename('sz_yi_member_cart') . " f 
        left join " . tablename('sz_yi_goods_option') . " o on f.optionid = o.id  " . " 
        where f.id=:id and f.uniacid=:uniacid and f.goodsid=:goodsid  and f.openid=:openid limit 1 ", array(
            ':id' => $id,
            ':uniacid' => $uniacid,
            ':goodsid' => $goodsid,
            ':openid' => $openid
        ));
        if (empty($data)) {
            return show_json(0, '购物车数据未找到');
        }
        pdo_update('sz_yi_member_cart', array(
            'total' => $total
        ), array(
            'id' => $id,
            'uniacid' => $uniacid,
            'goodsid' => $goodsid
        ));
            if ($isladder) {
                if ($data['option_ladders']) {

                    $ladders = unserialize($data['option_ladders']);
                }else{
                    $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                            ':id' => $goodsid
                        ));
                    $ladders = unserialize($ladders['ladders']);
                }
                if ($ladders) {
                    $laddermoney = m('goods')->getLaderMoney($ladders,$total);
                    $marketprice = $laddermoney > 0 ? $laddermoney : $data['marketprice'];
                }
            }

        return show_json(1,$marketprice);
    } else if ($operation == 'tofavorite' && $_W['ispost']) {
        $ids = $_GPC['ids'];
        if (empty($ids) || !is_array($ids)) {
            return show_json(0, '参数错误');
        }
        foreach ($ids as $id) {
            $goodsid = pdo_fetchcolumn('select goodsid from ' . tablename('sz_yi_member_cart') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1 ', array(
                ':id' => $id,
                ':uniacid' => $uniacid,
                ':openid' => $openid
            ));
            if (!empty($goodsid)) {
                $fav = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_member_favorite') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0 limit 1 ', array(
                    ':goodsid' => $goodsid,
                    ':uniacid' => $uniacid,
                    ':openid' => $openid
                ));
                if ($fav <= 0) {
                    $fav = array(
                        'uniacid' => $uniacid,
                        'goodsid' => $goodsid,
                        'openid' => $openid,
                        'deleted' => 0,
                        'createtime' => time()
                    );
                    pdo_insert('sz_yi_member_favorite', $fav);
                }
            }
        }
        $sql = "update " . tablename('sz_yi_member_cart') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
        pdo_query($sql, array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        return show_json(1);
    } else if ($operation == 'remove' && $_W['ispost']) {
        $ids = $_GPC['ids'];
        if (empty($ids) || !is_array($ids)) {
            return show_json(0, '参数错误');
        }
        $sql = "update " . tablename('sz_yi_member_cart') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
        pdo_query($sql, array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        return show_json(1);
    } else if ($operation == 'cart' && $_W['ispost']) {
        $data          = pdo_fetchall("select * from " . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and  uniacid=:uniacid ', array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        foreach ($data as &$row) {
            $row['total'] =pdo_fetchcolumn("select sum(total) from " . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and  uniacid=:uniacid and goodsid=:id limit 1', 
                array(
                    ':uniacid' => $uniacid,
                    ':openid' => $openid,
                    ':id' => $row['goodsid']
                )
            );
        }
        // $current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where uniacid=:uniacid order by displayorder DESC', array(
        //     ':uniacid' => $_W['uniacid']
        // ));

        $parent_category = pdo_fetchall('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where parentid=0  and uniacid=:uniacid ', array(
            
            ':uniacid' => $_W['uniacid']
        ));

        foreach ($parent_category as $key => &$category) {
            $args = array(           
            'pcate' => $category['id']
            );
            $goods    = m('goods')->getList($args);

            $conut = 0;
            foreach ($goods as $key => $good) {
                $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_member_cart') . ' where openid=:openid and deleted=0 and uniacid=:uniacid and goodsid = :goodsid limit 1', array(
                    ':uniacid' => $_W['uniacid'],
                    'goodsid' => $good['id'],
                    ':openid' => $openid
                ));

                $conut = $cartcount + $conut;
            }

            $category['count'] = $conut;
        }

         return show_json(1, array(
            'categorys' => $parent_category,
            'goods' => $data
        ));
    } else if ($operation == 'gettotal' ) {
        $id = $_GPC['id'];
        $optionid = $_GPC['optionid'];
        $total = pdo_fetchcolumn("SELECT total FROM ".tablename('sz_yi_member_cart')." WHERE goodsid=:id and optionid=:optionid and uniacid=:uniacid and openid=:openid and deleted=0",
            array(
                ':id' => $id,
                ':optionid' => $optionid,
                ':uniacid' => $uniacid,
                ':openid' => $openid

            )
        );
        
        return show_json(1,$total);
    }
}
include $this->template('shop/cart');


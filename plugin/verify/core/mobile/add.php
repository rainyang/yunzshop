<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
if ($_W['isajax']) {
    if(empty($openid) || strstr($openid, 'http-equiv=refresh')){
        return show_json(2, array(
                'message' => '请先登录',
                'url' => $this->createMobileUrl('member/login')
            )); 
    }
    if ($operation == 'display') {
        $ischannelpick = intval($_GPC['ischannelpick']);
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
        if (p('channel')) {
            $channel_condtion = 'g.isopenchannel,';
        }
        if (p('yunbi')) {
            $yunbi_condtion = 'g.isforceyunbi,g.yunbi_deduct,';
        }
        $sql        = 'SELECT f.id,f.total,' . $channel_condtion . $yunbi_condtion . 'f.goodsid,g.total as stock, o.stock as optionstock, g.maxbuy,g.title,g.thumb,ifnull(o.marketprice, g.marketprice) as marketprice,g.productprice,o.title as optiontitle,f.optionid,o.specs FROM ' . tablename('sz_yi_store_goods') . ' f ' . ' left join ' . tablename('sz_yi_goods') . ' g on f.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on f.optionid = o.id ' . ' where 1 ' . $condition . ' ORDER BY `id` DESC ';
        $list       = pdo_fetchall($sql, $params);
        $verify_goods_ischannelpick = '';
        $virtual_currency = 1;
        foreach ($list as &$r) {
            if (!empty($r['optionid'])) {
                $r['stock'] = $r['optionstock'];
            }
            if (p('yunbi')) {
                $yunbi_set = p('yunbi')->getSet();
                if (!empty($yunbi_set['isdeduct'])) {
                    if (!empty($r['isforceyunbi'])) {
                        $virtual_currency += $r['yunbi_deduct'];
                    }
                }
            }
            if (p('channel')) {
                $member = m('member')->getInfo($openid);
                if (!empty($member['ischannel']) && !empty($member['channel_level'])) {
                    $r['marketprice'] = $r['marketprice'] * $my_info['my_level']['purchase_discount']/100;
                }
                //自提库存替换
                if ($ischannelpick == 1) {
                    if (empty($r['isopenchannel'])) {
                        $verify_goods_ischannelpick .= 1;
                    }
                    $my_stock = p('channel')->getMyOptionStock($openid, $r['goodsid'], $r['optionid']);
                    $r['stock'] = $my_stock;
                }
            }
            $totalprice += $r['marketprice'] * $r['total'];
            $total += $r['total'];
        }
        if (!empty($virtual_currency) && $member['virtual_currency'] >= $virtual_currency) {
            $virtual_currency = 0;
        }
        $difference = '';
        $ischannelpay = $_GPC['ischannelpay'];
        if (p('channel')) {
            if (empty($ischannelpick)) {
                //if (!empty($ischannelpay)) {
                    $min_price = $my_info['my_level']['min_price'];
                    $difference = $min_price - $totalprice;
                    if ($difference <= 0) {
                        $difference = '';
                    } else {
                        $difference = number_format($difference,2);
                        $difference = "您还需要{$difference}元才可以购买";
                    }
                //}
            }
        }
        unset($r);
        $list       = set_medias($list, 'thumb');
        $totalprice = number_format($totalprice, 2);
        $counttotal = pdo_fetchcolumn('SELECT COUNT(DISTINCT goodsid) FROM '.tablename('sz_yi_store_goods')." WHERE storeid=:storeid and uniacid=:uniacid ",array(':storeid'=>intval($_GPC['storeid']), ':uniacid'=>$_W['uniacid']));
            return show_json(1, array(
                'total' => $total,
                'counttotal' => $counttotal,
                'list' => $list,
                'totalprice' => $totalprice,
                'difference' => $difference,
                'ischannelpay' => $ischannelpay,
                'verify_goods_ischannelpick' => $verify_goods_ischannelpick,
                'virtual_currency' => $virtual_currency
            ));
        
    } else if ($operation == 'add' && $_W['ispost']) {
        $id    = intval($_GPC['id']);
        $is    = $_GPC['is'] ? $_GPC['is'] : '';
        $total = intval($_GPC['total']);
        $type = $_GPC['type'];
        $storeid = intval($_GPC['storeid']);
        if ($total <= 0) {
            $old_total = pdo_fetchcolumn( "SELECT total FROM ".tablename('sz_yi_store_goods')." where goodsid=:id and uniacid=:uniacid and storeid=:storeid",array(':id' => $id, ':uniacid' => $uniacid, ':storeid' => $storeid) );
            $total = $old_total + $total;
            if ($total <= 0) {
                pdo_delete('sz_yi_store_goods',array('goodsid' => $id, 'storeid' => $storeid, 'uniacid' => $uniacid));  
            } else {
                $sql = "update " . tablename('sz_yi_store_goods') . ' set total= '.$total.' where uniacid=:uniacid and storeid=:storeid and goodsid = :goodsid';
                pdo_query($sql, array(
                    ':uniacid' => $uniacid,
                    ':goodsid' => $id,
                    ':storeid' => $storeid
                ));
            }

            
            return show_json(1, array(
                /*'message' => '添加成功',*/
                'cartcount' => 0
            ));
        }
        empty($total) && $total = 1;
        $optionid = intval($_GPC['optionid']);
        $goods    = pdo_fetch('select id,marketprice from ' . tablename('sz_yi_goods') . ' where uniacid=:uniacid and id=:id limit 1', array(
            ':uniacid' => $uniacid,
            ':id' => $id
        ));
        if (!empty($optionid)) {
            $isoption = pdo_fetch("SELECT * FROM " .tablename('sz_yi_store_goods'). " WHERE goodsid=:goodsid and storeid=:storeid and uniacid=:uniacid and optionid=0", array(':goodsid' => $id, ':storeid' => $storeid, ':uniacid' => $uniacid));
            if (!empty($isoption)) {
                pdo_delete('sz_yi_store_goods', array('goodsid' => $id, 'uniacid' => $uniacid, 'storeid' => $storeid, 'optionid' => 0));
            }
        }
        if (empty($goods)) {
            return show_json(0, '商品未找到');
        }
        $diyform_plugin = p('diyform');
        $datafields     = "id,total";
        if ($diyform_plugin) {
            $datafields .= ",diyformdataid";
        }
        $data          = pdo_fetch("select {$datafields} from " . tablename('sz_yi_store_goods') . ' where storeid=:storeid and goodsid=:id and  optionid=:optionid and deleted=0 and  uniacid=:uniacid   limit 1', array(
            ':uniacid' => $uniacid,
            ':storeid' => $storeid,
            ':optionid' => $optionid,
            ':id' => $id
        ));
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
        $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_store_goods') . ' where storeid=:storeid and deleted=0 and uniacid=:uniacid  limit 1', array(
            ':uniacid' => $uniacid,
            ':storeid' => $storeid
        ));
        $dates= pdo_fetch("select {$datafields} from " . tablename('sz_yi_store_goods') . ' where storeid=:storeid and goodsid=:id  and deleted=0 and  uniacid=:uniacid   limit 1', array(
        ':uniacid' => $uniacid,
        ':storeid' => $storeid,
        
        ':id' => $id
        ));                   
        if (empty($data)) {

            $data = array(
            'uniacid' => $uniacid,
            'goodsid' => $id,
            'optionid' => $optionid,
            'marketprice' => $goods['marketprice'],
            'total' => $total,
            'diyformid' => $diyformid,
            'diyformdata' => $diyformdata,
            'diyformfields' => $diyformfields,
            'createtime' => time(),
            'storeid' => $storeid
            );
            pdo_insert('sz_yi_store_goods', $data);
            $cartcount += $total;
            return show_json(1, array(
                'message' => '添加成功',
                'cartcount' => $cartcount
            )); 

        } else {
           /* $data['diyformdataid'] = $diyformdataid;
            $data['diyformdata']   = $diyformdata;
            $data['diyformfields'] = $diyformfields;
            pdo_update('sz_yi_store_goods', $data, array(
                'id' => $data['id']
            ));*/
            if ($is == 'choose' || $type == 'propertychange') {
                $data['total'] = $total;
            } else {
                $data['total'] += $total;
            }
            
            pdo_update('sz_yi_store_goods', array(
                    'total' => $data['total']
                ), array(
                    'uniacid' => $uniacid,
                    'goodsid' => $id,
                    'optionid' => $optionid,
            		'storeid' => $storeid
                ));
            $cartcount += $total;
            return show_json(1, array(
                'message' => '添加成功',
                'cartcount' => $cartcount
            ));
        }
        $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_store_goods') . ' where openid=:openid and deleted=0 and uniacid=:uniacid and goodsid = :goodsid limit 1', array(
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
        $cartdata   = pdo_fetch("SELECT id,optionid,total FROM " . tablename('sz_yi_store_goods') . " WHERE id = :id and uniacid=:uniacid and openid=:openid limit 1", array(
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
        pdo_update('sz_yi_store_goods', array(
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
        $data = pdo_fetchall("select id,total from " . tablename('sz_yi_store_goods') . " " . " where id=:id and uniacid=:uniacid and goodsid=:goodsid  and openid=:openid limit 1 ", array(
            ':id' => $id,
            ':uniacid' => $uniacid,
            ':goodsid' => $goodsid,
            ':openid' => $openid
        ));
        if (empty($data)) {
            return show_json(0, '购物车数据未找到');
        }
        pdo_update('sz_yi_store_goods', array(
            'total' => $total
        ), array(
            'id' => $id,
            'uniacid' => $uniacid,
            'goodsid' => $goodsid
        ));
        return show_json(1);
    } else if ($operation == 'tofavorite' && $_W['ispost']) {
        $ids = $_GPC['ids'];
        if (empty($ids) || !is_array($ids)) {
            return show_json(0, '参数错误');
        }
        foreach ($ids as $id) {
            $goodsid = pdo_fetchcolumn('select goodsid from ' . tablename('sz_yi_store_goods') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1 ', array(
                ':id' => $id,
                ':uniacid' => $uniacid,
                ':openid' => $openid
            ));
            if (!empty($goodsid)) {
                $fav = pdo_fetchcolumn('select sum(*) from ' . tablename('sz_yi_member_favorite') . ' where goodsid=:goodsid and uniacid=:uniacid and openid=:openid and deleted=0 limit 1 ', array(
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
        $sql = "update " . tablename('sz_yi_store_goods') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
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
        $sql = "update " . tablename('sz_yi_store_goods') . ' set deleted=1 where uniacid=:uniacid and openid=:openid and id in (' . implode(',', $ids) . ')';
        pdo_query($sql, array(
            ':uniacid' => $uniacid,
            ':openid' => $openid
        ));
        return show_json(1);
    } else if ($operation == 'cart' && $_W['ispost']) {
        $storeid = intval($_GPC['storeid']);
        $data          = pdo_fetchall("select * from " . tablename('sz_yi_store_goods') . ' where storeid=:storeid and deleted=0 and  uniacid=:uniacid ', array(
            ':uniacid' => $uniacid,
            ':storeid' => $storeid
        ));
        foreach ($data as &$row) {
            $row['total'] =pdo_fetchcolumn("select sum(total) from " . tablename('sz_yi_store_goods') . ' where storeid=:storeid and deleted=0 and  uniacid=:uniacid and goodsid=:id limit 1', 
                array(
                    ':uniacid' => $uniacid,
                    ':storeid' => $storeid,
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
                $cartcount = pdo_fetchcolumn('select sum(total) from ' . tablename('sz_yi_store_goods') . ' where storeid=:storeid and deleted=0 and uniacid=:uniacid and goodsid = :goodsid limit 1', array(
                    ':uniacid' => $_W['uniacid'],
                    'goodsid' => $good['id'],
                    ':storeid' => $storeid
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
        $id = intval($_GPC['id']);
        $storeid = intval($_GPC['storeid']);
        $optionid = intval($_GPC['optionid']);
        $total = pdo_fetchcolumn("SELECT total FROM ".tablename('sz_yi_store_goods')." WHERE goodsid=:id and optionid=:optionid and uniacid=:uniacid and storeid=:storeid and deleted=0",
            array(
                ':id' => $id,
                ':optionid' => $optionid,
                ':uniacid' => $uniacid,
                ':storeid' => $storeid

            )
        );
        
        return show_json(1,$total);
    }
}
include $this->template('shop/cart');


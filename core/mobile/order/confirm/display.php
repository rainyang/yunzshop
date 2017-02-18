<?php
namespace mobile\order\confirm;
class Display extends Base
{
    private function _isFromCart()
    {
        $goods_id = $this->getGoodsId();
        if (empty($goods_id)) {
            return true;
        }
        return false;
    }

    function goodid()
    {
        global $_GPC;
        $goodid = $_GPC['id'] ? intval($_GPC['id']) : 0;
        return $goodid;
    }

    function cartid()
    {
        global $_GPC;
        $cartid = $_GPC['cartids'] ? $_GPC['cartids'] : 0;
        return $cartid;
    }

    private function isHotelGoods()
    {
        if (!p('hotel')) {
            return false;
        }
        if (!$this->_isFromCart()) {
            return false;
        }
        $goods = $this->getGoods();
        if ($goods['type'] == 99) {
            return true;
        }
        return false;
    }

    private function getGoodsId()
    {
        global $_GPC;
        return intval($_GPC["id"]);
    }

    private function getGoods()
    {
        if ($this->_isFromCart()) {
            $goods = $this->_getCartBuyGoods();
        } else {
            $goods = $this->_getDirectBuyGoods();

        }
        foreach ($goods as &$g) {
            if ($g['plugin'] == 'fund') {
                $g['url'] = $this->createPluginMobileUrl('fund/detail', array('id' => $g['goodsid']));
            } else {
                $g['url'] = $this->createMobileUrl('shop/detail', array('id' => $g['goodsid']));
            }

        }
        $goods = set_medias($goods, 'thumb');
        return $goods;
    }

    private function suppliers()
    {
        global $_GPC;
        $condition = '';
        //check var. cart store in db.
        $cartids = $_GPC['cartids'];
        if (!empty($cartids)) {
            $condition = ' and c.id in (' . $cartids . ')';
        }
        if ($this->_isFromCart()) {
            return pdo_fetchall('SELECT distinct g.supplier_uid FROM ' . tablename('sz_yi_member_cart') . ' c ' . ' left join ' . tablename('sz_yi_goods') . ' g on c.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on c.optionid = o.id ' . " where c.openid=:openid and  c.deleted=0 and c.uniacid=:uniacid {$condition} order by g.supplier_uid asc", array(
                ':uniacid' => $this->getUniacid(),
                ':openid' => $this->getOpenid()
            ), 'supplier_uid');
        } else {
            $data = $this->getGoodsFromGoodsModel();
            return array($data['supplier_uid'] => array("supplier_uid" => $data['supplier_uid']));
        }
    }

    private function getGoodsFromGoodsModel()
    {
        $card_cond = '';
        if (p('card')) {
            $card_cond = ', card_deduct';
        }
        if (p('hotel')) {
            $sql = "SELECT id as goodsid,type,title,weight,deposit,issendfree,isnodiscount, thumb,marketprice,storeids,isverify,isverifysend,dispatchsend,deduct,virtual,maxbuy,usermaxbuy,discounts,discounts2,deductcommission,discounttype,discountway,total as stock, deduct2, ednum, edmoney, edareas, diyformtype, diyformid, diymode, dispatchtype, dispatchid, dispatchprice, supplier_uid, yunbi_deduct, plugin " . $card_cond . " FROM " . tablename("sz_yi_goods") . " where id=:id and uniacid=:uniacid  limit 1";
        } else {
            $sql = "SELECT id as goodsid,type,title,weight,issendfree,isnodiscount, thumb,marketprice,storeids,isverify,isverifysend,dispatchsend,deduct,virtual,maxbuy,usermaxbuy,discounts,discounts2,deductcommission,discounttype,discountway,total as stock, deduct2, ednum, edmoney, edareas, diyformtype, diyformid, diymode, dispatchtype, dispatchid, dispatchprice, supplier_uid, yunbi_deduct, plugin " . $card_cond . " FROM " . tablename("sz_yi_goods") . " where id=:id and uniacid=:uniacid  limit 1";
        }
        $data = pdo_fetch($sql, array(
            ':uniacid' => $this->getUniacid(),
            ':id' => $this->getGoodsId()
        ));
        return $data;
    }

    private function isladder()
    {
        if (p('ladder')) {
            $ladder_set = p('ladder')->getSet();
            if ($ladder_set['isladder']) {
                return true;
            }
        }
        return false;
    }

    private function optionid($key = null)
    {
        global $_GPC;
        if (strpos($_GPC['optionid'], '|')) {
            $result = rtrim($_GPC['optionid'], '|');
            $result = explode('|', $result);
        } else {
            $result = intval($_GPC['optionid']);
        }
        if (isset($key)) {
            return $result[$key];
        }
        return $result;
    }

    private function _getDirectBuyGoods()
    {
        $data = $this->getGoodsFromGoodsModel();
        //阶梯价格
        if ($this->isladder()) {
            $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                ':id' => $data['goodsid']
            ));
            if ($ladders) {
                $ladders = unserialize($ladders['ladders']);
                $laddermoney = m('goods')->getLaderMoney($ladders, $this->getTotal());
                $data['marketprice'] = $laddermoney > 0 ? $laddermoney : $data['marketprice'];
            }
        }

        //新规格
        if (is_int($this->getTotal()) && is_int($this->optionid())) {
            $data['total'] = $this->getTotal();
            $data['optionid'] = $this->optionid();
            if (!$this->optionid()) {
                $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,virtual,stock,weight,option_ladders from ' . tablename('sz_yi_goods_option') . ' WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1', array(
                    ':uniacid' => $this->getUniacid(),
                    ':goodsid' => $this->getGoodsId(),
                    ':id' => $this->optionid()
                ));
                //阶梯价格
                if ($this->isladder()) {
                    $ladders = unserialize($option['option_ladders']);
                    if ($ladders) {
                        $laddermoney = m('goods')->getLaderMoney($ladders, $this->getTotal());
                        $option['marketprice'] = $laddermoney > 0 ? $laddermoney : $option['marketprice'];
                    }
                }
                if (!empty($option)) {
                    $data['optionid'] = $this->optionid();
                    $data['optiontitle'] = $option['title'];
                    if (p('supplier')) {
                        if ($option['marketprice'] != 0) {
                            $data['marketprice'] = $option['marketprice'];
                        }
                    } else {
                        $data['marketprice'] = $option['marketprice'];
                    }
                    $data['virtual'] = $option['virtual'];
                    $data['stock'] = $option['stock'];
                    if (!empty($option['weight'])) {
                        $data['weight'] = $option['weight'];
                    }
                }
            }
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
                    ':uniacid' => $this->getUniacid(),
                    ':openid' => $this->getOpenid()
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
            if ($this->isHotelGoods()) {
                $btime = $_SESSION['data']['btime'];
                $bdate = $_SESSION['data']['bdate'];
                // 住几天
                $days = intval($_SESSION['data']['day']);
                // 离店
                $etime = $_SESSION['data']['etime'];
                $edate = $_SESSION['data']['edate'];
                $date_array = array();
                $date_array[0]['date'] = $bdate;
                $date_array[0]['day'] = date('j', $btime);
                $date_array[0]['time'] = $btime;
                $date_array[0]['month'] = date('m', $btime);

                if ($days > 1) {
                    for ($i = 1; $i < $days; $i++) {
                        $date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
                        $date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
                        $date_array[$i]['day'] = date('j', $date_array[$i]['time']);
                        $date_array[$i]['month'] = date('m', $date_array[$i]['time']);
                    }
                }
                $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                $params2 = array(':goodsid' => $this->getGoodsId());
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
                                    $list['date'] = $date_array[$i]['date'];
                                } else if ($room_num > 0 && $room_num < $max_room) {
                                    $max_room = $room_num;
                                    $list['num'] = $room_num;
                                    $list['date'] = $date_array[$i]['date'];
                                } else {
                                    $list['num'] = $max_room;
                                    $list['date'] = $date_array[$i]['date'];
                                }
                                break;
                            }
                        }
                    }
                }
                $data['totalmaxbuy'] = $list['num'];
            }
            $goods[] = $data;
        } else {
            if (count($this->getTotal()) != count($this->optionid())) {
                return show_json(0);
            }
            foreach ($this->optionid() as $key => $val) {
                $data['total'] = $this->getTotal($key);
                $data['optionid'] = $this->optionid($key);
                if (!empty($data['optionid'])) {
                    $option = pdo_fetch('select id,title,marketprice,goodssn,productsn,virtual,stock,weight from ' . tablename('sz_yi_goods_option') . ' WHERE id=:id AND goodsid=:goodsid AND uniacid=:uniacid  limit 1', array(
                        ':uniacid' => $this->getUniacid(),
                        ':goodsid' => $this->getGoodsId(),
                        ':id' => $data['optionid']
                    ));
                    if (!empty($option)) {
                        $data['optionid'] = $data['optionid'];
                        $data['optiontitle'] = $option['title'];
                        if (p('supplier')) {
                            if ($option['marketprice'] != 0) {
                                $data['marketprice'] = $option['marketprice'];
                            }
                        } else {
                            $data['marketprice'] = $option['marketprice'];
                        }
                        $data['virtual'] = $option['virtual'];
                        $data['stock'] = $option['stock'];
                        if (!empty($option['weight'])) {
                            $data['weight'] = $option['weight'];
                        }
                    }
                }
                $changenum = true;
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
                        ':uniacid' => $this->getUniacid(),
                        ':openid' => $this->getOpenid()
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

    private function _getCartBuyGoods()
    {
        global $_GPC;
        $condition = '';
        //check var. cart store in db.
        $cartids = $_GPC['cartids'];
        if (!empty($cartids)) {
            $condition = ' and c.id in (' . $cartids . ')';
        }


        $card_cond = '';
        if (p('card')) {
            $card_cond = ', g.card_deduct';
        }
        $sql = 'SELECT c.goodsid, c.total, g.maxbuy, g.type, g.issendfree, g.isnodiscount, g.weight, o.weight as optionweight, g.title, g.thumb, ifnull(o.marketprice, g.marketprice) as marketprice, o.title as optiontitle,c.optionid,g.storeids,g.isverify,g.isverifysend,g.dispatchsend, g.deduct,g.deduct2, g.deductcommission, g.virtual, o.virtual as optionvirtual, discounts, discounts2, discounttype, discountway, g.supplier_uid, g.dispatchprice, g.dispatchtype, g.dispatchid, g.yunbi_deduct, g.isforceyunbi, o.option_ladders, g.plugin ' . $card_cond . ' FROM ' . tablename('sz_yi_member_cart') . ' c ' . ' left join ' . tablename('sz_yi_goods') . ' g on c.goodsid = g.id ' . ' left join ' . tablename('sz_yi_goods_option') . ' o on c.optionid = o.id ' . " where c.openid=:openid and  c.deleted=0 and c.uniacid=:uniacid {$condition} order by g.supplier_uid asc";

        $goods = pdo_fetchall($sql, array(
            ':uniacid' => $this->getUniacid(),
            ':openid' => $this->getOpenid()
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
                if ($this->isladder()) {
                    if ($v['option_ladders']) {
                        $ladders = unserialize($v['option_ladders']);
                    } else {
                        $ladders = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_ladder') . " WHERE goodsid = :id limit 1", array(
                            ':id' => $v['goodsid']
                        ));
                        $ladders = unserialize($ladders['ladders']);
                    }

                    if ($ladders) {
                        $laddermoney = m('goods')->getLaderMoney($ladders, $v['total']);
                        $goods[$k]['marketprice'] = $laddermoney > 0 ? $laddermoney : $v['marketprice'];
                    }
                }
            }
        }
        return $goods;
    }

    function isverify($goods)
    {
        foreach ($goods as &$g) {
            if ($g['isverify'] == 2) {
                return true;
            }
        }
    }

    function isverifysend($goods)
    {
        foreach ($goods as &$g) {
            if ($g['isverifysend'] == 1) {
                return true;
            }
        }
    }

    function dispatchsend($goods)
    {
        foreach ($goods as &$g) {
            if ($g['dispatchsend'] == 1) {
                return true;
            }
        }
    }

    private function ischannelpay()
    {
        global $_GPC;
        return intval($_GPC['ischannelpay']);

    }

    function isvirtual($goods)
    {
        foreach ($goods as &$g) {
            if (!empty($g['virtual']) || $g['type'] == 2) {
                return true;
            }
            if (p('channel')) {
                if ($this->ischannelpay() == 1 && empty($ischannelpick)) {
                    return true;
                }
            }
        }
    }

    function issale($goods)
    {
        foreach ($goods as &$g) {
            if ($g['plugin'] == 'fund') {
                return false;
            }
        }
    }

    function hascouponplugin($goods)
    {
        foreach ($goods as &$g) {
            if ($g['plugin'] == 'fund') {
                return false;
            }
        }
    }

    function getTotal($key = null)
    {
        global $_GPC;
        if (strpos($_GPC['total'], '|')) {
            $result = rtrim($_GPC['total'], '|');
            $result = explode('|', $result);
        } else {
            $result = intval($_GPC['total']);
        }
        if ($result < 1) {
            $result = 1;
        }
        if (isset($key)) {
            return $result[$key];
        }
        return $result;
    }

    public function index()
    {
        global $_GPC;


        if (is_array($this->getTotal())) {
            $buytotal = 1;
        } else {
            $buytotal = $this->getTotal();
        }


        $goods = $this->getGoods();


        //多店值分开初始化
        foreach ($this->suppliers() as $key => $val) {
            $order_all[$val['supplier_uid']]['weight'] = 0;
            $order_all[$val['supplier_uid']]['total'] = 0;
            $order_all[$val['supplier_uid']]['goodsprice'] = 0;
            $order_all[$val['supplier_uid']]['realprice'] = 0;
            $order_all[$val['supplier_uid']]['deductprice'] = 0;
            $order_all[$val['supplier_uid']]['yunbideductprice'] = 0;
            $order_all[$val['supplier_uid']]['discountprice'] = 0;
            $order_all[$val['supplier_uid']]['deductprice2'] = 0;
            $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
            $order_all[$val['supplier_uid']]['storeids'] = array();
            $order_all[$val['supplier_uid']]['dispatch_array'] = array();
            $order_all[$val['supplier_uid']]['supplier_uid'] = $val['supplier_uid'];
            if ($val['supplier_uid'] == 0) {
                $order_all[$val['supplier_uid']]['supplier_name'] = $this->getShopSet('name');
            } else {
                $supplier_names = pdo_fetch('select username, brandname from ' . tablename('sz_yi_perm_user') . ' where uid=' . $val['supplier_uid'] . " and uniacid=" . $this->getUniacid());
                if (!empty($supplier_names)) {
                    $order_all[$val['supplier_uid']]['supplier_name'] = $supplier_names['brandname'] ? $supplier_names['brandname'] : "";
                } else {
                    $order_all[$val['supplier_uid']]['supplier_name'] = '';
                }
            }
        }
        //购买人信息,等级
        $member = m('member')->getMember($this->getOpenid());
        $level = m("member")->getLevel($this->getOpenid());

        $stores = array();
        $stores_send = array();
        $dispatch_list = false;


        if (p('channel')) {
            $my_info = p('channel')->getInfo($this->getOpenid());
        }

        $card_deduct_total = 0;

        foreach ($goods as &$g) {

            if (p('card')) {
                $card_deduct_total += $g['card_deduct'];
            }

            if (empty($g["total"]) || intval($g["total"]) == "-1") {
                $g["total"] = 1;
            }
            if (p('channel')) {
                if ($this->ischannelpay() == 1) {
                    $g['marketprice'] = $g['marketprice'] * $my_info['my_level']['purchase_discount'] / 100;
                }
            }
            $gprice = $g["marketprice"] * $g["total"];

            $discounts = json_decode($g["discounts"], true);

            $discountway = $g['discountway'];
            $discounttype = $g['discounttype'];
            if ($discountway == 1) {
                //折扣
                if ($g["discounttype"] == 1) {
                    //会员等级折扣
                    $level = m("member")->getLevel($this->getOpenid());
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
                    $level = p('commission')->getLevel($this->getOpenid());
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
                if (p('channel') && $this->ischannelpay() == 1) {
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
                    $level = m("member")->getLevel($this->getOpenid());
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
                    $level = p('commission')->getLevel($this->getOpenid());
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
                if (p('channel') && $this->ischannelpay() == 1) {
                    $price = $gprice;
                }
            }

            $g["discount"] = $level["discount"];
            $g["ggprice"] = $price;

            $order_all[$g['supplier_uid']]['realprice'] += $price;
            $order_all[$g['supplier_uid']]['goodsprice'] += $gprice;
            //商品为酒店时候的价格
            if ($this->isHotelGoods()) {
                $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                $params2 = array(':goodsid' => $this->getGoodsId());
                $room = pdo_fetch($sql2, $params2);
                $pricefield = 'oprice';
                $r_sql = 'SELECT `roomdate`, `num`, `oprice`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('sz_yi_hotel_room_price') .
                    ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                    ' `roomdate` < :etime';
                $params = array(':roomid' => $room['id'], ':btime' => $btime, ':etime' => $etime);
                $price_list = pdo_fetchall($r_sql, $params);
                $this_price = $old_price = $pricefield == 'cprice' ? $room['oprice'] * $member_p[$_W['member']['groupid']] : $room['roomprice'];
                if ($this_price == 0) {
                    $this_price = $old_price = $room['oprice'];
                }
                $totalprice = $old_price * $days;
                //价格表中存在
                if ($price_list) {
                    $check_date = array();
                    foreach ($price_list as $k => $v) {
                        $price_list[$k]['time'] = date('Y-m-d', $v['roomdate']);

                        $new_price = $pricefield == 'mprice' ? $this_price : $v['m_price'];
                        $roomdate = $v['roomdate'];
                        if ($v['status'] == 0 || $v['num'] == 0) {
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

                } else {
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
                if ($g["yunbi_deduct"] * $g["total"] > $price) {
                    $order_all[$g['supplier_uid']]['yunbideductprice'] += $price;
                } else {
                    $order_all[$g['supplier_uid']]['yunbideductprice'] += $g["yunbi_deduct"] * $g["total"];
                }

            } else {
                $order_all[$g['supplier_uid']]['yunbideductprice'] += $g["yunbi_deduct"];
            }

            if ($g["deduct2"] == 0.00) {
                $order_all[$g['supplier_uid']]['deductprice2'] += $price;
            } elseif ($g["deduct2"] > 0) {
                if ($g["deduct2"] > $price) {
                    $order_all[$g['supplier_uid']]['deductprice2'] += $price;
                } else {
                    $order_all[$g['supplier_uid']]['deductprice2'] += $g["deduct2"];
                }
            }
            if ($g["deductcommideductcommissionssion"] == 0.00) {
                $order_all[$g['supplier_uid']]['deductcommissionprice'] += $price;
            } elseif ($g["deductcommission"] > 0) {
                if ($g["deductcommission"] > $price) {
                    $order_all[$g['supplier_uid']]['deductcommissionprice'] += $price;
                } else {
                    $order_all[$g['supplier_uid']]['deductcommissionprice'] += $g["deductcommission"];
                }
            }
            $order_all[$g['supplier_uid']]['goods'][] = $g;
        }

        unset($g);
        //核销
        if ($this->isverify()) {
            $storeids = array();
            foreach ($goods as $g) {
                if (!empty($g['storeids'])) {
                    $order_all[$g['supplier_uid']]['storeids'] = array_merge(explode(',', $g['storeids']), $order_all[$g['supplier_uid']]['storeids']);
                }
            }

            foreach ($this->suppliers() as $key => $val) {
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
        }


        if (!$this->isvirtual() && $this->isDispath()) {
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
                            if (!($this->address())) {
                                if (!in_array($this->address('city'), $gareas)) {
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
                            if (!($this->address())) {
                                if (!in_array($this->address("city"), $gareas)) {
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
                                $order_all[$g['supplier_uid']]['dispatch_array'][$dkey]["data"] = $order_all[$g['supplier_uid']]['dispatch_data'];
                                $order_all[$g['supplier_uid']]['dispatch_array'][$dkey]["param"] = $order_all[$g['supplier_uid']]['param'];
                            }
                        }
                    }
                }
            }

            foreach ($this->suppliers() as $key => $val) {
                if (!empty($order_all[$val['supplier_uid']]['dispatch_array'])) {
                    foreach ($order_all[$val['supplier_uid']]['dispatch_array'] as $k => $v) {
                        $order_all[$val['supplier_uid']]['dispatch_data'] = $order_all[$val['supplier_uid']]['dispatch_array'][$k]["data"];
                        $param = $order_all[$val['supplier_uid']]['dispatch_array'][$k]["param"];
                        $areas = unserialize($order_all[$val['supplier_uid']]['dispatch_data']["areas"]);
                        if (!($this->address())) {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getCityDispatchPrice($areas, $this->address("city"), $param, $order_all[$val['supplier_uid']]['dispatch_data'], $val['supplier_uid']);
                        } else if (!empty($member["city"])) {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getCityDispatchPrice($areas, $member["city"], $param, $order_all[$val['supplier_uid']]['dispatch_data'], $val['supplier_uid']);
                        } else {
                            $order_all[$val['supplier_uid']]['dispatch_price'] += m("order")->getDispatchPrice($param, $order_all[$val['supplier_uid']]['dispatch_data'], -1, $val['supplier_uid']);
                        }
                    }
                }
            }
        }

        $sale_plugin = p('sale');
        $saleset = false;

        if ($sale_plugin && $this->issale()) {
            $saleset = $sale_plugin->getSet();
            $saleset["enoughs"] = $sale_plugin->getEnoughs();
        }


        //订单总价
        $realprice_total = 0;
        foreach ($this->suppliers() as $key => $val) {
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
                                if (!($this->address())) {
                                    if (!in_array($this->address("city"), $areas)) {
                                        $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($this->isHotelGoods()) {
                    $order_all[$val['supplier_uid']]['dispatch_price'] = 0;
                }
                $order_all[$val['supplier_uid']]['saleset'] = $saleset;
                if (p('channel') && $this->ischannelpay() == 1) {
                    $saleset = array();
                }
                if (!empty($saleset["enoughs"])) {
                    //取满额条件值最大的1个条件
                    $tmp_money = 0;

                    foreach ($saleset["enoughs"] as $e) {
                        if ($order_all[$val['supplier_uid']]['realprice'] >= floatval($e["enough"]) && floatval($e["money"]) > 0) {
                            if ($e["enough"] > $tmp_money) {
                                $tmp_money = $e["enough"];

                                $order_all[$val['supplier_uid']]['saleset']["showenough"] = true;
                                $order_all[$val['supplier_uid']]['saleset']["enoughmoney"] = $e["enough"];
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
                //虚拟币抵扣运费
                if (empty($yunbiset["dispatchnodeduct"])) {
                    $order_all[$val['supplier_uid']]['yunbideductprice'] += $order_all[$val['supplier_uid']]['dispatch_price'];
                }
            }
            $order_all[$val['supplier_uid']]['hascoupon'] = false;
            if ($this->hascouponplugin()) {
                $order_all[$val['supplier_uid']]['couponcount'] = p("coupon")->consumeCouponCount($this->getOpenid(), $order_all[$val['supplier_uid']]['goodsprice'], $val['supplier_uid'], 0, 0, $this->goodid(), $this->cartid());
                $order_all[$val['supplier_uid']]['hascoupon'] = $order_all[$val['supplier_uid']]['couponcount'] > 0;
            }
            if (p('card')) {
                $order_all[$val['supplier_uid']]['cardcount'] = p('card')->consumeCardCount($this->getOpenid());
            }
            $order_all[$val['supplier_uid']]['realprice'] += $order_all[$val['supplier_uid']]['dispatch_price'];
            $realprice_total += $order_all[$val['supplier_uid']]['realprice'];
            $order_all[$val['supplier_uid']]['deductcredit'] = 0;
            $order_all[$val['supplier_uid']]['deductmoney'] = 0;
            $order_all[$val['supplier_uid']]['deductcredit2'] = 0;
            if ($sale_plugin) {
                $credit = m('member')->getCredit($this->getOpenid(), 'credit1');
                if (!empty($saleset['creditdeduct'])) {
                    $pcredit = intval($saleset['credit']);
                    $pmoney = round(floatval($saleset['money']), 2);
                    if ($pcredit > 0 && $pmoney > 0) {
                        if ($credit % $pcredit == 0) {
                            $order_all[$val['supplier_uid']]['deductmoney'] = round(intval($credit / $pcredit) * $pmoney, 2);
                        } else {
                            $order_all[$val['supplier_uid']]['deductmoney'] = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
                        }
                    }
                    if ($order_all[$val['supplier_uid']]['deductmoney'] > $order_all[$val['supplier_uid']]['deductprice']) {
                        $order_all[$val['supplier_uid']]['deductmoney'] = $order_all[$val['supplier_uid']]['deductprice'];
                    }
                    if ($order_all[$val['supplier_uid']]['deductmoney'] > $order_all[$val['supplier_uid']]['realprice']) {
                        $order_all[$val['supplier_uid']]['deductmoney'] = $order_all[$val['supplier_uid']]['realprice'];
                    }
                    $order_all[$val['supplier_uid']]['deductcredit'] = $order_all[$val['supplier_uid']]['deductmoney'] / $pmoney * $pcredit;
                }
                if (!empty($saleset['moneydeduct'])) {
                    $order_all[$val['supplier_uid']]['deductcredit2'] = m('member')->getCredit($this->getOpenid(), 'credit2');
                    if ($order_all[$val['supplier_uid']]['deductcredit2'] > $order_all[$val['supplier_uid']]['realprice']) {
                        $order_all[$val['supplier_uid']]['deductcredit2'] = $order_all[$val['supplier_uid']]['realprice'];
                    }
                    if ($order_all[$val['supplier_uid']]['deductcredit2'] > $order_all[$val['supplier_uid']]['deductprice2']) {
                        $order_all[$val['supplier_uid']]['deductcredit2'] = $order_all[$val['supplier_uid']]['deductprice2'];
                    }
                }
            }

            //佣金抵扣
            $order_all[$val['supplier_uid']]['deductcommission'] = 0;
            if (p('commission')) {
                $commission_set = p('commission')->getSet();
                if ($commission_set['deduction']) {
                    $member_commission = p('commission')->getInfo($this->getOpenid(), array('ok'));
                    $order_all[$val['supplier_uid']]['deductcommission_money'] = $member_commission['commission_ok'];
                    if ($order_all[$val['supplier_uid']]['deductcommission_money'] > $order_all[$val['supplier_uid']]['deductcommissionprice']) {
                        $order_all[$val['supplier_uid']]['deductcommission_money'] = $order_all[$val['supplier_uid']]['deductcommissionprice'];
                    }
                    if ($order_all[$val['supplier_uid']]['deductcommission_money'] > $order_all[$val['supplier_uid']]['realprice']) {
                        $order_all[$val['supplier_uid']]['deductcommission_money'] = $order_all[$val['supplier_uid']]['realprice'];
                    }
                    $order_all[$val['supplier_uid']]['deductcommission'] = $order_all[$val['supplier_uid']]['deductcommission_money'];
                }
            }


            //虚拟币抵扣
            $order_all[$val['supplier_uid']]['deductyunbi'] = 0;
            $order_all[$val['supplier_uid']]['deductyunbimoney'] = 0;
            if (p('yunbi') && $yunbiset['isdeduct']) {
                $virtual_currency = $member['virtual_currency'];//m('member')->getCredit($this->getOpenid(), 'virtual_currency');
                $ycredit = 1;
                $ymoney = round(floatval($yunbiset['money']), 2);
                if ($ycredit > 0 && $ymoney > 0) {
                    if ($virtual_currency % $ycredit == 0) {
                        $order_all[$val['supplier_uid']]['deductyunbimoney'] = round(intval($virtual_currency / $ycredit) * $ymoney, 2);
                    } else {
                        $order_all[$val['supplier_uid']]['deductyunbimoney'] = round((intval($virtual_currency / $ycredit) + 1) * $ymoney, 2);
                    }
                }
                if ($order_all[$val['supplier_uid']]['deductyunbimoney'] > $order_all[$val['supplier_uid']]['yunbideductprice']) {
                    $order_all[$val['supplier_uid']]['deductyunbimoney'] = $order_all[$val['supplier_uid']]['yunbideductprice'];
                }
                if ($order_all[$val['supplier_uid']]['deductyunbimoney'] > $order_all[$val['supplier_uid']]['realprice']) {
                    $order_all[$val['supplier_uid']]['deductyunbimoney'] = $order_all[$val['supplier_uid']]['realprice'];
                }

                $order_all[$val['supplier_uid']]['deductyunbi'] = $order_all[$val['supplier_uid']]['deductyunbimoney'] / $ymoney * $ycredit;

            }
            $order_all[$val['supplier_uid']]['goodsprice'] = number_format($order_all[$val['supplier_uid']]['goodsprice'], 2);
            $order_all[$val['supplier_uid']]['totalprice'] = number_format($order_all[$val['supplier_uid']]['totalprice'], 2);
            if (p('channel') && $this->ischannelpay() == 1) {
                $order_all[$val['supplier_uid']]['discountprice'] = 0;
            }
            $order_all[$val['supplier_uid']]['discountprice'] = number_format($order_all[$val['supplier_uid']]['discountprice'], 2);
            $order_all[$val['supplier_uid']]['realprice'] = number_format($order_all[$val['supplier_uid']]['realprice'], 2);
            $order_all[$val['supplier_uid']]['dispatch_price'] = number_format($order_all[$val['supplier_uid']]['dispatch_price'], 2);

        }
        $supplierids = implode(',', array_keys($this->suppliers()));
        if ($this->isHotelGoods()) {
            $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
            $params2 = array(':goodsid' => $this->getGoodsId());
            $room = pdo_fetch($sql2, $params2);
            $pricefield = 'oprice';
            $r_sql = 'SELECT `roomdate`, `num`, `oprice`, `status`, ' . $pricefield . ' AS `m_price` FROM ' . tablename('sz_yi_hotel_room_price') .
                ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                ' `roomdate` < :etime';
            $btime = $_SESSION['data']['btime'];
            $etime = $_SESSION['data']['etime'];
            $params = array(':roomid' => $room['id'], ':btime' => $btime, ':etime' => $etime);
            $price_list = pdo_fetchall($r_sql, $params);
            $this_price = $old_price = $pricefield == 'cprice' ? $room['oprice'] * $member_p[$_W['member']['groupid']] : $room['roomprice'];
            if ($this_price == 0) {
                $this_price = $old_price = $room['oprice'];
            }
            $totalprice = $old_price * $days;
            if ($price_list) {//价格表中存在
                $check_date = array();
                foreach ($price_list as $k => $v) {
                    $price_list[$k]['time'] = date('Y-m-d', $v['roomdate']);
                    $new_price = $pricefield == 'mprice' ? $this_price : $v['m_price'];
                    $roomdate = $v['roomdate'];
                    if ($v['status'] == 0 || $v['num'] == 0) {
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
            } else {
                $goodsprice = round($goods[0]['marketprice']) * $days;
            }
            $realprice = $goodsprice + $goods[0]['deposit'];
            $deposit = $goods[0]['deposit'];
            $order_all[$g['supplier_uid']]['realprice'] = $goodsprice;
            $order_all[$g['supplier_uid']]['goodsprice'] = $goodsprice;


        }
        if ($this->getGoodsType() == 'recharge') {
            $changenum = false;
        }
        $variable = array(
            'show' => $this->getGoodsType() == 'recharge',
            //'diyform_flag' => $diyform_flag,
            'goods' => $goods,
        );

        return show_json(1, array(
            'member' => $member,
            //'deductmoney' => $deductmoney,//以前就没有赋值
            //'deductcredit2' => $deductcredit2,//以前就没有赋值
            'saleset' => $saleset,
            'goods' => $goods,
            'has' => $has,
            //'weight' => $weight / $buytotal,//以前就没有赋值
            'set' => $this->getShopSet(),
            'fromcart' => $this->_isFromCart(),
            'haslevel' => !empty($level) && $level['discount'] > 0 && $level['discount'] < 10,
            'total' => $this->getTotal(),
            'totalprice' => number_format($totalprice, 2),
            'goodsprice' => number_format($goodsprice, 2),
            //'discountprice' => number_format($discountprice, 2),//以前就没有赋值
            'discount' => $level['discount'],
            'realprice_total' => number_format($realprice_total, 2),
            'address' => $this->address(),

            'carrier' => $stores[0],
            'carrier_list' => $stores,
            'carrier_send' => $stores_send[0],
            'carrier_list_send' => $stores_send,
            'dispatch_list' => $dispatch_list,
            'isverify' => $this->isverify(),
            'isverifysend' => $this->isverifysend(),
            'dispatchsend' => $this->dispatchsend(),
            'stores' => $stores,
            'stores_send' => $stores_send,
            'isvirtual' => $this->isvirtual(),
            'changenum' => $changenum,

            'order_all' => $order_all,
            'supplierids' => $supplierids,
            "deposit" => number_format($deposit, 2),
            'price_list' => $price_list,
            'realprice' => number_format($realprice, 2),
            'hascouponplugin' => $this->hascouponplugin(),
            'type' => $goods[0]['type'],
            'card_deduct_total' => $card_deduct_total,
        ), $variable);
    }

    private function getTelephone()
    {
        global $_GPC;
        $telephone = intval($_GPC['telephone']) ? intval($_GPC['telephone']) : '';
        return $telephone;

    }

    private function getGoodsType()
    {
        if (p('recharge') && $this->getTelephone()) {
            return 'recharge';
        }
    }

    //预下单时可以更新商品数量
    private function changenum()
    {
        if ($this->_isFromCart()) {
            return false;
        }
        //充值
        if ($this->getGoodsType() == 'recharge') {
            return false;
        }
        //规格和数量为数字
        if (is_int($this->getTotal()) && is_int($this->optionid())) {
            return true;
        }
        //规格和数量为非空数组
        if (count($this->getTotal()) && count($this->getTotal())) {
            return true;
        }
        return false;
    }

    private function isDispath()
    {
        //如果开启核销并且不支持配送，则没有运费
        if ($this->isverify() && !$this->isverifysend() && !$this->dispatchsend()) {
            return false;
        }

    }

    private function address($key = null)
    {
        $fields = 'id,realname,mobile,address,province,city,area';
        //是否开启街道联动
        $trade = m('common')->getSysset('trade');
        if ($trade['is_street'] == '1') {
            $fields .= ',street';
        }
        $result = pdo_fetch('select ' . $fields . ' from ' . tablename('sz_yi_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(

            ':uniacid' => $this->getUniacid(),
            ':openid' => $this->getOpenid()
        ));
        if (isset($key)) {
            return $result[$key];
        }
        return $result;
    }
}
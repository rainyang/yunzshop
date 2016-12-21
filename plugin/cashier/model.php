<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

if (!class_exists('CashierModel')) {
    class CashierModel extends PluginModel
    {
        /**

         * 支付完成回调方法

         */
        public function payResult($params)
        {
            global $_W;
            $fee  = $params['fee'];
            $data = array(
                'status' => $params['result'] == 'success' ? 1 : 0
            );
            $ordersn = $params['tid'];
            $order   = pdo_fetch('select * from ' . tablename('sz_yi_order') . ' where  (ordersn=:ordersn or pay_ordersn=:ordersn or ordersn_general=:ordersn) and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid'],
                ':ordersn' => $ordersn
            ));
            $store = pdo_fetch(
                'select s.* from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $order['id']
                )
            );
            //验证paylog里金额是否与订单金额一致

            $log = pdo_fetch('select * from ' . tablename('core_paylog') . ' where `uniacid`=:uniacid and fee=:fee and `module`=:module and `tid`=:tid limit 1',

                array(
                ':uniacid' => $_W['uniacid'],
                ':module' => 'sz_yi',
                ':fee' => $fee,
                ':tid' => $params['tid']
            ));

            if (empty($log)) {
                return show_json(-1, '订单金额错误, 请重试!');
                exit;
            }

            $orderid = $order['id'];
            if ($params['from'] == 'return') {
                if ($order['status'] == 0 || $order['status'] == 1 || $order['status'] == 3) {
                    pdo_update('sz_yi_order', array(
                        'status' => 3,
                        'paytime' => time(),
                        'finishtime' => time()
                    ), array(
                        'id' => $orderid
                    ));
                    if ($order['deductcredit2'] > 0) {
                        $shopset = m('common')->getSysset('shop');
                        m('member')->setCredit($order['openid'], 'credit2', -$order['deductcredit2'], array(
                            0,
                            $shopset['name'] . "余额抵扣: {$order['deductcredit2']} 订单号: " . $order['ordersn']
                        ));
                    }
                    $this->setCredits($orderid);
                    if ($params['type'] != 'wechat') {
                         m('notice')->sendOrderMessage($orderid);
                    }
                    if (p('commission')) {
                        $this->calculateCommission($order['id']);
                    }
                    if ($order['price'] >= $store['condition']) {
                        $this->setCoupon($orderid);
                    }
                    
                }
            }
            if (p('return') && $store['isreturn'] == 1) {
                p('return')->cumulative_order_amount($orderid);
            }
            if (p('commission')) {
                p('commission')->upgradeLevelByOrder($orderid);
            }
        }

        /**
         * 消费者在商家支付完成后，获得的积分奖励百分比
         */
        function setCredits($orderid, $forStatistics = false)
        {
            global $_W;
            $order = pdo_fetch(
                'select id,ordersn,openid,price from ' . tablename('sz_yi_order') . ' where id=:id limit 1',
                array(':id' => $orderid)
            );
            $store = pdo_fetch(
                'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )
            );
            $credits = 0;
            if ($store['credit1'] > 0) {
                $credits += $order['price'] * $store['credit1'] / 100;
            }
            
            if ($credits > 0) {
                if ($forStatistics) {
                    return $credits;
                } else {
                    m('member')->setCredit($order['openid'], 'credit1', $credits, array(
                        0, '收银台奖励积分 订单号: ' . $order['ordersn']
                    ));
                }
            }
        }

        /**
         * 消费者在商家支付完成后，获得的余额奖励百分比
         */

        function setCredits2($orderid, $forStatistics = false)
        {
            global $_W;
            $order = pdo_fetch(
                'select id,ordersn,openid,price from ' . tablename('sz_yi_order') . ' where id=:id limit 1',
                array(':id' => $orderid)
            );
            $store = pdo_fetch(
                'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )
            );
            $credits = 0;
            if ($store['creditpack'] > 0) {
                $credits += $order['price'] * $store['creditpack'] / 100;
            }
            
            if ($credits > 0) {
                if ($forStatistics) {
                    return $credits;
                } else {
                    m('member')->setCredit($order['openid'], 'credit2', $credits, array(
                        0, '收银台奖励余额 订单号: ' . $order['ordersn']
                    ));
                }
            }
        }
        /**
         * 支付完成后，发放设置的优惠券
         */
        function setCoupon($orderid)
        {
            global $_W;
            $pcoupon = p('coupon');
            if (!$pcoupon) {
                return;
            }
            $order = pdo_fetch(
                'select * from ' . tablename('sz_yi_order') . ' where id=:id limit 1',
                array(':id' => $orderid)
            );
            $store = pdo_fetch(
                'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )
            );
            $couponid = $store['coupon_id'];
            if (!$couponid) {
                return;
            }
            $coupon   = $pcoupon->getCoupon($couponid);
            if (empty($coupon)) {
                return;
            }
            $logData = array(
                'uniacid'       => $_W['uniacid'],
                'openid'        => $order['openid'],
                'logno'         => m('common')->createNO('coupon_log', 'logno', 'CC'),
                'couponid'      => $couponid,
                'status'        => 1,
                'paystatus'     => -1,
                'creditstatus'  => -1,
                'createtime'    => time(),
                'getfrom'       => 0
            );
            pdo_insert('sz_yi_coupon_log', $logData);
            $data = array(
                'uniacid'   => $_W['uniacid'],
                'openid'    => $order['openid'],
                'couponid'  => $couponid,
                'gettype'   => 0,
                'gettime'   => time()
            );
            pdo_insert('sz_yi_coupon_data', $data);
        }

        /**
         * 相关上线获得佣金奖励，佣金比例在收银台商家中设置
         */
        function calculateCommission($orderid, $forStatistics = false)
        {
            global $_W;
            $pcom = p('commission');
            if (!$pcom) {
                return;
            }

            $set  = $pcom->getSet();

            $order = pdo_fetch(
                'select * from ' . tablename('sz_yi_order') . ' where id=:id limit 1',
                array(':id' => $orderid)
            );
            $store = pdo_fetch(
                'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )
            );
            if ($set['level'] > 0) {
                $realprice = $order['price'];
                $commissions                = array();
                $commissions['commission1'] = array('default' => 0);
                $commissions['commission2'] = array('default' => 0);
                $commissions['commission3'] = array('default' => 0);
                if ($set['level'] >= 1 && $store['commission1_rate'] > 0) {
                    $commissions['commission1'] = array('default' => round($store['commission1_rate'] * $realprice / 100, 2));
                }
                if ($set['level'] >= 2 && $store['commission2_rate'] > 0) {
                    $commissions['commission2'] = array('default' => round($store['commission2_rate'] * $realprice / 100, 2));
                }
                if ($set['level'] >= 3 && $store['commission3_rate'] > 0) {
                    $commissions['commission3'] = array('default' => round($store['commission3_rate'] * $realprice / 100, 2));
                }

                $levels = array('level1' => 0, 'level2' => 0, 'level3' => 0);
                if (!empty($order['agentid'])) {
                    
                    $user = m('member')->getMember($order['agentid']);
                    $commissionss = array();
                    if ($user['isagent'] == 1 && $user['status'] == 1) {
                        $levels['level1'] = round($commissions['commission1']['default'], 2);

                        $commissionss['commission1'] = iserializer($commissions['commission1']);
                        if (!empty($user['agentid'])) {
                            $puser = m('member')->getMember($user['agentid']);
                            $levels['level2'] = round($commissions['commission2']['default'], 2);

                            $commissionss['commission1'] = iserializer($commissions['commission1']);
                            $commissionss['commission2'] = iserializer($commissions['commission2']);
                            if (!empty($puser['agentid'])) {
                                $levels['level3'] = round($commissions['commission3']['default'], 2);

                                $commissionss['commission1'] = iserializer($commissions['commission1']);
                                $commissionss['commission2'] = iserializer($commissions['commission2']);
                                $commissionss['commission3'] = iserializer($commissions['commission3']);
                            }
                        }
                    }
                    $commissionss['commissions'] = iserializer($levels);
                    if ($forStatistics) {
                        $total = 0;
                        foreach ($levels as $level => $commission) {
                            $total += $commission;
                        }
                    } else {
                        pdo_update('sz_yi_order_goods', $commissionss,
                            array('orderid' => $orderid)
                        );
                    }
                }
            }
        }

        public function calculateBonus($orderid)
        {
            global $_W;
            
            $set = p('bonus')->getSet();
            $levels = p('bonus')->getLevels();
            $time = time();
            $order = pdo_fetch('select openid, address from ' . tablename('sz_yi_order') . ' where id=:id limit 1', array(':id' => $orderid));
            $openid = $order['openid'];
            $address = unserialize($order['address']);
            
            $goods = pdo_fetchall('select id,realprice,price,goodsid,total,optionname,optionid,bonusmoney from ' . tablename('sz_yi_order_goods') . ' where orderid=:orderid and uniacid=:uniacid', array(':orderid' => $orderid, ':uniacid' => $_W['uniacid']));
            $member = m('member')->getInfo($openid);
            $levels = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_bonus_level') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY level asc");
            $isdistinction = empty($set['isdistinction']) ? 0 : 1;
            foreach ($goods as $cinfo) {
                $cinfo['productprice'] = $cinfo['realprice'];
                $cinfo['marketprice'] = $cinfo['realprice'];
                $cinfo['costprice'] = $cinfo['realprice'];
                $price_all = p('bonus')->calculate_method($cinfo);
                if (!empty($cinfo['bonusmoney']) && $price_all > 0) {
                    if(empty($set['selfbuy'])){
                        $masid = $member['agentid'];
                    }else{
                        $masid = $member['id'];
                    }
                    //查询分红人员
                    if(!empty($masid) && !empty($set['start'])){
                        $parentAgents = p('bonus')->getParentAgents($masid, $isdistinction);
                        $range_money = 0;
                        foreach ($levels as $key => $level) {
                            $levelid = $level['id'];
                            if(array_key_exists($levelid, $parentAgents)){
                                if($level['agent_money'] > 0){
                                    $setmoney = $level['agent_money']/100;
                                }else{
                                    continue;
                                }
                                $bonus_money_old = round($price_all * $setmoney, 2);
                                //级差分红
                                if($isdistinction==0){
                                    $bonus_money = $bonus_money_old - $range_money;
                                    $range_money = $bonus_money_old;
                                }else{
                                    $bonus_money = $bonus_money_old;
                                }
                                //如分红金额小于0不写入
                                if($bonus_money <= 0){
                                    continue;
                                }
                                $data = array(
                                    'uniacid' => $_W['uniacid'],
                                    'ordergoodid' => $cinfo['goodsid'],
                                    'orderid' => $orderid,
                                    'total' => $cinfo['total'],
                                    'optionname' => $cinfo['optionname'],
                                    'mid' => $parentAgents[$levelid],
                                    'levelid' => $levelid,
                                    'money' => $bonus_money,
                                    'createtime' => $time
                                );
                                pdo_insert('sz_yi_bonus_goods', $data);
                            }
                            
                        }
                    }
                
                    //是否开启区域代理
                    $bonus_area_money_old = 0;
                    if(!empty($set['area_start'])){
                        //区级代理计算
                        $bonus_commission3 = floatval($set['bonus_commission3']);
                        if(!empty($bonus_commission3)){
                            $agent_districtall =  pdo_fetchall("select id, bonus_area_commission from " . tablename('sz_yi_member') . " where bonus_province='". $address['province']."' and bonus_city='". $address['city']."' and bonus_district='". $address['area']."' and bonus_area=3 and uniacid=".$_W['uniacid']);
                            if(!empty($agent_districtall)){
                                foreach ($agent_districtall as $key => $agent_district) {
                                    if($agent_district['bonus_area_commission'] > 0){
                                        $bonus_area_money_new = round($price_all * $agent_district['bonus_area_commission']/100, 2);
                                    }else{
                                        $bonus_area_money_new = round($price_all * $set['bonus_commission3']/100, 2);
                                    }
                                    if(empty($set['isdistinction_area'])){
                                        $bonus_area_money = $bonus_area_money_new - $bonus_area_money_old;
                                        $bonus_area_money_old = $bonus_area_money_new;
                                    }else{
                                        $bonus_area_money = $bonus_area_money_new;
                                    }
                                    if($bonus_area_money > 0){
                                        $data = array(
                                            'uniacid' => $_W['uniacid'],
                                            'ordergoodid' => $cinfo['goodsid'],
                                            'orderid' => $orderid,
                                            'total' => $cinfo['total'],
                                            'optionname' => $cinfo['optionname'],
                                            'mid' => $agent_district['id'],
                                            'bonus_area' => 3,
                                            'money' => $bonus_area_money,
                                            'createtime' => $time
                                        );
                                    }
                                    pdo_insert('sz_yi_bonus_goods', $data);
                                    if(empty($set['isdistinction_area']) || empty($set['isdistinction_area_all'])){
                                        break;
                                    }
                                }
                                
                            }
                        }
                        //市级代理计算
                        $bonus_commission2 = floatval($set['bonus_commission2']);
                        if(!empty($bonus_commission2)){
                            $agent_cityall =  pdo_fetchall("select id, bonus_area_commission from " . tablename('sz_yi_member') . " where bonus_province='". $address['province']."' and bonus_city='". $address['city']."' and bonus_area=2 and uniacid=".$_W['uniacid']);
                            
                            if(!empty($agent_cityall)){
                                foreach ($agent_cityall as $key => $agent_city) {
                                    if($agent_city['bonus_area_commission'] > 0){
                                        $bonus_area_money_new = round($price_all * $agent_city['bonus_area_commission']/100, 2);
                                    }else{
                                        $bonus_area_money_new = round($price_all * $set['bonus_commission2']/100, 2);
                                    }
                                    if(empty($set['isdistinction_area'])){
                                        $bonus_area_money = $bonus_area_money_new - $bonus_area_money_old;
                                        $bonus_area_money_old = $bonus_area_money_new;
                                    }else{
                                        $bonus_area_money = $bonus_area_money_new;
                                    }
                                    if($bonus_area_money > 0){
                                        $data = array(
                                            'uniacid' => $_W['uniacid'],
                                            'ordergoodid' => $cinfo['goodsid'],
                                            'orderid' => $orderid,
                                            'total' => $cinfo['total'],
                                            'optionname' => $cinfo['optionname'],
                                            'mid' => $agent_city['id'],
                                            'bonus_area' => 2,
                                            'money' => $bonus_area_money,
                                            'createtime' => $time
                                        );
                                        pdo_insert('sz_yi_bonus_goods', $data);
                                    }
                                    if(empty($set['isdistinction_area']) || empty($set['isdistinction_area_all'])){
                                        break;
                                    }
                                }
                            }
                        }
                        //省级代理计算
                        $bonus_commission1 = floatval($set['bonus_commission1']);
                        if(!empty($bonus_commission1)){
                            $agent_provinceall =  pdo_fetchall("select id, bonus_area_commission from " . tablename('sz_yi_member') . " where bonus_province='". $address['province']."' and bonus_area=1 and uniacid=".$_W['uniacid']);
                            if(!empty($agent_provinceall)){
                                foreach ($agent_provinceall as $key => $agent_province) {
                                    if($agent_province['bonus_area_commission'] > 0){
                                        $bonus_area_money_new = round($price_all * $agent_province['bonus_area_commission']/100, 2);
                                    }else{
                                        $bonus_area_money_new = round($price_all * $set['bonus_commission1']/100, 2);
                                    }
                                    if(empty($set['isdistinction_area'])){
                                        $bonus_area_money = $bonus_area_money_new - $bonus_area_money_old;
                                        $bonus_area_money_old = $bonus_area_money_new;
                                    }else{
                                        $bonus_area_money = $bonus_area_money_new;
                                    }
                                    if($bonus_area_money > 0){
                                        $data = array(
                                            'uniacid' => $_W['uniacid'],
                                            'ordergoodid' => $cinfo['goodsid'],
                                            'orderid' => $orderid,
                                            'total' => $cinfo['total'],
                                            'optionname' => $cinfo['optionname'],
                                            'mid' => $agent_province['id'],
                                            'bonus_area' => 1,
                                            'money' => $bonus_area_money,
                                            'createtime' => $time
                                        );
                                        pdo_insert('sz_yi_bonus_goods', $data);
                                    }
                                    if(empty($set['isdistinction_area']) || empty($set['isdistinction_area_all'])){
                                        break;
                                    }
                                }
                            }
                        } 
                    }
                }
            }
        }

        /**
         * 消费者在商家支付完成后，获得的红包奖励
         */
        public function redpack($openid, $orderid, $desc = '', $act_name = '', $remark = '')
        {
            global $_W;
            $order = pdo_fetch(
                'select id,ordersn,openid,price from ' . tablename('sz_yi_order') . ' where id=:id limit 1',
                array(':id' => $orderid)
            );
            $member  = m('member')->getMember($openid);
            $store = pdo_fetch(
                'select * from ' . tablename('sz_yi_cashier_order') . ' o inner join ' . tablename('sz_yi_cashier_store') . ' s on o.cashier_store_id = s.id where o.order_id=:orderid and o.uniacid=:uniacid',
                array(
                    ':uniacid' => $_W['uniacid'],
                    ':orderid' => $orderid
                )
            );
            // 订单金额如果小于发红包的最小限制，就不用发红包了
            if ($order['price'] < $store['redpack_min']) {
                return;
            }
            $money = $order['price'] * $store['redpack'] / 100;
            if ($money < 1 || $money > 200) {

                $credit2 =  $money;

                m('member')->setCredit($openid, 'credit2', $credit2, array(
                        0, '收银台红包奖励(超过红包最大金额限制,直接加到余额) 订单号: ' . $order['ordersn']
                    ));
                return;
            }

            if (empty($openid)) {
                return error(-1, 'openid不能为空');
            }
            $member = m('member')->getInfo($openid);
            if (empty($member)) {
                return error(-1, '未找到用户');
            }
            load()->model('payment');
            $setting = uni_setting($_W['uniacid'], array(
                'payment'
            ));
            if (!is_array($setting['payment'])) {
                return error(1, '没有设定支付参数');
            }
            $pay = m('common')->getSysset('pay');
            $wechat = $setting['payment']['wechat'];
            $sql = 'SELECT `key`,`secret`,`name` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
            $row = pdo_fetch($sql, array(
                ':uniacid' => $_W['uniacid']
            ));

            $url      = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
            $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $noncestr = '';
            for ($i = 0; $i < 32; $i++) {
                $noncestr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            $post = array(
                'wxappid'      => $row['key'],
                'mch_id'       => $wechat['mchid'],
                'mch_billno'   => $mchId . date('YmdHis') . rand(1000, 9999),
                'client_ip'    => gethostbyname($_SERVER["HTTP_HOST"]),
                're_openid'    => $openid,
                'total_amount' => $money * 100,
                'total_num'    => 1,
                'send_name'    => $row['name'],
                'wishing'      => empty($desc) ? '微信红包奖励' : $desc,
                'act_name'     => empty($act_name) ? '红包奖励' : $act_name,
                'remark'       => empty($remark) ? '红包奖励' : $remark,
                'nonce_str'    => $noncestr
            );
            ksort($post);

            $params = array();
            foreach ($post as $key => $val) {
                if ($key != 'sign' && $val != null && $val != 'null') {
                    $params[] = $key . '=' . $val;
                }
            }
            $stringA        = implode('&', $params);
            $stringSignTemp = $stringA . '&key=' . $wechat['apikey'];
            $post['sign']   = strtoupper(md5($stringSignTemp));

            $postXml = array2xml($post);
            $sec     = m('common')->getSec();
            $certs   = iunserializer($sec['sec']);
            if (is_array($certs)) {
                if (empty($certs['cert']) || empty($certs['key']) || empty($certs['root'])) {
                    message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
                }
                $certfile = IA_ROOT . "/addons/sz_yi/cert/" . random(128);
                file_put_contents($certfile, $certs['cert']);
                $keyfile = IA_ROOT . "/addons/sz_yi/cert/" . random(128);
                file_put_contents($keyfile, $certs['key']);
                $rootfile = IA_ROOT . "/addons/sz_yi/cert/" . random(128);
                file_put_contents($rootfile, $certs['root']);
                $extras = array(
                    'CURLOPT_SSLCERT' => $certfile,
                    'CURLOPT_SSLKEY'  => $keyfile,
                    'CURLOPT_CAINFO'  => $rootfile
                );
            } else {
                message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
            }
            load()->func('communication');
            $resp = ihttp_request($url, $postXml, $extras);
            @unlink($certfile);
            @unlink($keyfile);
            @unlink($rootfile);
            if (is_error($resp)) {
                return error(-2, $resp['message']);
            }
            if (empty($resp['content'])) {
                return error(-2, '网络错误');
            } else {
                $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])) , true);
                $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
                $dom = new DOMDocument();
                if ($dom->loadXML($xml)) {
                    $xpath = new DOMXPath($dom);
                    $code = $xpath->evaluate('string(//xml/return_code)');
                    $ret = $xpath->evaluate('string(//xml/result_code)');
                    if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
                        return true;
                    } else {
                        if ($xpath->evaluate('string(//xml/return_msg)') == $xpath->evaluate('string(//xml/err_code_des)')) {
                            $error = $xpath->evaluate('string(//xml/return_msg)');
                        } else {
                            $error = $xpath->evaluate('string(//xml/return_msg)') . "<br/>" . $xpath->evaluate('string(//xml/err_code_des)');
                        }

                        if (!empty($orderid)) {
                            $sql = 'SELECT `ordersn` FROM ' . tablename('sz_yi_order') . ' WHERE `id`=:orderid limit 1';
                            $row = pdo_fetch($sql,
                                array(
                                    ':orderid' => $orderid
                                )
                            );

                            $msg = array(
                                'keyword1' => array('value' => '收银台收款发送红包失败', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【订单编号】' . $row['ordersn'], 'color' => '#73a68d'),
                                'remark' => array('value' => '收银台收款红包发送失败！失败原因：'.$error)
                            );
                            pdo_update('sz_yi_order', 
                                array(
                                    'redstatus' => $error
                                ), 
                                array(
                                    'id' => $orderid
                                )
                            );
                            m('message')->sendCustomNotice($openid, $msg);
                        }
                        return error(-2, $error);
                    }

                } else {
                    return error(-1, '未知错误');
                }
            }
        }

    }
}

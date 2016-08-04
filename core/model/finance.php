<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Finance {
    //$params, $alipay = array(), $type = 0, $openid = ''
    function getHttpResponseGET($url,$cacert_url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 1); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
        /*
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
         */

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        
        return $responseText;
    }
    public function alipay_build($openid = '', $paytype = 0, $money = 0, $trade_no = '', $desc = '')
    {
        global $_W;
        $set                   = array();
        $set['partner']        = '2088121517115776';//$alipay['partner'];
        //$set['seller_id']    = $alipay['partner'];  //即时到帐情况下sellerid = partner
        $set['service']        = 'batch_trans_notify';
        $set['_input_charset'] = 'utf-8';
        $set['sign_type']      = 'MD5';
        $set['notify_url']     = $_W['siteroot'] . "addons/sz_yi/payment/alipay/notify.php";
        $set['email']          = '3303063404@qq.com';
        $set['account_name']   = '哈尔滨思卓信息科技有限公司';
        $set['pay_date']       = '20160804';
        $set['batch_no']       = '20160804016';
        $set['batch_fee']      = 1.00;
        $set['batch_num']      = 1;
        $set['detail_data']    = '20160804016^18646588292^矫春艳^1.00^备注说明1';
        $prepares            = array();
        foreach ($set as $key => $value) {
            if ($key != 'sign' && $key != 'sign_type') {
                $prepares[] = "{$key}={$value}";
            }
        }
        sort($prepares);
        $string = implode($prepares, '&');
        //$string .= $alipay['secret'];
        $string .= '562pmytrtu0vjkrmp8id23uwrlkd1ng1';
        $set['sign'] = md5($string);
        $url = 'https://mapi.alipay.com/gateway.do' . '?' . http_build_query($set, '', '&');
        $resp = $this->getHttpResponseGET($url, IA_ROOT . "/addons/sz_yi/cert/cacert.pem");
        print_r($resp);
        exit;
        //echo $resp;
        //exit;
        //echo $resp;
        //echo $url;exit;
        /*
        load()->func('communication');
        $resp = ihttp_request($url);
        print_r($resp);
        header("Location:" . $resp['headers']['Location']);
        exit;
         */
    }

    public function pay($openid = '', $paytype = 0, $money = 0, $trade_no = '', $desc = '')
    {
        global $_W, $_GPC;
        if (empty($openid)) {
            return error(-1, 'openid不能为空');
        }
        $member = m('member')->getInfo($openid);
        if (empty($member)) {
            return error(-1, '未找到用户');
        }
        if (empty($paytype)) {
            m('member')->setCredit($openid, 'credit2', $money, array(
                0,
                $desc
            ));
            return true;
        } else {
            $setting = uni_setting($_W['uniacid'], array(
                'payment'
            ));
            if (!is_array($setting['payment'])) {
                return error(1, '没有设定支付参数');
            }
            $pay = m('common')->getSysset('pay');
            if ($paytype == 3) {
                $this->alipay_build($openid, $paytype, $money, $trade_no, $desc);
            }
            $wechat = $setting['payment']['wechat'];
            $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
            $row = pdo_fetch($sql, array(
                ':uniacid' => $_W['uniacid']
            ));

            $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
            $pars = array();
            $pars['mch_appid'] = $row['key'];
            $pars['mchid'] = $wechat['mchid'];
            $pars['nonce_str'] = random(32);
            $pars['partner_trade_no'] = empty($trade_no) ? time() . random(4, true) : $trade_no;
            $pars['openid'] = $openid;
            $pars['check_name'] = 'NO_CHECK';
            $pars['amount'] = $money;
            $pars['desc'] = empty($desc) ? '佣金提现' : $desc;
            $pars['spbill_create_ip'] = gethostbyname($_SERVER["HTTP_HOST"]);
            ksort($pars, SORT_STRING);
            $string1 = '';
            foreach ($pars as $k => $v) {
                $string1.= "{$k}={$v}&";
            }

            $string1.= "key=" . $wechat['apikey'];
            $pars['sign'] = strtoupper(md5($string1));
            $xml = array2xml($pars);
            $extras = array();
            
            $sec = m('common')->getSec();
            $certs = iunserializer($sec['sec']);
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
                $extras['CURLOPT_SSLCERT'] = $certfile;
                $extras['CURLOPT_SSLKEY'] = $keyfile;
                $extras['CURLOPT_CAINFO'] = $rootfile;

                // $extras['CURLOPT_SSLCERT'] = IA_ROOT . '/addons/sz_yi/cert/apiclient_cert.pem';
                // $extras['CURLOPT_SSLKEY'] = IA_ROOT . '/addons/sz_yi/cert/apiclient_key.pem';
                // $extras['CURLOPT_CAINFO'] = IA_ROOT . '/addons/sz_yi/cert/rootca.pem';
            } else {
                message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
            }
            load()->func('communication');
            $resp = ihttp_request($url, $xml, $extras);
            @unlink($certfile);
            @unlink($keyfile);
            @unlink($rootfile);
            if (is_error($resp)) {
                return error(-2, $resp['message']);
            }
            if (empty($resp['content'])) {
                return error(-2, '网络错误');
            } else {
                $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])), true);
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
                        return error(-2, $error);
                    }
                } else {
                    return error(-1, '未知错误');
                }
            }
        }
    }

    //发送红包
    public function sendredpack($openid, $money, $orderid, $desc = '', $act_name = '', $remark = '')
    {
        global $_W;
        //查询公众号名称
        $_W['account']['name'] = pdo_fetchcolumn("SELECT name FROM ". tablename("uni_account") . "WHERE uniacid = '".$_W['uniacid']."'");
        if (empty($openid)) {
            return error(-1, 'openid不能为空');
        }
        $member = m('member')->getInfo($openid);
        if (empty($member)) {
            return error(-1, '未找到用户');
        }
        //查询支付参数配置
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return error(1, '没有设定支付参数');
        }
        $pay = m('common')->getSysset('pay');
        $wechat = $setting['payment']['wechat'];
        //查询微信公众号参数信息
        $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
        $row = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        //接口地址及相应参数
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $post = array(
            'wxappid'      => $row['key'],
            'mch_id'       => $wechat['mchid'],
            'mch_billno'   => $wechat['mchid'] . date('YmdHis') . rand(1000, 9999),
            'client_ip'    => gethostbyname($_SERVER["HTTP_HOST"]),
            're_openid'    => $openid,
            'total_amount' => $money,
            'total_num'    => 1,
            'send_name'    => $_W['account']['name'],
            'wishing'      => empty($desc) ? '佣金提现红包' : $desc,
            'act_name'     => empty($act_name) ? '佣金提现红包' : $act_name,
            'remark'       => empty($remark) ? '佣金提现红包' : $remark,
            'nonce_str'    => $this->createNonceStr()
        );

        $stringA           = $this->formatQuery($post, false);
        $stringSignTemp    = $stringA . '&key=' . $wechat['apikey'];
        $post['sign']      = strtoupper(md5($stringSignTemp));
        $postXml = array2xml($post, 1);
        $sec     = m('common')->getSec();
        //上传微信支付证书信息
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
            // $extras['CURLOPT_SSLCERT'] = IA_ROOT . '/addons/sz_yi/cert/apiclient_cert.pem';
            // $extras['CURLOPT_SSLKEY'] = IA_ROOT . '/addons/sz_yi/cert/apiclient_key.pem';
            // $extras['CURLOPT_CAINFO'] = IA_ROOT . '/addons/sz_yi/cert/rootca.pem';
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
            $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])), true);
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
            $dom = new DOMDocument();
            if ($dom->loadXML($xml)) {
                $xpath = new DOMXPath($dom);
                $code = $xpath->evaluate('string(//xml/return_code)');
                $ret = $xpath->evaluate('string(//xml/result_code)');
                if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
                    //发送成功
                    return true;
                } else {
                    if ($xpath->evaluate('string(//xml/return_msg)') == $xpath->evaluate('string(//xml/err_code_des)')) {
                        $error = $xpath->evaluate('string(//xml/return_msg)');
                    } else {
                        $error = $xpath->evaluate('string(//xml/return_msg)') . "<br/>" . $xpath->evaluate('string(//xml/err_code_des)');
                    }
                    //失败后更新发送状态字段
                    if (!empty($orderid) && $orderid != 0) {
                        $sql = 'SELECT `ordersn` FROM ' . tablename('sz_yi_order') . ' WHERE `id`=:orderid limit 1';
                        $row = pdo_fetch($sql,
                            array(
                                ':orderid' => $orderid
                            )
                        );
                        
                        if (!empty($row)) {
                            $_var_156 = array(
                                'keyword1' => array('value' => '购买商品发送红包失败', 'color' => '#73a68d'),
                                'keyword2' => array('value' => '【订单编号】' . $row['ordersn'], 'color' => '#73a68d'),
                                'remark' => array('value' => '购物赠送红包发送失败！失败原因：'.$error)
                            );
                            pdo_update('sz_yi_order',
                                array(
                                    'redstatus' => $error
                                ),
                                array(
                                    'id' => $orderid
                                )
                            );
                            m('message')->sendCustomNotice($openid, $_var_156);
                        }
                    }
                    
                    return error(-2, $error);
                }
            } else {
                return error(-1, '未知错误');
            }
        }
    }

    public function refund($openid, $out_trade_no, $out_refund_no, $totalmoney, $refundmoney = 0)
    {
        global $_W, $_GPC;
        if (empty($openid)) {
            return error(-1, 'openid不能为空');
        }
        $member = m('member')->getInfo($openid);
        if (empty($member)) {
            return error(-1, '未找到用户');
        }
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return error(1, '没有设定支付参数');
        }
        $pay = m('common')->getSysset('pay');
        $wechat = $setting['payment']['wechat'];
        $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
        $row = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $pars = array();
        $pars['appid'] = $row['key'];
        $pars['mch_id'] = $wechat['mchid'];
        $pars['nonce_str'] = random(8);
        $pars['out_trade_no'] = $out_trade_no;
        $pars['out_refund_no'] = $out_refund_no;
        $pars['total_fee'] = $totalmoney;
        $pars['refund_fee'] = $refundmoney;
        $pars['op_user_id'] = $wechat['mchid'];
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1.= "{$k}={$v}&";
        }
        $string1.= "key=" . $wechat['apikey'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        $extras = array();
        $sec = m('common')->getSec();
        $certs = iunserializer($sec['sec']);
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
            $extras['CURLOPT_SSLCERT'] = $certfile;
            $extras['CURLOPT_SSLKEY'] = $keyfile;
            $extras['CURLOPT_CAINFO'] = $rootfile;
        } else {
            message('未上传完整的微信支付证书，请到【系统设置】->【支付方式】中上传!', '', 'error');
        }
        load()->func('communication');
        $resp = ihttp_request($url, $xml, $extras);
        @unlink($certfile);
        @unlink($keyfile);
        @unlink($rootfile);
        if (is_error($resp)) {
            return error(-2, $resp['message']);
        }
        if (empty($resp['content'])) {
            return error(-2, '网络错误');
        } else {
            $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])), true);
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
                    return error(-2, $error);
                }
            } else {
                return error(-1, '未知错误');
            }
        }
    }

    public function downloadbill($starttime, $endtime, $type = 'ALL')
    {
        global $_W, $_GPC;
        $dates = array();
        $startdate = date('Ymd', $starttime);
        $enddate = date('Ymd', $endtime);
        if ($startdate == $enddate) {
            $dates = array(
                $startdate
            );
        } else {
            $days = (float)($endtime - $starttime) / 86400;
            for ($d = 0; $d < $days; $d++) {
                $dates[] = date('Ymd', strtotime($startdate . "+{$d} day"));
            }
        }
        if (empty($dates)) {
            message('对账单日期选择错误!', '', 'error');
        }
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return error(1, '没有设定支付参数');
        }
        $wechat = $setting['payment']['wechat'];
        $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
        $row = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        $content = "";
        foreach ($dates as $date) {
            $dc = $this->downloadday($date, $row, $wechat, $type);
            if (is_error($dc) || strexists($dc, 'CDATA[FAIL]')) {
                continue;
            }
            $content.= $date . " 账单\r\n\r\n";
            $content.= $dc . "\r\n\r\n";
        }
        $file = time() . ".csv";
        header('Content-type: application/octet-stream ');
        header('Accept-Ranges: bytes ');
        header("Content-Disposition: attachment; filename={$file}");
        header('Expires: 0 ');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0 ');
        header('Pragma: public ');
        die($content);
    }

    private function downloadday($date, $row, $wechat, $type)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/downloadbill';
        $pars = array();
        $pars['appid'] = $row['key'];
        $pars['mch_id'] = $wechat['mchid'];
        $pars['nonce_str'] = random(8);
        $pars['device_info'] = "sz_yi";
        $pars['bill_date'] = $date;
        $pars['bill_type'] = $type;
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1.= "{$k}={$v}&";
        }
        $string1.= "key=" . $wechat['apikey'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        $extras = array();
        load()->func('communication');
        $resp = ihttp_request($url, $xml, $extras);
        if (strexists($resp['content'], 'No Bill Exist')) {
            return error(-2, '未搜索到任何账单');
        }
        if (is_error($resp)) {
            return error(-2, $resp['message']);
        }
        if (empty($resp['content'])) {
            return error(-2, '网络错误');
        } else {
            return $resp['content'];
        }
    }

    public function closeOrder($out_trade_no = '')
    {
        global $_W, $_GPC;
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return error(1, '没有设定支付参数');
        }
        $wechat = $setting['payment']['wechat'];
        $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
        $row = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';
        $pars = array();
        $pars['appid'] = $row['key'];
        $pars['mch_id'] = $wechat['mchid'];
        $pars['nonce_str'] = random(8);
        $pars['out_trade_no'] = $out_trade_no;
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1.= "{$k}={$v}&";
        }
        $string1.= "key=" . $wechat['apikey'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        load()->func('communication');
        $resp = ihttp_post($url, $xml);
        if (is_error($resp)) {
            return error(-2, $resp['message']);
        }
        if (empty($resp['content'])) {
            return error(-2, '网络错误');
        } else {
            $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])), true);
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
            $dom = new DOMDocument();
            if ($dom->loadXML($xml)) {
                $xpath = new DOMXPath($dom);
                $code = $xpath->evaluate('string(//xml/return_code)');
                $ret = $xpath->evaluate('string(//xml/result_code)');
                $trade_state = $xpath->evaluate('string(//xml/trade_state)');
                if (strtolower($code) == 'success' && strtolower($ret) == 'success' && strtolower($trade_state) == 'success') {
                    return true;
                } else {
                    if ($xpath->evaluate('string(//xml/return_msg)') == $xpath->evaluate('string(//xml/err_code_des)')) {
                        $error = $xpath->evaluate('string(//xml/return_msg)');
                    } else {
                        $error = $xpath->evaluate('string(//xml/return_msg)') . "<br/>" . $xpath->evaluate('string(//xml/err_code_des)');
                    }
                    return error(-2, $error);
                }
            } else {
                return error(-1, '未知错误');
            }
        }
    }

    public function isWeixinPay($out_trade_no)
    {
        global $_W, $_GPC;
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return error(1, '没有设定支付参数');
        }
        $wechat = $setting['payment']['wechat'];
        $sql = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid limit 1';
        $row = pdo_fetch($sql, array(
            ':uniacid' => $_W['uniacid']
        ));
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $pars = array();
        $pars['appid'] = $row['key'];
        $pars['mch_id'] = $wechat['mchid'];
        $pars['nonce_str'] = random(8);
        $pars['out_trade_no'] = $out_trade_no;
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1.= "{$k}={$v}&";
        }
        $string1.= "key=" . $wechat['apikey'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        load()->func('communication');
        $resp = ihttp_post($url, $xml);
        if (is_error($resp)) {
            return error(-2, $resp['message']);
        }
        if (empty($resp['content'])) {
            return error(-2, '网络错误');
        } else {
            $arr = json_decode(json_encode((array)simplexml_load_string($resp['content'])), true);
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
            $dom = new DOMDocument();
            if ($dom->loadXML($xml)) {
                $xpath = new DOMXPath($dom);
                $code = $xpath->evaluate('string(//xml/return_code)');
                $ret = $xpath->evaluate('string(//xml/result_code)');
                $trade_state = $xpath->evaluate('string(//xml/trade_state)');
                if (strtolower($code) == 'success' && strtolower($ret) == 'success' && strtolower($trade_state) == 'success') {
                    return true;
                } else {
                    if ($xpath->evaluate('string(//xml/return_msg)') == $xpath->evaluate('string(//xml/err_code_des)')) {
                        $error = $xpath->evaluate('string(//xml/return_msg)');
                    } else {
                        $error = $xpath->evaluate('string(//xml/return_msg)') . "<br/>" . $xpath->evaluate('string(//xml/err_code_des)');
                    }
                    return error(-2, $error);
                }
            } else {
                return error(-1, '未知错误');
            }
        }
    }

    public function isAlipayNotify($gpc)
    {
        global $_W;
        $notify_id = trim($gpc['notify_id']);
        $notify_sign = trim($gpc['sign']);
        if (empty($notify_id) || empty($notify_sign)) {
            return false;
        }
        $setting = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        if (!is_array($setting['payment'])) {
            return false;
        }
        $alipay = $setting['payment']['alipay'];
        $params = array();
        foreach ($gpc as $key => $value) {
            if (in_array($key, array(
                'sign',
                'sign_type',
                'i',
                'm',
                'openid',
                'c',
                'do',
                'p',
                'op'
            )) || empty($value)) {
                continue;
            }
            $params[$key] = $value;
        }
        ksort($params, SORT_STRING);
        $string1 = '';
        foreach ($params as $k => $v) {
            $string1.= "{$k}={$v}&";
        }
        $string1 = rtrim($string1, '&') . $alipay['secret'];
        $sign = strtolower(md5($string1));
        if ($notify_sign != $sign) {
            return false;
        }
        $url = "https://mapi.alipay.com/gateway.do?service=notify_verify&partner={$alipay['partner']}&notify_id={$notify_id}";
        $resp = @file_get_contents($url);
        return preg_match("/true$/i", $resp);
    }

    //红包
    protected function createNonceStr()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str   = '';
        for ($i = 0; $i < 32; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    //红包
    protected function formatQuery($querys, $urlencode = false)
    {
        if (!is_array($querys) || empty($querys)) {
            return;
        }
        ksort($querys);

        $params = array();
        foreach ($querys as $key => $val) {
            if ($key != 'sign' && $val != null && $val != 'null') {
                if ($urlencode) {
                    $val = urlencode($val);
                }
                $params[] = $key . '=' . $val;
            }
        }

        return implode('&', $params);
    }
}

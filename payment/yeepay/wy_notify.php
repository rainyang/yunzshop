<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/9/29
 * Time: 上午10:00
 */
//芸众商城 QQ:913768135
error_reporting(0);
define('IN_MOBILE', true);
if (!empty($_GET)) {
    require '../../../../../framework/bootstrap.inc.php';
    require '../../../../../addons/sz_yi/defines.php';
    require '../../../../../addons/sz_yi/core/inc/functions.php';
    require '../../../../../addons/sz_yi/core/inc/plugin/plugin_model.php';

    $dir = dirname(__FILE__);
    $uniacid = substr($dir,strrpos($dir,'/')+1);

    include("../../../core/inc/plugin/vendor/yeepay/yeepay/yeepayMPay.php");
    $setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
        ':uniacid' => $uniacid
    ));
    $set     = unserialize($setdata['sets']);

    $merchantaccount= $set['pay']['merchantaccount'];
    $merchantPublicKey= $set['pay']['merchantPublicKey'];
    $merchantPrivateKey= $set['pay']['merchantPrivateKey'];
    $yeepayPublicKey= $set['pay']['yeepayPublicKey'];

    $p1_MerId		= $set['pay']['merchantaccount'];
    $merchantKey	= $set['pay']['merchantKey'];

    $data=array();
    if ( $_REQUEST['r9_BType'] == 1)
    {
        $data['p1_MerId']		 = $_REQUEST['p1_MerId'];
        $data['r0_Cmd']		   = $_REQUEST['r0_Cmd'];
        $data['r1_Code']	   = $_REQUEST['r1_Code'];
        $data['r2_TrxId']    = $_REQUEST['r2_TrxId'];
        $data['r3_Amt']      = $_REQUEST['r3_Amt'];
        $data['r4_Cur']		   = $_REQUEST['r4_Cur'];
        $data['r5_Pid']		   = iconv("utf-8","gbk",$_REQUEST['r5_Pid']);
        $data['r6_Order']	   = $_REQUEST['r6_Order'];
        $data['r7_Uid']		   = $_REQUEST['r7_Uid'];
        $data['r8_MP']		   = iconv("utf-8","gbk",$_REQUEST['r8_MP']);
        $data['r9_BType']	   = $_REQUEST['r9_BType'];
        $data['hmac']			   = $_REQUEST['hmac'];
        $data['hmac_safe']   = $_REQUEST['hmac_safe'];
    }
    else
    {
        $data['p1_MerId']		 = $_REQUEST['p1_MerId'];
        $data['r0_Cmd']		   = $_REQUEST['r0_Cmd'];
        $data['r1_Code']	   = $_REQUEST['r1_Code'];
        $data['r2_TrxId']    = $_REQUEST['r2_TrxId'];
        $data['r3_Amt']      = $_REQUEST['r3_Amt'];
        $data['r4_Cur']		   = $_REQUEST['r4_Cur'];
        $data['r5_Pid']		   = $_REQUEST['r5_Pid'] ;
        $data['r6_Order']	   = $_REQUEST['r6_Order'];
        $data['r7_Uid']		   = $_REQUEST['r7_Uid'];
        $data['r8_MP']		   = $_REQUEST['r8_MP'] ;
        $data['r9_BType']	   = $_REQUEST['r9_BType'];
        $data['hmac']			   = $_REQUEST['hmac'];
        $data['hmac_safe']   = $_REQUEST['hmac_safe'];
    }
//var_dump($data);
    //本地签名
    $hmacLocal = HmacLocal($data);
// echo "</br>hmacLocal:".$hmacLocal;
    $safeLocal= gethamc_safe($data);
// echo "</br>safeLocal:".$safeLocal;

    //验签
//    if($data['hmac']	 != $hmacLocal    || $data['hmac_safe'] !=$safeLocal)
//    {
//        echo "验签失败";
//        return;
//    }

    $out_trade_no =$data['r6_Order'];


    $total_fee = $data['r3_Amt'];  //最小单位(元)
    $trade_no = $data['r2_TrxId'];

    $_W['uniacid'] = $_W['weid'] = intval($uniacid);
    $type = 0;
    if ($type == 0) {
        $paylog = "\r\n-------------------------------------------------\r\n";
        $paylog .= "orderno: " . $out_trade_no . "\r\n";
        $paylog .= "paytype: yeepay_wy\r\n";
        $paylog .= "data: " . json_encode($data) . "\r\n";
        m('common')->paylog($paylog);
    }
    $setting = uni_setting($_W['uniacid'], array('payment'));
    if (is_array($setting['payment'])) {
        $alipay = $setting['payment']['alipay'];
        if (!empty($alipay)) {
            m('common')->paylog("setting: ok\r\n");

            if ($data['r1_Code'] == 1) {
                m('common')->paylog("sign: ok\r\n");
                if (empty($type)) {
                    $tid = $out_trade_no;
                    if (strexists($tid, 'GJ')) {
                        $tids = explode("GJ", $tid);
                        $tid = $tids[0];
                    }
                    $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `tid`=:tid and `module`=:module limit 1';
                    $params = array();
                    $params[':tid'] = $tid;
                    $params[':module'] = 'sz_yi';
                    $log = pdo_fetch($sql, $params);
                    m('common')->paylog('log: ' . (empty($log) ? '' : json_encode($log)) . "\r\n");
                    if (!empty($log) && $log['status'] == '0' &&  bccomp($log['fee'], $total_fee, 2) == 0) {

                        m('common')->paylog("corelog: ok\r\n");
                        $site = WeUtility::createModuleSite($log['module']);
                        if (!is_error($site)) {
                            $method = 'payResult';
                            if (method_exists($site, $method)) {
                                $ret = array();
                                $ret['weid'] = $log['weid'];
                                $ret['uniacid'] = $log['uniacid'];
                                $ret['result'] = 'success';
                                $ret['type'] = $log['type'];
                                $ret['from'] = 'return';
                                $ret['tid'] = $log['tid'];
                                $ret['user'] = $log['openid'];
                                $ret['fee'] = $log['fee'];
                                $ret['is_usecard'] = $log['is_usecard'];
                                $ret['card_type'] = $log['card_type'];
                                $ret['card_fee'] = $log['card_fee'];
                                $ret['card_id'] = $log['card_id'];
                                m('common')->paylog('method: execute');
                                $result = $site->$method($ret);
                                if (is_array($result) && $result['result'] == 'success') {
                                    $record = array();
                                    $record['status'] = '1';
                                    pdo_update('core_paylog', $record, array('plid' => $log['plid']));
                                    $orders = array('trade_no'=>$trade_no);
                                    pdo_update('sz_yi_order', $orders, array('pay_ordersn' =>$out_trade_no,'uniacid'=>$log['uniacid']));
                                    exit('success');
                                }
                            } else {
                                m('common')->paylog('method not found!
');
                            }
                        } else {
                            m('common')->paylog('error: ' . json_encode($site) . "\r\n");
                        }
                    }
                }
            }
        }
    }
}
exit('fail');

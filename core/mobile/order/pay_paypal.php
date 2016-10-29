<?php
/**
 * Created by Sublime Text3.
 * User: yitian
 * Date: 16/10/27
 * Time: 上午10:10
 * QQ: 995265288
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
if (empty($openid)) {
    $openid = $_GPC['openid'];
}

$uniacid = $_W['uniacid'];
$orderid = intval($_GPC['orderid']);

$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
	':uniacid' => $_W['uniacid']
));
$set     = unserialize($setdata['sets']);
//$oldset  = unserialize($setdata['sets']);
$paypal = $set['pay']['paypal'];
$paypal['paypalstatus'] = $set['pay']['paypalstatus'];

//$logid   = intval($_GPC['logid']);
//$shopset = m('common')->getSysset('shop');
//$member  = m('member')->getMember($openid);
if (!empty($orderid) && $operation = 'display') {
	
    $order = pdo_fetch("select * from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
        ':id' => $orderid,
        ':uniacid' => $uniacid,
        ':openid' => $openid
    ));
    if (empty($order)) {
        show_json(0, '订单未找到!');
    }
    $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
        ':uniacid' => $uniacid,
        ':module' => 'sz_yi',
        ':tid' => $order['ordersn_general']
    ));
    if (!empty($log) && $log['status'] != '0') {
        show_json(0, '订单已支付, 无需重复支付!');
    }
	//echo "<pre>"; print_r($log);exit;
/*	load()->func('communication');
	load()->model('payment');
*/



	//echo "<pre>"; print_r($paypal);exit;

/*	$setting = uni_setting($_W['uniacid'], array('payment'));
	if (is_array($setting['payment'])) {
		$options = $setting['payment']['paypal'];
	}*/

	if (empty($paypal['currencies'])) {
		$paypal['currencies'] = 1;
	}
	$paymentAmount = $order['price'] * $paypal['currencies'];

	$paymentType = 'Sale';
	$currencyCodeType = $paypal['currency'];
	$data_order_id = $log['tid'];
	
	$payPalURL = 'https://www.paypal.com/cgi-bin/webscr&cmd=_express-checkout&token=';
	
	$serverName = $_SERVER['SERVER_NAME'];
	$serverPort = $_SERVER['SERVER_PORT'];
	$url=dirname('http://'.$serverName.':'.$serverPort.$_SERVER['REQUEST_URI']);
	
    $returnURL = urlencode($_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&m=sz_yi&do=order&p=pay_paypal&op=returnpaypal&openid=" . $openid . '&currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount.'&invoice='.$data_order_id);

	$cancelURL =urlencode($_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&p=pay&do=order&m=sz_yi&orderid={$orderid}&openid=" . $openid);
	$nvpstr="&Amt=".$paymentAmount."&PAYMENTACTION=".$paymentType."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType;
	
	$resArray=hash_call("SetExpressCheckout",$nvpstr,$paypal);
	//echo "<pre>"; print_r(789);exit;
	$token = urldecode($resArray["TOKEN"]);
	$payPalURL .= $token;
	//echo "<pre>"; print_r($paypal);exit;
	die("<script>window.location.href='".$payPalURL."';</script>");
}else if ($operation == 'returnpaypal') {
	$tid = $_REQUEST['invoice'];
	$token =urlencode($_REQUEST['token']);
	$nvpstr="&TOKEN=".$token;
	
/*	load()->func('communication');
	load()->model('payment');*/
	//$setting = uni_setting($_W['uniacid'], array('payment'));

/*	if (is_array($setting['payment'])) {
		$options = $setting['payment']['paypal'];
	}*/
/*	if (empty($paypal['currencies'])) {
		$paypal['currencies'] = 1;
	}*/
	
    $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr,$paypal);
	$ack = strtoupper($resArray["ACK"]);
	if($ack=="SUCCESS"){
		//&currencyCodeType=AUD&paymentType=Sale&paymentAmount=0.2&invoice=SH201606071207472862&token=EC-4W177963XS430313R&PayerID=C3VJUFK6DQZNN
		$payerID = urlencode($_REQUEST['PayerID']);
		$paymentType = urlencode($_REQUEST['paymentType']);
		$paymentAmount =urlencode ($_REQUEST['paymentAmount']);
		$currencyCodeType = urlencode($_REQUEST['currencyCodeType']);
		$serverName = urlencode($_SERVER['SERVER_NAME']);
		$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currencyCodeType.'&IPADDRESS='.$serverName;
		$resArray=hash_call("DoExpressCheckoutPayment",$nvpstr,$paypal);
		$ack = strtoupper($resArray["ACK"]);
        if($ack=="SUCCESS"){
			$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
				':uniacid' => $_W['uniacid'],
				':module' => 'sz_yi',
				':tid' => $tid
			));
			if (empty($log)) {
				die('支付出现错误，请重试!');
			}
			if ($log['status'] != 1) {
				$record           = array();
				$record['status'] = '1';
				$record['type']   = 'paypal';
				pdo_update('core_paylog', $record, array(
					'plid' => $log['plid']
				));
				$ret            = array();
				$ret['result']  = 'success';
				$ret['type']    = 'paypal';
				$ret['from']    = 'return';
				$ret['tid']     = $log['tid'];
				$ret['user']    = $log['openid'];
				$ret['fee']     = $log['fee'];
				$ret['weid']    = $log['weid'];
				$ret['uniacid'] = $log['uniacid'];
				$this->payResult($ret);
			}
/*			$orderid = pdo_fetchcolumn('select id from ' . tablename('sz_yi_order') . ' where ordersn=:ordersn and uniacid=:uniacid', array(
				':ordersn_general' => $log['tid'],
				':uniacid' => $_W['uniacid']
			));*/
			$url     = $this->createMobileUrl('order/list', array(
				'status' => ''
			));
			die("<script>top.window.location.href='{$url}'</script>");
		}
		die('支付出现错误，请重试!');
	}else{
        die('支付出现错误，请重试!');
    }

}
	
function hash_call($methodName,$nvpStr,$paypal)
{
    global $API_Endpoint;
    $version='53.0';
    $API_UserName=$paypal['mchid'];
    $API_Password=$paypal['key'];
    $API_Signature=$paypal['signkey'];
    $nvp_Header;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'https://api-3t.paypal.com/nvp');
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $nvpreq="METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($API_Password)."&USER=".urlencode($API_UserName)."&SIGNATURE=".urlencode($API_Signature).$nvpStr;

    curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

    $response = curl_exec($ch);

    $nvpResArray=deformatNVP($response);
    $nvpReqArray=deformatNVP($nvpreq);
    curl_close($ch);

    return $nvpResArray;
}


function deformatNVP($nvpstr)
{

    $intial=0;
    $nvpArray = array();

    while(strlen($nvpstr))
    {
        $keypos= strpos($nvpstr,'=');
        $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
        $keyval=substr($nvpstr,$intial,$keypos);
        $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
        $nvpArray[urldecode($keyval)] =urldecode( $valval);
        $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
    }
    return $nvpArray;
}

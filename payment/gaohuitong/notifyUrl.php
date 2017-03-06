<?php

/* 页面转跳即时返回通知页面回调示例  */

require_once ("./classes/GatewayNotify.class.php");

/* 商户密钥 */
$key = "857e6g8y51b5k365f7v954s50u24h14w";


$notify = new GatewayNotify();
$notify->setKey($key);

//验证签名
if($notify->verifySign()) {
	
	$busi_code = $notify->getParameter("busi_code");
	$merchant_no = $notify->getParameter("merchant_no");
	$terminal_no = $notify->getParameter("terminal_no");
	$order_no = $notify->getParameter("order_no");
	$pay_no = $notify->getParameter("pay_no");
	$amount = $notify->getParameter("amount");
	$pay_result = $notify->getParameter("pay_result");
	$pay_time = $notify->getParameter("pay_time");
	$sett_date = $notify->getParameter("sett_date");
	$sett_time = $notify->getParameter("sett_time");
	$base64_memo = $notify->getParameter("base64_memo");
	$sign_type = $notify->getParameter("sign_type");
	$sign = $notify->getParameter("sign");
	$memo = base64_decode($base64_memo);

	if( "1" == $pay_result ) {

		//处理业务开始
		echo "</br>获取异步通知信息成功!</br></br>";	
		echo " success "."</br></br>";
		echo "业务代码：".$busi_code."</br>";
		echo "商户号：".$merchant_no."</br>";
		echo "终端号：".$terminal_no."</br>";
		echo "商户系统订单号：".$order_no."</br>";
		echo "网关系统支付号：".$pay_no."</br>";
		echo "订单金额：".$amount."</br>";
		echo "支付结果（1表示成功）：".$pay_result."</br>";
		echo "支付时间：".$pay_time."</br>";
		echo "清算日期：".$sett_date."</br>";
		echo "清算时间：".$sett_time."</br>";
		echo "订单备注：".$memo."</br>";
		echo "签名类型：".$sign_type."</br>";
		echo "签名：".$sign."</br>";

		//注意订单不要重复处理
		//注意判断返回金额是否与本系统金额相符
		//处理业务完毕
	
	} else {
		//返回通知处理不成功
		echo "支付失败！</br></br>";
		echo "商户系统订单号：".$order_no."</br>";
		echo "网关系统支付号：".$pay_no."</br>";
		echo "支付结果（0表示未支付，2表示支付失败）：".$pay_result."</br>";
	}
	
} else {
	echo "<br/>" . "验证签名失败" ;
}

//获取调试信息
//echo $notify->getDebugMsg() ;

?>
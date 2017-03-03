<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝批量付款到支付宝账户有密接口接口</title>
</head>
<?php
/* *
 * 功能：批量付款到支付宝账户有密接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

/**************************请求参数**************************/

        //服务器异步通知页面路径
        $notify_url = "http://商户网关地址/batch_trans_notify-PHP-UTF-8/notify_url.php";
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        //付款账号
        $email = $_POST['WIDemail'];
        //必填
        //付款账户名
        $account_name = $_POST['WIDaccount_name'];
        //必填，个人支付宝账号是真实姓名公司支付宝账号是公司名称
        //付款当天日期
        $pay_date = $_POST['WIDpay_date'];
        //必填，格式：年[4位]月[2位]日[2位]，如：20100801
        //批次号
        $batch_no = $_POST['WIDbatch_no'];
        //必填，格式：当天日期[8位]+序列号[3至16位]，如：201008010000001
        //付款总金额
        $batch_fee = $_POST['WIDbatch_fee'];
        //必填，即参数detail_data的值中所有金额的总和
        //付款笔数
        $batch_num = $_POST['WIDbatch_num'];
        //必填，即参数detail_data的值中，“|”字符出现的数量加1，最大支持1000笔（即“|”字符出现的数量999个）
        //付款详细数据
        $detail_data = $_POST['WIDdetail_data'];
        //必填，格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2....


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "batch_trans_notify",
		"partner" => trim($alipay_config['partner']),
		"notify_url"	=> $notify_url,
		"email"	=> $email,
		"account_name"	=> $account_name,
		"pay_date"	=> $pay_date,
		"batch_no"	=> $batch_no,
		"batch_fee"	=> $batch_fee,
		"batch_num"	=> $batch_num,
		"detail_data"	=> $detail_data,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>
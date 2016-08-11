<?php
//下载地址 http://api.feyin.net/php_doc.zip

include 'HttpClient.class.php';

//使用本测试代码，您需要设置以下3项变量
//@ MEMBER_CODE：商户代码，登录飞印后在“API集成”->“获取API集成信息”获取
//@ FEYIN_KEY：密钥，获取方法同上
//@ DEVICE_NO：打印机设备编码，通过打印机后面的激活按键获取，为16位数字，例如"4600365507768327";
define('MEMBER_CODE', 'd2e595a4225311e6b50d52540008b6e6');
define('FEYIN_KEY', '425c8961');
define('DEVICE_NO', '9497610928583271');


//以下2项是平台相关的设置，您不需要更改
define('FEYIN_HOST','my.feyin.net');
define('FEYIN_PORT', 80);


//$msgNo = testSendFormatedMessage();
//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/addons/sz_yi/core/model/1.txt', print_r($print_order,true));


//testQueryState($msgNo);

//testListDevice();

//testListException();



function testSendFormatedMessage(){
	$msgNo = time()+1;
	/*
	 格式化的打印内容
	*/
	$msgInfo = array (
			'memberCode'=>$member_code,  
			'charge'=>'3000',  
			'customerName'=>'刘小姐',  
			'customerPhone'=>'13321332245',  
			'customerAddress'=>'五山华南理工',  
			'customerMemo'=>'请快点送货',  
			'msgDetail'=>'番茄炒粉@1000@1||客家咸香鸡@2000@1',  
			'deviceNo'=>$device_no,  
			'msgNo'=>$msgNo,
	);

	sendFormatedMessage($msgInfo);

	return $msgNo;
}
//房型打印
function testSendFreeMessage($print_order,$member_code,$device_no,$key,$set,$price_list){
	//file_put_contents($_SERVER['DOCUMENT_ROOT'].'/addons/sz_yi/core/model/1.txt', print_r($print_order,true));
	//<QRcode# Size=8>http://www.baidu.com</QRcode#>
	$goods = "";
	
	$depositprice = number_format($print_order['depositprice'],2);
	$type= array(
		'2'=>'到付',
		'1'=>'在线付'
		);
	if($print_order['depositpricetype']=='1'){
		$sum_money = number_format(($print_order['goodsprice'] + $print_order['depositprice']),2);
	}else{
		$sum_money = number_format(($print_order['goodsprice']),2);

	}

    $depositpricetype = $type[$print_order['depositpricetype']];
	foreach ($price_list  as $value) {
		$total = $value['oprice']*$print_order['num'];
		$goods .= $value['oprice']."   ".$print_order['num']."    ".$total."    ".$value['thisdate']."\n";
		//$num++;
	}
	
	$msgNo = $print_order['ordersn'];
	$address = unserialize($print_order['address']);
	$time = date('Y-m-d H:i:s',$print_order['createtime']);
    //房间小计
    if($print_order['depositpricetype']=='1'){ //1在线 2 到付
          $room_price = number_format($print_order['price']-$print_order['depositprice'],2);
       }else{
          $room_price = number_format($print_order['price'],2);
    }
    //余额
    if($print_order['deductcredit2']!=''){
    	$deductcredit2= number_format($print_order['deductcredit2'],2);
    }else{
    	$deductcredit2= '0.00';
    }
    //会员
    if($print_order['discountprice']!=''){
    	$discountprice= number_format($print_order['discountprice'],2);
    }else{
    	$discountprice= '0.00';
    }
    //积分
    if($print_order['deductprice']!=''){
    	$deductprice= number_format($print_order['deductprice'],2);
    }else{
    	$deductprice= '0.00';
    }
    // if($set['print_order']=='1'){
    //      $set['print_text']= '该订单未支付';
    // }else{
    // 	 $set['print_text']= '该订单已支付';
    // }
	$freeMessage = array(
		'memberCode'=>$member_code, 
		'msgDetail'=>
"
    {$set['name']}
------------------------------
订单编号：{$msgNo}
下单时间：{$time}
房型：{$print_order['goods'][0]['goodstitle']}
客户姓名：{$print_order['checkname']}
联系方式：{$print_order['realmobile']}
订单备注：{$print_order['remark']}
------------------------------
单价   数量   金额    入住日期        
{$goods}
------------------------------
房间小计：       {$print_order['goodsprice']}
押金：           {$depositprice}({$depositpricetype})
合计：           {$sum_money}
会员优惠：		 {$discountprice}
余额抵扣： 		 {$deductcredit2}
积分抵扣： 		 {$deductprice}
实际支付：       {$print_order['price']}
------------------------------
{$set['description']}
客服服务热线：{$set['phone']}
",
		'deviceNo'=>$device_no, 
		'msgNo'=>$msgNo
	);
	 sendFreeMessage($freeMessage,$key);

	return $msgNo;
}

//便利店打印
function testSendFreeMessageshop($print_order,$member_code,$device_no,$key,$set){
	$goods = "";
	//$num = 1;
	foreach ($print_order['goods'] as $value) {
		$goods .= $value['goodstitle']."\n"."            ".$value['price']."    ".$value['total']."     ".$value['totalmoney']."\n";
		//$num++;
	}
	// if($set['print_order']=='1'){
 //         $set['print_text']= '该订单未支付';
 //    }else{
 //    	 $set['print_text']= '该订单已支付';
 //    }
	$msgNo = $print_order['ordersn'];
	$time = date('Y-m-d H:i:s',$print_order['createtime']);
	//余额
    if($print_order['deductcredit2']!=''){
    	$deductcredit2= number_format($print_order['deductcredit2'],2);
    }else{
    	$deductcredit2= '0.00';
    }
    //会员
    if($print_order['discountprice']!=''){
    	$discountprice= number_format($print_order['discountprice'],2);
    }else{
    	$discountprice= '0.00';
    }
    //积分
    if($print_order['deductprice']!=''){
    	$deductprice= number_format($print_order['deductprice'],2);
    }else{
    	$deductprice= '0.00';
    }

    //运费
    if($print_order['olddispatchprice']!=''){
    	$olddispatchprice= number_format($print_order['olddispatchprice'],2);
    }else{
    	$olddispatchprice= '0.00';
    }
    //房间号
    if($print_order['room_number']!=''){
    	$room_number= "配送房间号:  ".  $print_order['room_number'];
    }else{
    	$room_number= '';
    }
    $sum_money = number_format(($print_order['goodsprice'] + $olddispatchprice),2);

	$freeMessage = array(
		'memberCode'=>$member_code, 
		'msgDetail'=>
"
     {$set['name']}
------------------------------
订单编号：{$msgNo}
下单时间：{$time}
{$room_number}
订单备注：{$print_order['remark']}
------------------------------
商品名称    单价   数量  金额
{$goods}
------------------------------
运费 ：          {$olddispatchprice}
合计：           {$sum_money}
会员优惠：		 {$discountprice}
余额抵扣： 		 {$deductcredit2}
积分抵扣： 		 {$deductprice}
实际支付：       {$print_order['price']}
------------------------------
{$set['description']}
客服服务热线：{$set['phone']}
",
		'deviceNo'=>$device_no, 
		'msgNo'=>$msgNo
	);

	 sendFreeMessage($freeMessage,$key);

	return $msgNo;
}

//会议打印
function testSendFreeMessagemeet($data,$member_code,$device_no,$key,$set){
	$time = date('Y-m-d H:i:s',time());
	$freeMessage = array(
		'memberCode'=>$member_code, 
		'msgDetail'=>
"
     {$set['name']}
------------------------------
预约时间：{$data['time']}
姓名：{$data['contact']}
手机：{$data['mobile']}
会议室：{$data['title']}
备注：{$data['message']}
------------------------------
下单时间：{$time}
------------------------------
{$set['description']}
客服服务热线：{$set['phone']}
",
		'deviceNo'=>$device_no, 
		'msgNo'=>$msgNo
	);
	 sendFreeMessage($freeMessage,$key);

	return $msgNo;
}
//餐厅打印
function testSendFreeMessagerest($data,$member_code,$device_no,$key,$set){
	$time = date('Y-m-d H:i:s',time());
	$freeMessage = array(
		'memberCode'=>$member_code, 
		'msgDetail'=>
"
     {$set['name']}
------------------------------
预约时间：{$data['time']}
姓名：{$data['contact']}
手机：{$data['mobile']}
餐厅：{$data['title']}
备注：{$data['message']}
------------------------------
下单时间：{$time}
------------------------------
{$set['description']}
客服服务热线：{$set['phone']}
",
		'deviceNo'=>$device_no, 
		'msgNo'=>$msgNo
	);
	 sendFreeMessage($freeMessage,$key);

	return $msgNo;
}
/*
 * 查询打印状态
 */
function testQueryState($msgNo){
	$result = queryState($msgNo);

	echo $result;

	return $result;
}

/*
 * 测试获取设备列表
 */
function testListDevice(){

	echo listDevice();
}


function testListException(){

	echo listException();
}


//----------------------以下是接口定义实现，第三方应用可根据具体情况直接修改----------------------------

function sendFreeMessage($msg,$key) {
	$msg['reqTime'] = number_format(1000*time(), 0, '', '');
	$content = $msg['memberCode'].$msg['msgDetail'].$msg['deviceNo'].$msg['msgNo'].$msg['reqTime'].$key;
	$msg['securityCode'] = md5($content);
	$msg['mode']=2;

	return sendMessage($msg);
}

function sendFormatedMessage($msgInfo) {
	$msgInfo['reqTime'] = number_format(1000*time(), 0, '', '');
	$content = $msgInfo['memberCode'].$msgInfo['customerName'].$msgInfo['customerPhone'].$msgInfo['customerAddress'].$msgInfo['customerMemo'].$msgInfo['msgDetail'].$msgInfo['deviceNo'].$msgInfo['msgNo'].$msgInfo['reqTime'].$key;

	$msgInfo['securityCode'] = md5($content);
	$msgInfo['mode']=1;
	
	return sendMessage($msgInfo);
}


function sendMessage($msgInfo) {
	$client = new HttpClient(FEYIN_HOST,FEYIN_PORT);
	if(!$client->post('/api/sendMsg',$msgInfo)){ //提交失败
		return 'faild';
	}else{
		return $client->getContent();
	}
}

function queryState($msgNo){
	$now = number_format(1000*time(), 0, '', '');
	$client = new HttpClient(FEYIN_HOST,FEYIN_PORT);
	if(!$client->get('/api/queryState?memberCode='.$member_code.'&reqTime='.$now.'&securityCode='.md5($member_code.$now.$key.$msgNo).'&msgNo='.$msgNo)){ //请求失败
		return 'faild';
	}
	else{
		return $client->getContent();
	}
}

function listDevice(){
	$now = number_format(1000*time(), 0, '', '');
	$client = new HttpClient(FEYIN_HOST,FEYIN_PORT);
	if(!$client->get('/api/listDevice?memberCode='.$member_code.'&reqTime='.$now.'&securityCode='.md5($member_code.$now.$key))){ //请求失败
		return 'faild';
	}
	else{
		/***************************************************
		解释返回的设备状态
		格式：
		<device id="4600006007272080">	
		<address>广东**</address>
		<since>2010-09-29</since>
		<simCode>135600*****</simCode>
		<lastConnected>2011-03-09  19:39:03</lastConnected>
		<deviceStatus>离线 </deviceStatus>
		<paperStatus></paperStatus>
		</device>
		**************************************************/

		$xml = $client->getContent();
		$sxe = new SimpleXMLElement($xml);
		foreach($sxe->device as $device) {
			$id = $device['id'];
			echo "设备编码：$id    ";
			
			$deviceStatus = $device->deviceStatus;
			echo "状态：$deviceStatus";
			echo '<br>';
		}
	}
}


function listException(){
	$now = number_format(1000*time(), 0, '', '');
	$client = new HttpClient(FEYIN_HOST,FEYIN_PORT);
	if(!$client->get('/api/listException?memberCode='.$member_code.'&reqTime='.$now.'&securityCode='.md5($member_code.$now.$key))){ //请求失败
		return 'faild';
	}
	else{
		return $client->getContent();
	}
}

?>

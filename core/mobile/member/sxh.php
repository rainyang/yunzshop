<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$openid         = m('user')->getOpenid();
$member         = m('member')->getInfo($openid);
$key            = 'sxhsz201608'; 
$memberbind = pdo_fetch("select * from " . tablename('sz_yi_sxh_member') . " where uniacid={$_W['uniacid']} and userid={$member['id']}");
$url = 'http://api-test-monitor.shanxinhui.com/User/UserApi/account_query';    
$post_data = array(               
"bindname" => $memberbind['username'],
"password" => md5($memberbind['username'].$key),
"timestamp" => date('YmdHis',time()),
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
$output = curl_exec($ch);
curl_close($ch);
$output =  json_decode($output,true);
$sjblog = pdo_fetchall("select * from " . tablename('sz_yi_sjb_log') . " where uniacid={$_W['uniacid']} and uid={$member['uid']}");
foreach ($sjblog as $keys=> $value) {
	$bindcredit += $value['accountnum'];
}
if ($_W['isajax']) {
    if ($_W['ispost']) {
        $memberdata = $_GPC['memberdata'];
		$url = 'http://api-test-monitor.shanxinhui.com/User/UserApi/user_deduction';    
		$post_data = array(               
		"bindname" => $memberbind['username'],
		"phone" => $memberbind['mobile'],
		"productid" => '1',
		"productname" => '虚拟币扣除',
		"productnum" => '1',
		"totalnum" =>  $memberdata['accountnum'],
		"orderid" => date('YmdHis',time()),
		"pay_time" => time(),
		"mcid" => $_W['uniacid'],
		"password" =>md5($memberbind['username'].$key),
		"timestamp" => date('YmdHis',time()),
		);
		//print_r($post_data);exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
		$output2 = curl_exec($ch);
		curl_close($ch);
		$output2 =  json_decode($output2,true);
		//print_r($output2);exit;
		if ($output2['status']==0) { 
			$afterbalance = ($output['data']['accountnum']-$memberdata['accountnum']);
		    $log = array(
	           'uniacid' => $_W['uniacid'],
	           'uid' =>$member['uid'],
	           'accountnum' =>$memberdata['accountnum'],
	           'beforebalance' =>$output['data']['accountnum'],//兑换之前
	           'afterbalance' =>$afterbalance,//兑换之后
	           'createtime' =>date('Y-m-d H:i:s',time()),
	           'remark' =>'善金币兑换',
	        );
	        pdo_insert('sz_yi_sjb_log',$log);
			$logid = pdo_insertid();	
		    $credit1 = $member['credit1']+$memberdata['accountnum'];
			pdo_update('mc_members', array('credit1' =>$credit1 ), array(
	           'uid' => $member['uid'],
	           'uniacid' => $_W['uniacid'],
	        ));
	  		if($logid){
				show_json(0, array(
		        'msg' =>'善金币兑换成功'
		   		));
			} 

		}else{
			show_json(1, array(
		        'msg' => $output2['msg']
		    ));
		}
        
     
    }
    show_json(1, array(
        'member' => $member
    ));
}

include $this->template('member/sxh');

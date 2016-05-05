<?php
global $_W, $_GPC;
$set = $this->getSet();
$isexecute = false;
 //  unset($set['current']);
 //  $this->updateSet($set);
 // echo "<pre>"; print_r($set);exit;
if(date('H') == $set['returntime'])
{
	if(!isset($set['current']) || $set['current'] !=date('d')){

		$data  = array_merge($set, array('current'=>date('d')));
		$this->updateSet($data);
		$isexecute = true;
	}
}

if($set["isreturn"] && $isexecute){

	p('return')->getmoney($set['orderprice'],$_W['uniacid']);

	//昨天成交金额
	$daytime = strtotime(date("Y-m-d 00:00:00"));
	    $stattime = $daytime - 86400;
	    $endtime = $daytime - 1;
	$sql = "select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$_W['uniacid']} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}  ORDER BY o.finishtime DESC,o.status DESC";
	$ordermoney = pdo_fetchcolumn($sql);
	$ordermoney = floatval($ordermoney);

	$r_ordermoney = $ordermoney * $set['percentage'] / 100;//可返利金额


	//返利队列
	$data_money = pdo_fetchall("select * from " . tablename('sz_yi_return') . " where uniacid = '". $_W['uniacid'] ."' and status = 0");
	$r_each = $r_ordermoney / count($data_money);//每个队列返现金额
	$r_each = sprintf("%.2f", $r_each);

	foreach ($data_money as $key => $value) {
		
		$member = pdo_fetch("select * from " . tablename('sz_yi_member') . " where uniacid = '". $_W['uniacid'] ."' and id = '".$value['mid']."'");
		
		if(($value['money']-$value['return_money']) < $r_each){
			pdo_update('sz_yi_return', array('return_money'=>$value['money'],'status'=>'1'), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
			m('member')->setCredit($member['openid'],'credit2',$value['money']-$value['return_money']);

			$messages = array(
				'keyword1' => array('value' => '返现通知', 
					'color' => '#73a68d'),
					'keyword2' => array('value' => '本次返现金额'.$value['money']-$value['return_money']."元！",
									'color' => '#73a68d'
					 ),
					'keyword3' => array('value' => '此返单已经全部返现完成！',
									'color' => '#73a68d'
					 )
				);
			m('message')->sendCustomNotice($member['openid'], $messages);

		}else
		{
			pdo_update('sz_yi_return', array('return_money'=>$value['return_money']+$r_each), array('id' => $value['id'], 'uniacid' => $_W['uniacid']));
			m('member')->setCredit($member['openid'],'credit2',$r_each);
			$messages = array(
				'keyword1' => array('value' => '返现通知', 
					'color' => '#73a68d'),
					'keyword2' => array('value' => '本次返现金额'.$r_each,
									'color' => '#73a68d'
					 ),
					'keyword3' => array('value' => '此返单剩余返现金额'.$value['money']-$value['return_money']+$r_each,
									'color' => '#73a68d'
					 )
				);
			m('message')->sendCustomNotice($member['openid'], $messages);
		}

	}
}
//定时任务 执行地址
//http://yu.gxzajy.com/app/index.php?i=19&c=entry&method=task&p=return&m=sz_yi&do=plugin

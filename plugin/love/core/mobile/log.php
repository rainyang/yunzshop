<?php
/*=============================================================================
#     FileName: log.php
#         Desc: 用户端爱心基金记录
#       Author: ym
#      Version: 0.0.1
#   LastChange: 2016-07-14
=============================================================================*/
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$uniacid = $_W['uniacid'];
$total = pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_love_log') . " WHERE uniacid=:uniacid and status=0", array(":uniacid" => $uniacid));
$total = number_format($total, 2);
$mytotal = pdo_fetchcolumn('SELECT sum(money) FROM ' . tablename('sz_yi_love_log') . " WHERE uniacid=:uniacid and openid=:openid and status=0", array(":openid" => $openid, ":uniacid" => $uniacid));
$mytotal = number_format($mytotal, 2);
$usetotal = pdo_fetchcolumn('SELECT sum(money) FROM ' . tablename('sz_yi_love_log') . " WHERE uniacid=:uniacid and status=1", array(":uniacid" => $uniacid));
$usetotal = number_format($usetotal, 2);
$member = p('commission')->getInfo($openid, array("ok"));


if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$status = trim($_GPC['status']);
		$datastatus =	trim($_GPC['datastatus']);
		$condition = " ";
		$params = array(':uniacid' => $uniacid);
		if($status == 9){
			$list = pdo_fetchall('SELECT a.id, a.article_title, l.createtime FROM ' . tablename('sz_yi_article') . ' a ' . ' left join ' . tablename('sz_yi_love_log') . " l on a.love_log_id = l.id where l.uniacid=:uniacid and a.love_money>0 {$condition} order by a.id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			foreach ($list as $key => &$row) {
				$row['title'] = $row['article_title'];
				$row['createtime'] = date("Y-m-d H:i",$row['createtime']);
			}
		}else{
			if($status == 3){
				$condition .= " and l.openid='{$openid}'";
			}else if($status == 2){
				$condition .= " and l.type=1";
			}else if($status == 5){
				$condition .= " and l.type=1 and l.openid='{$openid}'";
			}else if($status == 7){
				$condition .= " and l.type=2";
			}else if($status == 8){
				$condition .= " and l.type=2 and l.openid='{$openid}'";
			}
			$list = pdo_fetchall('SELECT m.nickname, g.title, l.money, l.paymonth, l.type, l.goodsid FROM ' . tablename('sz_yi_love_log') . ' l ' . ' left join ' . tablename('sz_yi_goods') . " g on l.goodsid = g.id left join " . tablename('sz_yi_member') . " m on l.openid=m.openid where l.uniacid=:uniacid and l.status=0 {$condition} order by l.id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			foreach ($list as $key => &$row) {
				$row['nickname'] = $row['nickname'] ? $row['nickname'] : "未获取";
				$row['goodsid'] = $row['goodsid'] ? $row['goodsid'] : 0;
				if($row['type'] == 2){
					$row['title'] = "会员爱心贡献";
					if($row['paymonth'] == 1){
						$row['title'] .= "积分";
					}else if($row['paymonth'] == 2){
						$row['title'] .= "宝币";
					}else if($row['paymonth'] == 3){
						$row['title'] .= "佣金";
					}
				}
			}
		}
		unset($row);
		
	}else if ($operation == 'submit') {
		$date =explode("#",$_GPC['pay']);
		$pay=intval($date[0]);
		$gender =intval($date[1]);
		$time=time();
		$data=array(
			'mid'=>$member['id'],
			'openid'=>$openid,
			'money'=>$pay,
			'paymonth'=>$gender,
			'createtime'=>$time,
			'uniacid' => $uniacid,
			'type' => 2
		);
		if($gender==1)
		{
		  if($member['credit1'] > 0 && $member['credit1'] >= ($pay*100)){
		  	$donation_credit1 = -$pay*100;
		  	pdo_insert('sz_yi_love_log',$data);
		  	m('member')->setCredit($openid, 'credit1', $donation_credit1, array(0, '会员爱心基金贡献：' . $donation_credit1 . " 积分"));
		  	return show_json(1, array('url' => $this->createPluginMobileUrl('love/log_detail')));
		  }else{
		  	return show_json(0, array('message' => '积分不足!您可以购买商品来进行捐赠！'));
		  }			
		}else if($gender==2){
	      if($member['credit2'] > 0 && $member['credit2'] >= $pay){
		  	$donation_credit2 = -$pay;
		  	pdo_insert('sz_yi_love_log',$data);
		  	m('member')->setCredit($openid, 'credit2', $donation_credit2, array(0, '会员爱心基金贡献：' . $donation_credit2 . " 元"));
		  	return show_json(1, array('url' => $this->createPluginMobileUrl('love/log_detail')));
		  }else{
		  	return show_json(0, array('message' => '余额不足!您可以购买商品来进行捐赠！'));
		  }
		}else if($gender==3){
	      if($member['commission_ok'] > 0 && $member['commission_ok'] >= $pay){
		  	$donation_credit20 = $pay;
		  	pdo_insert('sz_yi_love_log',$data);
		  	m('member')->setCredit($openid, 'credit20', $donation_credit20);
		  	return show_json(1, array('url' => $this->createPluginMobileUrl('love/log_detail')));
		  }else{
		  	return show_json(0, array('message' => '佣金不足!您可以购买商品来进行捐赠！'));
		  }
		}
		exit();
 	}	
 	$member['commission_ok'] = number_format($member['commission_ok'], 2);
	return show_json(1, array('list' => $list, 'member' => $member, 'status' => $status));
}
include $this->template('log');
	

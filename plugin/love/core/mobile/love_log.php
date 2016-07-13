<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
	die('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$mid = m('member')->getMid();
$uniacid = $_W['uniacid'];
$agentLevel = $this->model->getLevel($openid);
$level = intval($this->set['level']);
$setdata = pdo_fetch("select * from " . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
$uselove = pdo_fetchcolumn('SELECT sum(uselove) FROM ' . tablename('site_article') . " WHERE uniacid = '{$_W['uniacid']}'");
$uselove = number_format($uselove, 2);
$member = $this->model->getInfo($openid, array('total', 'ordercount0', 'ok'));
$member['commission_total'] = number_format($member['commission_total'], 2);
$credit1 = m('member')->getCredit($openid, 'credit1');
$credit2 = m('member')->getCredit($openid, 'credit2');

	 	$condition = " and a.uniacid=:uniacid ";
		$conditionb = " and uniacid=:uniacid ";
		$params = array(':uniacid' => $uniacid);
		$condition.=" and a.id=b.orderid and b.goodsid=c.id and a.openid=d.openid and a.status>=1 and c.love>0 ";
		$tables=tablename('ewei_shop_order').' a, '.tablename('ewei_shop_order_goods').' b, '.tablename('ewei_shop_goods').' c,'.tablename('ewei_shop_member').' d ';
        $mecond=" and a.openid='$openid' ";

		/****************查询所有总和********************/
		//购物贡献总和
		$sqls="select sum( c.love * b.total ) from {$tables} where 1 {$condition} ";
		$gototal=pdo_fetchcolumn($sqls,$params);
		//会员贡献总和
		$sqlsh="select sum(pay) from  ".tablename('ewei_shop_commission_love');
		$gomtotal=pdo_fetchcolumn($sqlsh,$params);

		//累计总额
		$jjtotal = $gototal + $gomtotal;
		
		/******************查询个人总和******************/
	   	//购物贡献总和
		$sqlsme="select sum( c.love * b.total ) from {$tables} where 1 {$condition} {$mecond} ";
		$gometotal=pdo_fetchcolumn($sqlsme,$params);
		
		//会员贡献总和
		$sqlshme="select sum(pay) from  ".tablename('ewei_shop_commission_love')."  where mid='$mid' ";
		$gommetotal=pdo_fetchcolumn($sqlshme,$params);

		//我的贡献总额
		$mytotal = $gometotal + $gommetotal;

		//基金事业文章
		$art="select sum(uselove) from ".tablename('site_article')." where 1 {$conditionb}";
		$arttotal=pdo_fetchcolumn($art,$params);

		/*************************************************/
/*
$lovetotal=number_format(($gototal+$gomtotal), 2);
$melovetotal=number_format(($gometotal+$gommetotal), 2); */

//$status = trim($_GET['status']);
if ($_W['isajax']) {
	if ($operation == 'display') {
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$status = trim($_GPC['status']);
		$datastatus =	trim($_GPC['datastatus']);
		$condition = " and a.uniacid=:uniacid ";
		$conditionb = " and uniacid=:uniacid ";
		$params = array(':uniacid' => $uniacid);
		$condition.=" and a.id=b.orderid and b.goodsid=c.id and a.openid=d.openid  and a.status>=1 and c.love>0  ";
		$mecond=" and a.openid='$openid' ";
		//购物贡献
		$select=" a.id,a.openid,b.goodsid,c.title,a.goodsprice,c.love,b.total,d.nickname ";
		$tables=tablename('ewei_shop_order').' a, '.tablename('ewei_shop_order_goods').' b, '.tablename('ewei_shop_goods').' c,'.tablename('ewei_shop_member').' d ' ;
		$list = pdo_fetchall("select {$select} from {$tables} where 1 {$condition} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	    
		//基金事业文章
		$art="select * from ".tablename('site_article')." where 1 {$conditionb} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$artlist = pdo_fetchall($art, $params);
		
		$melist = pdo_fetchall("select {$select} from {$tables} where 1 {$condition}{$mecond} order by id desc ", $params);
		$total = pdo_fetchcolumn("select count(*) from {$tables} where 1 {$condition}", $params);
		
/* 		
		 /****************查询所有总和********************
		//购物贡献总和
		$sqls="select sum( c.love * b.total ) from {$tables} where 1 {$condition} ";
		$gototal=pdo_fetchcolumn($sqls,$params);
		//会员贡献总和
		$sqlsh="select sum(pay) from  ".tablename('ewei_shop_commission_love');
		$gomtotal=pdo_fetchcolumn($sqlsh,$params);
		
		/******************查询个人总和******************
	   	//购物贡献总和
		$sqlsme="select sum( c.love * b.total ) from {$tables} where 1 {$condition} {$mecond} ";
		$gometotal=pdo_fetchcolumn($sqlsme,$params);
		//会员贡献总和
		$sqlshme="select sum(pay) from  ".tablename('ewei_shop_commission_love')."  where mid='$mid' ";
		$gommetotal=pdo_fetchcolumn($sqlshme,$params); 

		/*************************************************
		
 */		
		if($status==1){
			$totallist=pdo_fetchall('select id,openid,goodsid,title,pay as love,nickname,type from'.tablename('ewei_shop_order_love').'order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($status==3){
			$totallist=pdo_fetchall('select a.id,a.openid,a.goodsid,a.title,a.pay as love,a.nickname from'.tablename('ewei_shop_order_love').'a,'.tablename('ewei_shop_member').'b where a.openid=b.openid and a.openid = "'.$openid. '" order by a.createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($status==9){
			$totallist=pdo_fetchall("select * from ".tablename('site_article')." where 1 {$conditionb} order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);

		}elseif($status==2){
			$totallist=pdo_fetchall('select id,openid,goodsid,title,pay as love,nickname,type from'.tablename('ewei_shop_order_love').'where lovetype=1 order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

		}elseif($status==5){
			$totallist=pdo_fetchall('select a.id,a.openid,a.goodsid,a.title,a.pay as love,a.nickname,a.type from'.tablename('ewei_shop_order_love').'a,'.tablename('ewei_shop_member').'b where lovetype=1 and a.openid= "'.$openid. '" and a.openid=b.openid order by a.createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

		}elseif($status==7){
			$totallist=pdo_fetchall('select id,openid,goodsid,title,pay as love,nickname,type from'.tablename('ewei_shop_order_love').'where lovetype=2  order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

		}elseif($status==8){
			$totallist=pdo_fetchall('select a.id,a.openid,a.goodsid,a.title,a.pay as love,a.nickname,a.type from'.tablename('ewei_shop_order_love').'a,'.tablename('ewei_shop_member').'b where lovetype=2 and a.openid= "'.$openid. '" and a.openid= b.openid order by a.createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

		}
        //会员基金
		// $ss='a.id,b.nickname,a.type,a.createtime as title,a.pay as love';
		// $tb=tablename('ewei_shop_commission_love').' a, '.tablename('ewei_shop_member').' b';
		// if($status=='7' || $status=='1') $ct='and a.openid=b.openid and b.uniacid=:uniacid';
		// elseif($status=='8' || $status=='3') $ct="and a.openid=b.openid and a.mid='$mid' and b.uniacid=:uniacid";
		// else $ct="  and b.uniacid=:uniacid";
		// $thlist = pdo_fetchall("select {$ss} from {$tb} where 1 {$ct} order by id desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
		
		 /*foreach ($list as &$roww) {
			$roww['title'] =mb_substr($roww['title'],0,18,'utf-8')."...";
		 }
		 foreach ($melist as &$rowe) {
			$rowe['title'] =mb_substr($rowe['title'],0,18,'utf-8')."...";
		 }
		 foreach ($artlist as &$rowa) {
			$rowa['createtime'] = date('Y-m-d', $rowa['createtime']);
			$rowa['title'] =mb_substr($rowa['title'],0,15,'utf-8');
		 }*/
		 // foreach ($thlist as &$rows) {
			// $rows['title'] = date('Y-m-d', $rows['title']);
			// $metotalarrt[]=intval($rows['love']);
		 // }
	
		 // if($status=='3'|| $status=='5') $list=$melist;
		 // if($status=='9') $list=$artlist;
		 // if($status=='7'||$status=='8')$list=$thlist;
		 
		 
		 	if($status!=9){
		 		show_json(1, array('total' => $total,'status' => $status,'lovetotal' => $lovetotal,'credit1' => $credit1,'credit2' => $credit2,'melovetotal' => $melovetotal,'melovetotalt' => $melovetotalt,'commission_total'=>$member['commission_total'],'list' => $totallist,'pagesize' => $psize));
		 	}else{
		 		show_json(1, array('total' => $total,'status' => $status,'lovetotal' => $lovetotal,'credit1' => $credit1,'credit2' => $credit2,'melovetotal' => $melovetotal,'melovetotalt' => $melovetotalt,'commission_total'=>$member['commission_total'],'list' => $artlist,'thlist' => $thlist,'pagesize' => $psize));
		 	}
		 	
		 	// else
		 	// 	show_json(1, array('total' => $total,'status' => $status,'lovetotal' => $lovetotal,'credit1' => $credit1,'credit2' => $credit2,'melovetotal' => $melovetotal,'melovetotalt' => $melovetotalt,'commission_total'=>$member['commission_total'],'list' => $list,'pagesize' => $psize));
	     
		 	
		 
		  unset($totallist,$listp,$list,$artlist);
	
	} 
	
	else{
	if ($operation == 'submit') {
			$date =explode("#",$_GPC['pay']);
			$pay=intval($date[0]);
			$type =intval($date[1]);
			$time=time();
			//if($type==1)$pay=($pay*100);
			$date=array('mid'=>$mid,'openid'=>$openid,'pay'=>$pay,'type'=>$type,'createtime'=>$time);
			$credit1=intval($credit1);$credit2=intval($credit2);
			load()->model('mc');
		    $uid = mc_openid2uid($openid);
			if($type==1)
			{
		      if (!empty($uid)){
			      if($credit1>0 && $credit1>($pay*100))
			      {
			         $new_credit1=($credit1-($pay*100));
			         pdo_insert('ewei_shop_commission_love',$date);
			         pdo_update('mc_members', array('credit1'=> $new_credit1), array('uid' =>$uid, 'uniacid' => $uniacid));
			  	   show_json(0, array('message' => '捐赠成功！'));
			      }
			  	else show_json(0, array('message' => '积分不足!您可以购买商品来进行捐赠！'));
                  
		       }
			   else{
			     if($credit1>0 && $credit1>($pay*100))
			     {
			       $new_credit2=($credit1-($pay*100));
			       pdo_insert('ewei_shop_commission_love',$date);
			       pdo_update('ewei_shop_member', array('credit1'=> $new_credit1), array('id' =>$mid, 'uniacid' => $uniacid));
			  	 show_json(0, array('message' => '捐赠成功！'));
			     }
                else show_json(0, array('message' => '积分不足!您可以购买商品来进行捐赠！'));
		      }
				
			}
			else
			{
		      if (!empty($uid)){
			      if($credit2>0 && $credit2>$pay)
			      {
			         $new_credit2=($credit2-$pay);
			         pdo_insert('ewei_shop_commission_love',$date);
			         pdo_update('mc_members', array('credit2'=> $new_credit2), array('uid' =>$uid, 'uniacid' => $uniacid));
			  	   show_json(0, array('message' => '捐赠成功！'));
			      }
			  	else show_json(0, array('message' => '余额不足!您可以购买商品来进行捐赠！'));
                  
		       }
			   else{
			     if($credit2>0 && $credit2>$pay)
			     {
			    	 $new_credit2=($credit2-$pay);
			       pdo_insert('ewei_shop_commission_love',$date);
			       pdo_update('ewei_shop_member', array('credit2'=> $new_credit2), array('id' =>$mid, 'uniacid' => $uniacid));
			  	 show_json(0, array('message' => '捐赠成功！'));
			     }
                else show_json(0, array('message' => '余额不足!您可以购买商品来进行捐赠！'));
		      }
			}
     }	
   }	

}
if ($operation == 'display') {
include $this->template('love_log');
}
if ($operation == 'submit') {
include $this->template('love_de');
}
	

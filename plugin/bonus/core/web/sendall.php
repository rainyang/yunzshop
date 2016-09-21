<?php
global $_W, $_GPC;
ca('bonus.sendall.view');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$set = $this->getSet();
$time             = time();
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$day_times        = intval($set['settledays']) * 3600 * 24;
$daytime = strtotime(date("Y-m-d 00:00:00"));
if(empty($set['sendmonth'])){
    $stattime = $daytime - $day_times - 86400;
    $endtime = $daytime - $day_times;
    $logs_count = pdo_fetchcolumn("select count(*) from ".tablename('sz_yi_bonus')." where uniacid={$_W['uniacid']} and isglobal=1 and sendmonth=0 and utime=".$daytime);
    $logs_text = "今天";
}else if($set['sendmonth'] == 1){
    $now_stattime = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
    $stattime = $now_stattime - $day_times;
    $now_endtime = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $endtime = $now_endtime - $day_times;
    $log_stattime = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $log_endtime = mktime(0, 0, 0, date('m')+1, 1, date('Y'));
    $logs_count = pdo_fetchcolumn("select count(*) from ".tablename('sz_yi_bonus')." where uniacid={$_W['uniacid']} and isglobal=1 and sendmonth=0 and ctime >= " . $log_stattime . " and ctime < ".$log_endtime);
    $logs_text = "本月";
}

$orderallmoney = pdo_fetchcolumn("select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$_W['uniacid']} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}");
//获取分红订单id
$orderids = pdo_fetchall("select o.id from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid=:uniacid and  o.finishtime >={$stattime} and o.finishtime < {$endtime}", array(":uniacid" => $_W['uniacid']), 'id');
$ordermoney = floatval($orderallmoney);
$premierlevels = pdo_fetchall("select * from ".tablename('sz_yi_bonus_level')." where uniacid={$_W['uniacid']} and premier=1");
$levelmoneys = array();
$totalmoney = 0;
foreach ($premierlevels as $key => $value) {
    $leveldcount = pdo_fetchcolumn("select count(*) from ".tablename('sz_yi_member')." where uniacid={$_W['uniacid']} and bonuslevel=".$value['id']);
    if($leveldcount>0){
        //当前等级分总额的百分比
        $levelmembermoney = round($orderallmoney*$value['pcommission']/100,2);
        if($levelmembermoney > 0){
            //当前等级人数平分该等级比例金额
            $membermoney = round($levelmembermoney/$leveldcount,2);
            if($membermoney > 0){
                //等级id座位键名保存该等级的代理商每人所分金额
                $levelmoneys[$value['id']] = $membermoney;
                $totalmoney += $levelmembermoney;
            }
        }
    }
}
$total = pdo_fetchcolumn("select count(*) from ".tablename('sz_yi_member')." m left join " . tablename('sz_yi_bonus_level') . " l on m.bonuslevel=l.id where 1 and l.premier=1 and m.uniacid={$_W['uniacid']}");
$sql = "select m.*,l.levelname from ".tablename('sz_yi_member')." m left join " . tablename('sz_yi_bonus_level') . " l on m.bonuslevel=l.id where 1 and l.premier=1 and m.uniacid={$_W['uniacid']}";
$setshop = m('common')->getSysset('shop');
if ($operation != "sub_bonus") {
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
}

$list = pdo_fetchall($sql);
foreach ($list as $key => &$row) {  
    //Author:ym Date:2016-04-08 Content:需消费一定金额，否则清除该用户不参与分红
    if(!empty($set['consume_withdraw'])){
        $myorder = pdo_fetchcolumn('select sum(og.realprice) as ordermoney from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
        if($myorder < floatval($set['consume_withdraw'])){
            unset($list[$key]);
            continue;
        }
    }
    $commission_pay = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_log') . " where openid=:openid and uniacid=:uniacid", array(":openid" => $row['openid'], ":uniacid" => $_W['uniacid']));
    $row['commission_ok'] = number_format($levelmoneys[$row['bonuslevel']],2); 
    $row['commission_pay'] = number_format($commission_pay,2);
}
unset($row);
$send_bonus_sn = time();
$sendpay_error = 0;
$bonus_money = 0;
if (!empty($_POST)) {
    if($totalmoney<=0){
        message("发放金额为0或太小，不足发放标准", '', "success");
    }
	foreach ($list as $key => $value) {
		$send_money = $levelmoneys[$value['bonuslevel']];
		$sendpay = 1;
		if(empty($set['paymethod'])){
			m('member')->setCredit($value['openid'], 'credit2', $send_money);
		}else{
			$logno = m('common')->createNO('bonus_log', 'logno', 'RB');
			$result = m('finance')->pay($value['openid'], 1, $send_money * 100, $logno, "【" . $setshop['name']. "】".$value['levelname']."分红");
	        if (is_error($result)) {
	            $sendpay = 0;
	            $sendpay_error = 1;
	        }
		}
		pdo_insert('sz_yi_bonus_log', array(
            "openid" => $value['openid'],
            "uid" => $value['uid'],
            "money" => $send_money,
            "uniacid" => $_W['uniacid'],
            "paymethod" => $set['paymethod'],
            "sendpay" => $sendpay,
            "isglobal" => 1,
			"status" => 1,
            "ctime" => time(),
            "send_bonus_sn" => $send_bonus_sn
        ));
        if($sendpay == 1){
            //获取用户等级名称
            $levelname = pdo_fetchcolumn("select levelname from " . tablename('sz_yi_bonus_level') . " where id=:id and uniacid=:uniacid", array(":id" => $value['bonuslevel'], ":uniacid" => $_W['uniacid']));
        	$this->model->sendMessage($value['openid'], array('nickname' => $value['nickname'], 'levelname' => $levelname, 'commission' => $send_money, 'type' => empty($set['paymethod']) ? "余额" : "微信钱包"), TM_BONUS_GLOBAL_PAY);
        }
	}
	$log = array(
            "uniacid" => $_W['uniacid'],
            "money" => $totalmoney,
            "status" => 0,
            "type" => 4,
            "ctime" => time(),
            "sendmonth" => $set['sendmonth'],
            "paymethod" => $set['paymethod'],
            "sendpay_error" => $sendpay_error,
            "orderids" => iserializer($orderids),
            "isglobal" => 1,
            'utime' => $daytime,
            "send_bonus_sn" => $send_bonus_sn,
            "total" => $total
            );
    pdo_insert('sz_yi_bonus', $log);
    message("全球分红发放成功", $this->createPluginWebUrl('bonus/detail', array("sn" => $send_bonus_sn)), "success");
}
$pager = pagination($total, $pindex, $psize);
include $this->template('sendall');

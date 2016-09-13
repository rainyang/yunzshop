<?php
global $_W, $_GPC;
ca('bonus.sendarea.view');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$set = $this->getSet();
$time             = time();
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$day_times        = intval($set['settledays']) * 3600 * 24;
$daytime = strtotime(date("Y-m-d 00:00:00"));
$sql = "select distinct cg.mid from " . tablename('sz_yi_bonus_goods') . " cg left join  ".tablename('sz_yi_order')."  o on o.id=cg.orderid and cg.status=0 left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 left join ".tablename('sz_yi_member')." m on cg.mid=m.id where 1 and m.id!=0 and o.status>=3 and o.uniacid={$_W['uniacid']} and ({$time} - o.finishtime > {$day_times}) and cg.bonus_area!=0  ORDER BY o.finishtime DESC,o.status DESC";
$count = pdo_fetchall($sql);
$setshop = m('common')->getSysset('shop');
if ($operation != "sub_bonus") {
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
}
$p = p('commission')->getSet();
$list = pdo_fetchall($sql);
$totalmoney = 0;
$real_total = 0;
foreach ($list as $key => &$row) {
	$member = $this->model->getInfo($row['mid'], array('ok', 'pay', 'myorder'));
	if(!empty($member)){
		//Author:ym Date:2016-04-08 Content:需消费一定金额，否则清除该用户不参与分红
		if(floatval($member['myordermoney']) < floatval($set['consume_withdraw']) || empty($member)){
			unset($list[$key]);
		}else{
			if($member['commission_ok'] <= 0){
				unset($list[$key]);
			}else{
				if(!empty($member['bonuslevel'])){
					$row['realname'] = pdo_fetchcolumn("select levelname from " . tablename('sz_yi_bonus_level') . " where id=".$member['bonuslevel']);
				}else{
					$row['realname'] = $set['levelname'];
				}
				$row['commission_ok'] = $member['commission_ok'];

				$row['commission_pay'] = $member['commission_pay'];
				$row['id'] = $member['id'];
				$row['avatar'] = $member['avatar'];
				$row['nickname'] = $member['nickname'];
				$row['realname'] = $member['realname'];
				$row['mobile'] = $member['mobile'];
				$totalmoney += $member['commission_ok'];
				$real_total +=1;
			}
		}
	}else{
		//Author:ym Date:2016-08-02 Content:如未查询到该用户则被删除
		unset($list[$key]);
	}	
}
unset($row);
$total = count($count);
$send_bonus_sn = time();
$sendpay_error = 0;
$bonus_money = 0;
if (!empty($_POST)) {
	$islog = false;
	if($real_total<=0){
		message("发放人数为0，不能发放。", "", "error");
	}
	foreach ($count as $key => $value) {
		$member = $this->model->getInfo($value['mid'], array('ok', 'pay', 'ordergoods'));
		if(empty($member)){
			continue;
		}
		$send_money = $member['commission_ok'];
		$sendpay = 1;
		$islog = true;
		$level = $this->model->getlevel($member['openid']);
		if(empty($set['paymethod'])){
			m('member')->setCredit($member['openid'], 'credit2', $send_money, array(0, '地区分红发放：' . $send_money . " 元"));
		}else{
			$logno = m('common')->createNO('bonus_log', 'logno', 'RB');
			$result = m('finance')->pay($member['openid'], 1, $send_money * 100, $logno, "【" . $setshop['name']. "】".$level['levelname']."分红");
	        if (is_error($result)) {
	            $sendpay = 0;
	            $sendpay_error = 1;
	        }
		}
		pdo_insert('sz_yi_bonus_log', array(
            "openid" => $member['openid'],
            "uid" => $member['uid'],
            "money" => $send_money,
            "uniacid" => $_W['uniacid'],
            "paymethod" => $set['paymethod'],
            "sendpay" => $sendpay,
			"status" => 1,
            "ctime" => time(),
            "send_bonus_sn" => $send_bonus_sn
        ));
        if($sendpay == 1){
        	if(empty($level)){
				if($member['bonus_area'] == 1){
					$level['levelname'] = "省级代理";
				}else if($member['bonus_area'] == 2){
					$level['levelname'] = "市级代理";
				}else if($member['bonus_area'] == 3){
					$level['levelname'] = "区级代理";
				}
			}
        	$this->model->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'levelname' => $level['levelname'], 'commission' => $send_money, 'type' => empty($set['paymethod']) ? "余额" : "微信钱包"), TM_BONUS_PAY_AREA);
        }
        //更新分红订单完成
		$ids = pdo_fetchall("select cg.id from " . tablename('sz_yi_bonus_goods') . " cg left join  ".tablename('sz_yi_order')."  o on o.id=cg.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and cg.mid=:mid and cg.status=0 and o.status>=3 and o.uniacid=:uniacid and ({$time} - o.finishtime > {$day_times}) and cg.bonus_area!=0", array(":mid" => $member['id'], ":uniacid" => $_W['uniacid']), 'id');

		//更新分红订单完成
		pdo_query('update ' . tablename('sz_yi_bonus_goods') . ' set status=3, applytime='.$time.', checktime='.$time.', paytime='.$time.', invalidtime='.$time.' where id in( ' . implode(',', array_keys($ids)) . ') and uniacid='.$_W['uniacid']);
	}
	if($islog){
		$log = array(
	            "uniacid" => $_W['uniacid'],
	            "money" => $totalmoney,
	            "status" => 1,
	            "type" => 3,
	            "ctime" => time(),
	            "paymethod" => $set['paymethod'],
	            "sendpay_error" => $sendpay_error,
	            'utime' => $daytime,
	            "send_bonus_sn" => $send_bonus_sn,
	            "total" => $real_total
	            );
	    pdo_insert('sz_yi_bonus', $log);
    }
    message("地区分红发放成功", $this->createPluginWebUrl('bonus/detail', array("sn" => $send_bonus_sn)), "success");
}
$pager = pagination($total, $pindex, $psize);
include $this->template('sendarea');

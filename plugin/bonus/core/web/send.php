<?php
global $_W, $_GPC;
set_time_limit(0);
ca('bonus.send.view');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$set = $this->getSet();
$time             = time();
$pindex    = max(1, intval($_GPC['page']));
$psize     = 20;
$day_times        = intval($set['settledays']) * 3600 * 24;
$daytime = strtotime(date("Y-m-d 00:00:00"));
$sql = "select distinct cg.mid from " . tablename('sz_yi_bonus_goods') . " cg left join  ".tablename('sz_yi_order')."  o on o.id=cg.orderid and cg.status=0 left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 left join ".tablename('sz_yi_member')." m on cg.mid=m.id where 1 and m.id!=0 and o.status>=3 and o.uniacid={$_W['uniacid']} and ({$time} - o.finishtime > {$day_times}) and cg.bonus_area=0 ORDER BY o.finishtime DESC,o.status DESC";
$setshop = m('common')->getSysset('shop');
if ($operation != "sub_bonus") {
    $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
}
$list = pdo_fetchall($sql);
$levelnames = pdo_fetchall("select id, levelname from ".tablename('sz_yi_bonus_level')." where uniacid=:uniacid", array(":uniacid" => $_W['uniacid']), 'id');
$totalmoney = 0;
if ($operation != "sub_bonus") {
	$total = pdo_fetchcolumn("select count(distinct cg.mid) as total from " . tablename('sz_yi_bonus_goods') . " cg left join  ".tablename('sz_yi_order')."  o on o.id=cg.orderid and cg.status=0 left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 left join ".tablename('sz_yi_member')." m on cg.mid=m.id where 1 and m.id!=0 and o.status>=3 and o.uniacid={$_W['uniacid']} and ({$time} - o.finishtime > {$day_times}) and cg.bonus_area=0 ORDER BY o.finishtime DESC,o.status DESC");
	
	foreach ($list as $key => &$row) {
		$member = pdo_fetch("select id, avatar, nickname, realname, mobile, openid, bonuslevel from ". tablename('sz_yi_member') . " where id=".$row['mid']." and uniacid=". $_W['uniacid']);
		if(!empty($member)){
			if(floatval($set['consume_withdraw']) > 0){
				$myorder = $this->model->myorder($member['openid']);
				if($myorder['ordermoney'] < floatval($set['consume_withdraw'])){
					unset($list[$key]);
					continue;

				}
			}

	        $commission_teamok = pdo_fetchcolumn("select sum(money) as money from " . tablename('sz_yi_order') . " o left join  ".tablename('sz_yi_bonus_goods')."  cg on o.id=cg.orderid and cg.status=0 left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3  and o.status<>4  and o.status<>5 and o.status<>6 and o.uniacid=:uniacid and cg.mid = :mid and ({$time} - o.finishtime > {$day_times})  and cg.bonus_area=0 ORDER BY o.createtime DESC,o.status DESC", array(':uniacid' => $_W['uniacid'], ":mid" => $member['id']));

			//Author:ym Date:2016-04-08 Content:需消费一定金额，否则清除该用户不参与分红
			if($commission_teamok <= 0){
				unset($list[$key]);
				continue;
			}else{
				if(!empty($member['bonuslevel'])){
					$row['levelname'] = $levelnames[$member['bonuslevel']]['levelname'];
				}else{
					$row['levelname'] = $set['levelname'];
				}
				$row['commission_ok'] = $commission_teamok;
	            $commission_pay = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_log') . " where sendpay=1 and uniacid=:uniacid and openid =:openid ", array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
				$row['commission_pay'] = $commission_pay;
				$row['id'] = $member['id'];
				$row['avatar'] = $member['avatar'];
				$row['nickname'] = $member['nickname'];
				$row['realname'] = $member['realname'];
				$row['mobile'] = $member['mobile'];
				$totalmoney += $commission_teamok;
			}
		}else{
			//Author:ym Date:2016-08-02 Content:如未查询到该用户则被删除
			unset($list[$key]);
		}	
	}
	unset($row);
}
if (!empty($_POST)) {
	$send_bonus_sn = time();
	$sendpay_error = 0;
	$bonus_money = 0;
	$real_total = 0;
	$islog = false;
	//定义会员分红明细log
    $insert_log_data = array();
    $insert_log_key = "INSERT INTO " . tablename('sz_yi_bonus_log') . " (openid, uid, money, uniacid, paymethod, sendpay, goodids, status, ctime, send_bonus_sn, type) VALUES ";
	//余额分红log
    $update_log_data = "";
    $update_log_key = "UPDATE " . tablename('mc_members') . " SET credit2 = CASE uid";
	//定义分红会员框架日志
    $insert_member_log_data = array();
    $insert_member_log_key = "INSERT INTO " . tablename('mc_credits_record') . " (uid, credittype, uniacid, num, createtime, operator, remark) VALUES ";

    //获取公众号函数
    load()->model('account');
    if (!empty($_W['acid'])) {
        $account = WeAccount::create($_W['acid']);
    } else {
        $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(
            ':uniacid' => $_W['uniacid']
        ));
        $account = WeAccount::create($acid);
    }

	$uids = array();
	if(empty($list)){
		message("发放人数为0，不能发放。", "", "error");
	}
	foreach ($list as $key => $value) {
		$member = pdo_fetch("select id, avatar, nickname, realname, mobile, openid, bonuslevel, uid from ". tablename('sz_yi_member') . " where id=".$value['mid']." and uniacid=". $_W['uniacid']);
		if(!empty($member)){
			if(floatval($set['consume_withdraw']) > 0){
				$myorder = $this->model->myorder($member['openid']);
				if($myorder['ordermoney'] < floatval($set['consume_withdraw'])){
					unset($list[$key]);
					continue;

				}
			}
			$send_money = pdo_fetchcolumn("select sum(money) as money from " . tablename('sz_yi_order') . " o left join  ".tablename('sz_yi_bonus_goods')."  cg on o.id=cg.orderid and cg.status=0 left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3  and o.status<>4  and o.status<>5 and o.status<>6 and o.uniacid=:uniacid and cg.mid = :mid and ({$time} - o.finishtime > {$day_times})  and cg.bonus_area=0 ORDER BY o.createtime DESC,o.status DESC", array(':uniacid' => $_W['uniacid'], ":mid" => $member['id']));

			//Author:ym Date:2016-04-08 Content:需消费一定金额，否则清除该用户不参与分红
			if($send_money > 0){
				$sql_num++;
				$real_total ++;
				$totalmoney += $send_money;
				if(!empty($member['bonuslevel'])){
					$value['levelname'] = $levelnames[$member['bonuslevel']]['levelname'];
				}else{
					$value['levelname'] = $set['levelname'];
				}
				$sendpay = 1;
				$islog = true;
				if(empty($set['paymethod'])){
					if($member['uid'] > 0){
		                $uid = $member['uid'];
		            }else{
		                $uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid=".$_W['uniacid']." AND openid='".$member['openid']."'");
		            }
		            if(!empty($uid)){
		                $update_log_data .= " WHEN ".$uid." THEN credit2+" . $send_money;
		                $insert_member_log_data[] = " ('".$uid."', 'credit2', '".$_W['uniacid']."', '".$send_money."', '".TIMESTAMP."', 0, '团队分红')";
		                $uids[] = $uid;
		            }else{  
		                pdo_query('update ' . tablename('sz_yi_member') . ' set credit2=credit2+'.$send_money.' where uniacid=' . $_W['uniacid'] . " and openid='".$member['openid']."'");
		            }
		            if ($sql_num % 500 == 0) {
		                if(!empty($update_log_data)){
		                    pdo_query($update_log_key . $update_log_data . " END WHERE uid IN (" . implode(",", $uids) . ")");
		                    $update_log_data = "";
		                    $uids = array();
		                }
		                if(!empty($insert_member_log_data)){
		                    pdo_query($insert_member_log_key . implode(",", $insert_member_log_data));
		                    $insert_member_log_data = array();
		                }   
		            }

				}else{
					/*$logno = m('common')->createNO('bonus_log', 'logno', 'RB');
					$result = m('finance')->pay($member['openid'], 1, $send_money * 100, $logno, "【" . $setshop['name']. "】".$value['levelname']."团队分红");
			        if (is_error($result)) {
			            
			        }*/
			        $sendpay = 0;
			        $sendpay_error = 1;
				}
			}
			//更新分红订单完成
			$ids = pdo_fetchall("select cg.id from " . tablename('sz_yi_bonus_goods') . " cg left join  ".tablename('sz_yi_order')."  o on o.id=cg.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and cg.mid=:mid and cg.status=0 and o.status>=3 and o.uniacid=:uniacid and ({$time} - o.finishtime > {$day_times}) and cg.bonus_area=0", array(":mid" => $member['id'], ":uniacid" => $_W['uniacid']), 'id');

			//写入日志调整
	        $insert_log_data[] = " ('".$member['openid']."', '".$member['uid']."', '".$send_money."', '".$_W['uniacid']."', '".$set['paymethod']."', '".$sendpay."', '".iserializer($ids)."', 1, ".TIMESTAMP.", ".$send_bonus_sn.", 2)";
	        if ($sql_num % 500 == 0) {
	            if(!empty($insert_log_data)){
	                pdo_query($insert_log_key . implode(",", $insert_log_data));
	                $insert_log_data = array();
	            }
	        }

	        //更新分红订单完成
			pdo_query('update ' . tablename('sz_yi_bonus_goods') . ' set status=3, applytime='.$time.', checktime='.$time.', paytime='.$time.', invalidtime='.$time.' where id in( ' . implode(',', array_keys($ids)) . ') and uniacid='.$_W['uniacid']);
		}	
	}

	if(!empty($insert_log_data)){
        pdo_query($insert_log_key . implode(",", $insert_log_data));
    }
    if(!empty($update_log_data)){
        pdo_query($update_log_key . $update_log_data . " END WHERE uid IN (" . implode(",", $uids) . ")");
    }
    if(!empty($insert_member_log_data)){
        pdo_query($insert_member_log_key . implode(",", $insert_member_log_data));
    }

	if($islog){
		$log = array(
	            "uniacid" => $_W['uniacid'],
	            "money" => $totalmoney,
	            "status" => 0,
	            "type" => 2,
	            "ctime" => TIMESTAMP,
	            "paymethod" => $set['paymethod'],
	            "sendpay_error" => $sendpay_error,
	            'utime' => $daytime,
	            "send_bonus_sn" => $send_bonus_sn,
	            "total" => $real_total
	            );
	    pdo_insert('sz_yi_bonus', $log);
    }
    plog('bonus.send', "后台发放团队分红，共计{$real_total}人 金额{$totalmoney}元");
    $ms = $set['paymethod'] == 1 ? "发放分红金额及" : "";
    message("团队分红发放成功,需在下一页面点击" . $ms . "发送消息", $this->createPluginWebUrl('bonus/detail', array("sn" => $send_bonus_sn)), "success");
}
$pager = pagination($total, $pindex, $psize);
include $this->template('send');

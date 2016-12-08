<?php
global $_W, $_GPC;
set_time_limit(0);
ca('bonus.sendall.view');
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
$set = $this->getSet();
$setshop = m('common')->getSysset('shop');
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
//获取总订单金额
$orderallmoney = pdo_fetchcolumn("select sum(o.price) from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid={$_W['uniacid']} and  o.finishtime >={$stattime} and o.finishtime < {$endtime}");
$ordermoney = floatval($orderallmoney);
//获取全球分红代理等级信息
$premierlevels = pdo_fetchall("select id, pcommission, levelname from ".tablename('sz_yi_bonus_level')." where uniacid={$_W['uniacid']} and premier=1");
//分组查询会员每个等级人数
$leveldcounts = pdo_fetchall("select count(*) as levelnum, bonuslevel from ".tablename('sz_yi_member')." where uniacid=:uniacid and bonuslevel!=0 GROUP BY bonuslevel",array(":uniacid" => $_W['uniacid']),"bonuslevel");
$levelmoneys = array();
$totalmoney = 0;
$levelnames = array();
//分红佣金计算
foreach ($premierlevels as $key => $value) {
    $levelnames[$value['id']] = $value['levelname'];
    $leveldcount = $leveldcounts[$value['id']]['levelnum'];
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
unset($value);
//是否有对应等级分红
if(!empty($levelnames)){
    $where_uid = implode(',', array_keys($levelnames));
    //获取分红总人数
    $total = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_member')." where uniacid={$_W['uniacid']} and bonuslevel in({$where_uid})");
    $sql = "select id, openid, bonuslevel, uid, nickname from ".tablename('sz_yi_member')." where uniacid={$_W['uniacid']} and bonuslevel in({$where_uid})";
    if ($operation != "sub_bonus") {
        $sql .= " limit " . ($pindex - 1) * $psize . ',' . $psize;
    }
    //获取分红列表
    $list = pdo_fetchall($sql);
    //$member_money = pdo_fetchall("select money, uid from " . tablename('member_money'), array(":uid" => $row['uid']), 'uid');
}else{
    $list = array();
}
//如为发放分红则不用计算
if ($operation != "sub_bonus") {
    foreach ($list as $key => &$row) {  
        //Author:ym Date:2016-04-08 Content:需消费一定金额，否则清除该用户不参与分红
        if(!empty($set['consume_withdraw'])){
            $myorder = $this->model->myorder($row['opneid']);
            if($myorder['ordermoney'] < floatval($set['consume_withdraw'])){
                $totalmoney -= $levelmoneys[$row['bonuslevel']];
                unset($list[$key]);
                continue;
            }
        }
        $commission_pay = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_log') . " where openid=:openid and uniacid=:uniacid", array(":openid" => $row['openid'], ":uniacid" => $_W['uniacid']));
        $row['commission_ok'] = number_format($levelmoneys[$row['bonuslevel']],2); 
        $row['commission_pay'] = number_format($commission_pay,2);
        $row['levelname'] = $levelnames[$row['bonuslevel']];
    }
    unset($row);
}
if (!empty($_POST)) {
    if($totalmoney<=0){
        message("发放金额为0或太小，不足发放标准", '', "success");
    }
    $send_bonus_sn = time();
    $sendpay_error = 0;
    $bonus_money = 0;
    $member_logs = array();
    $uids = array();
    $sql_num = 0;
    //定义会员分红明细log
    $insert_log_data = array();
    $insert_log_key = "INSERT INTO " . tablename('sz_yi_bonus_log') . " (openid, uid, money, uniacid, paymethod, sendpay, isglobal, status, ctime, send_bonus_sn) VALUES ";
    //定义分红会员框架日志
    $insert_member_log_data = array();
    $insert_member_log_key = "INSERT INTO " . tablename('mc_credits_record') . " (uid, credittype, uniacid, num, createtime, operator, remark) VALUES ";
    //余额分红log
    $update_log_data = "";
    $update_log_key = "UPDATE " . tablename('mc_members') . " SET credit2 = CASE uid";
    $paymethod = empty($set['paymethod']) ? 0 : 1;

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

    foreach ($list as $key => $value) {
        $send_money = $levelmoneys[$value['bonuslevel']];
        $levelname = $levelnames[$value['bonuslevel']];
        $sql_num++;
        //Author:ym Date:2016-10-15 Content:需消费一定金额，否则跳过该用户分红并减去对应分红金额
        if (!empty($set['consume_withdraw'])) {
            $myorder = $this->model->myorder($value['opneid']);
            if($myorder['ordermoney'] < floatval($set['consume_withdraw'])){
                $totalmoney -= $send_money;
                continue;
            }
        }
        
        if ($send_money <= 0) {
            continue;
        }
        
        $sendpay = 1;
        //分红打款
        if ($paymethod == 0) {
            //打款到余额
            if($value['uid'] > 0){
                $uid = $value['uid'];
            }else{
                $uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('mc_mapping_fans') . " WHERE uniacid=".$_W['uniacid']." AND openid='".$value['openid']."'");
            }
            if(!empty($uid)){
                $update_log_data .= " WHEN ".$uid." THEN credit2+" . $send_money;
                $insert_member_log_data[] = " ('".$uid."', 'credit2', '".$_W['uniacid']."', '".$send_money."', '".TIMESTAMP."', 0, '全球分红')";
                $uids[] = $uid;
            }else{  
                pdo_query('update ' . tablename('sz_yi_member') . ' set credit2=credit2+'.$send_money.' where uniacid=' . $_W['uniacid'] . " and openid='".$value['openid']."'");
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
            
        } else {
            //打款到微信钱包
            /*$logno = m('common')->createNO('bonus_log', 'logno', 'RB');
            $result = m('finance')->pay($value['openid'], 1, $send_money * 100, $logno, "【" . $setshop['name']. "】".$levelname."全球分红");
            if (is_error($result)) {
                
            }*/
            $sendpay = 0;
            $sendpay_error = 1;
        }

        //写入日志调整
        $insert_log_data[] = " ('".$value['openid']."', ".$value['uid'].", '".$send_money."', ".$_W['uniacid'].", ".$paymethod.", ".$sendpay.", 1, 1, ".TIMESTAMP.", ".$send_bonus_sn.")";
        if ($sql_num % 500 == 0) {
            if(!empty($insert_log_data)){
                pdo_query($insert_log_key . implode(",", $insert_log_data));
                $insert_log_data = array();
            }
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
    //获取分红订单id
    $orderids = pdo_fetchall("select o.id from ".tablename('sz_yi_order')." o left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id and ifnull(r.status,-1)<>-1 where 1 and o.status>=3 and o.uniacid=:uniacid and  o.finishtime >={$stattime} and o.finishtime < {$endtime}", array(":uniacid" => $_W['uniacid']), 'id');
    //分红明细记录
    $log = array(
            "uniacid" => $_W['uniacid'],
            "money" => $totalmoney,
            "status" => 0,
            "type" => 4,
            "ctime" => TIMESTAMP,
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
    plog('bonus.sendall', "后台发放全球分红，共计{$total}人 金额{$totalmoney}元，订单总额{$orderallmoney}元");
    $ms = $set['paymethod'] == 1 ? "发放分红金额及" : "";
    message("全球分红发放成功,需在下一页面点击" . $ms . "发送消息", $this->createPluginWebUrl('bonus/detail', array("sn" => $send_bonus_sn, "isglobal" => 1)), "success");
}
$pager = pagination($total, $pindex, $psize);
include $this->template('sendall');

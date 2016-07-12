<?php
global $_W, $_GPC;
// $set = $this->getSet($set);
// echo "<pre>";print_r($set);exit;
//ignore_user_abort();
set_time_limit(0);
//echo $_W['uniacid'];
$sets = pdo_fetchall('select uniacid from ' . tablename('sz_yi_sysset'));
foreach ($sets as $val) {
	$_W['uniacid'] = $val['uniacid'];
	if (empty($_W['uniacid'])) {
		continue;
	}

	$set = m('plugin')->getpluginSet('return', $_W['uniacid']);
	if(!empty($set))
	{

		$isexecute = false;
		if($set['returnlaw']==1)
		{
			if(date('H') == $set['returntime'])
			{
				if(!isset($set['current_d']) || $set['current_d'] !=date('d')){
					//$data  = array_merge($set, array('current_d'=>date('d')));
					$set['current_d'] = date('d');
					$this->updateSet($set);
					$isexecute = true;
				}
			}
		}elseif($set['returnlaw']==2){
			if(!isset($set['current_m']) || $set['current_m'] !=date('m')){
				//$data  = array_merge($set, array('current_m'=>date('m')));
				$set['current_m'] = date('m');
				$this->updateSet($set);
				$isexecute = true;
			}
		}
		if(($set["isreturn"]||$set["isqueue"]) && $isexecute){

			//p('return')->getmoney($set['orderprice'],$_W['uniacid']);
			if($set["returnrule"]==1)
			{
				//单笔订单
				p('return')->setOrderReturn($set,$_W['uniacid']);
			}else{
				//订单累计金额
				p('return')->setOrderMoneyReturn($set,$_W['uniacid']);

			}
			echo "<pre>"; print_r('成功');

		}else{
			echo "<pre>"; print_r('失败');
		}

		

	}
	
}
echo "ok...";



 // 	unset($set['current_d']);
 // unset($set['current_m']);
 // 	$this->updateSet($set);
 

//定时任务 执行地址
//http://yu.gxzajy.com/app/index.php?i=19&c=entry&method=task&p=return&m=sz_yi&do=plugin

<?php
global $_W, $_GPC;
$set = $this->getSet();
$isexecute = false;

  // 	unset($set['current_d']);
 	// unset($set['current_m']);
  // 	$this->updateSet($set);
 
  if($set['returnlaw']==1)
  {
	if(date('H') == $set['returntime'])
	{
		if(!isset($set['current_d']) || $set['current_d'] !=date('d')){
			$data  = array_merge($set, array('current_d'=>date('d')));
			$this->updateSet($data);
			$isexecute = true;
		}
	}
  }else{
	if(!isset($set['current_m']) || $set['current_m'] !=date('m')){
		$data  = array_merge($set, array('current_m'=>date('m')));
		$this->updateSet($data);
		$isexecute = true;
	}
  }



if($set["isreturn"] && $isexecute){

	//p('return')->getmoney($set['orderprice'],$_W['uniacid']);
	if($set["returnrule"]==1)
	{
		//单笔订单
		p('return')->setOrderReturn();
	}else{
		//订单累计金额
		p('return')->setOrderMoneyReturn();
	}
	echo "<pre>"; print_r('成功');exit;

}
echo "<pre>"; print_r('失败');exit;
//定时任务 执行地址
//http://yu.gxzajy.com/app/index.php?i=19&c=entry&method=task&p=return&m=sz_yi&do=plugin

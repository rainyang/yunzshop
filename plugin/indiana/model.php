<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('IndianaModel')) {

	class IndianaModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		
		public function setPeriod($id)
		{
			//设置本期
			global $_W, $_GPC;
			$indiana_good = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_goods') . " WHERE uniacid = '" .$_W['uniacid'] . "' AND id = '".$id."'");

			if($indiana_good['max_periods'] <= $indiana_good['periods']){
				//判断是否期数已满
				pdo_update('sz_yi_indiana_goods',array('status'=>0),array('id'=>$id));
			}

			//判断是否已经有正在进行的期数
			$sql_check = "select id,status from".tablename('sz_yi_indiana_period')." where uniacid=:uniacid and goodsid=:goodsid and ig_id=:igid and status=:status";
			$data_check = array(
				':uniacid'=>$_W['uniacid'],
				':goodsid'=>$indiana_good['good_id'],
				':igid'=>$indiana_good['id'],
				':status'=>1
			);
			$result_check = pdo_fetchall($sql_check,$data_check);
			if(!empty($result_check)){
				return 'false';
			}

			$code_num = $indiana_good['price'] / $indiana_good['init_money']; //夺宝码数量
			$allcodes = $this -> create_codes_group($code_num);
			//添加本期
			$new_period = array(
				'uniacid' 		=> $_W['uniacid'],
				'ig_id'			=> $indiana_good['id'],
				'goodsid' 		=> $indiana_good['good_id'],
				'period' 		=> intval($indiana_good['periods']) + 1,
				'init_money'	=> $indiana_good['init_money'],
				'shengyu_codes' => $code_num,
				'zong_codes' 	=> $code_num,
				'allcodes' 		=> $allcodes,
				'period_num' 	=> date('Ymd').substr(time(), -5).substr(microtime(), 2, 5).sprintf('%02d', rand(0, 99)),
				'canyurenshu'	=> 0,
				'status' 		=> 1,
				'create_time' 	=> time()
			);
			$result_insert = pdo_insert('sz_yi_indiana_period',$new_period);
			pdo_update("sz_yi_indiana_goods",array('periods'=>$new_period['period']),array('uniacid'=>$_W['uniacid'],'id'=>$id));
			$this -> create_code($new_period['period_num']);
		}

		/**********计算新的压缩字段********/
		public function create_codes_group($codes_number = 0){
			global $_W;
			$codes_ervery = 5;		//设置每组大小
			$codes_group = intval($codes_number/$codes_ervery);		//夺宝码组数
			$codes_group_last = intval($codes_number%$codes_ervery);	//夺宝码最后一组个数
			if($codes_group_last != 0){
				$codes_group++;		//有余数组数加1
			}

			$codes_group_new = array();
			for($i = 0;$i < $codes_group;$i++){
				if($codes_group_last != 0 && $i == $codes_group-1){
					$codes_group_new[$i] = $i*$codes_ervery.':'.($i*$codes_ervery+$codes_group_last); //最后一个区段
				}else{
					$codes_group_new[$i] = $i*$codes_ervery.':'.($i+1)*$codes_ervery;		//创建区段
				}
			}
			shuffle($codes_group_new);			//打乱数组
			$allcodes = serialize($codes_group_new);		//压缩数组
			return $allcodes;
		}

		/***************计算夺宝码****************/
		public function create_code($period_number = '',$flag = 0){
			global $_W;
			$sql_period = "select id,period_num,codes,shengyu_codes,allcodes,canyurenshu from ".tablename('sz_yi_indiana_period')." where uniacid = :uniacid and period_num = :period_number";
			$data_period = array(
					':uniacid' => $_W['uniacid'],
					':period_number' => $period_number
				);
			$result_period = pdo_fetch($sql_period,$data_period);
			$allcodes = unserialize($result_period['allcodes']);	
			$group_number  = sizeof($allcodes);		//	取得区间组数
			$codes_ervery = 5;		//夺宝码区间个数
				//解压所有码段
			$needcodes = array($group_number);			//设置夺宝码数组

				$need_groupnum = $group_number;
				$left_codes = array_slice($allcodes,$need_groupnum,sizeof($allcodes)-$need_groupnum);
				if(!is_array($left_codes)){
					if($flag == 1){
						return 'false';
					}else{
						self::create_code($period_number,1);
					}						//检测剩余码
				}
				$left_codes = serialize($left_codes);		//压缩剩余夺宝码段
			//剩余码小于单次取得数
			for($i = 0;$i < $need_groupnum ; $i++){
				$codes_ervery_group = array_slice($allcodes,$i,1);		//从第0个取值取一个
				$arr = explode(':', $codes_ervery_group[0]);		//分隔字符串
				for($j = intval($arr[0]) ; $j < intval($arr[1]) ; $j++){
					//合成夺宝码
					$num = $j;		//次序
					$needcodes[$num] = 1000001+$num;	//夺宝码合成
				}
			}
			shuffle($needcodes);			//打乱夺宝码
			if(!is_array($needcodes)){		//检测生成码
				if($flag == 1){
					return 'false';
				}else{
					self::create_code($period_number,1);
				}					
			}
			$needcodes = serialize($needcodes);
			pdo_update('sz_yi_indiana_period',array('codes'=>$needcodes,'allcodes'=>$left_codes),array('uniacid'=>$_W['uniacid'],'period_num'=>$period_number));

		}

		public function dispose($orderid = ''){
			global $_W;
			$set = $this->getSet();

	  //       pdo_update('sz_yi_order', array(
	  //           'status' => 3,
	  //           'finishtime' => time(),
	  //           'refundstate' => 0
	  //       ), array(
	  //           'id' => $orderid,
	  //           'uniacid' => $_W['uniacid']
	  //       ));

			// if (p('commission')) {
			// 	p('commission')->checkOrderFinish($orderid);
			// }


			$order = pdo_fetch('SELECT o.*, og.total, og.goodsid FROM ' . tablename('sz_yi_order') . ' o 
			left join ' . tablename('sz_yi_order_goods') . ' og on (o.id = og.orderid)
			 where o.uniacid=:uniacid and o.id = :orderid and o.status = 1 ',array(
			        ':uniacid'  => $_W['uniacid'],
			        ':orderid'  => $orderid
			    ));
			$codes_number 	= $order['total'];//购买数量
			$openid 		= $order['openid'];
			$period_num 	= $order['period_num'];
			// 本期数据
			$indiana_period = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_period') . ' where uniacid=:uniacid and period_num = :period_num ',array(
			        ':uniacid'  => $_W['uniacid'],
			        ':period_num'  => $period_num
			    ));	

			$sql_record = "select * from ".tablename('sz_yi_indiana_record')." where uniacid = :uniacid and openid = :openid and period_num = :period_num";
			$data_record = array(
					':uniacid' => $_W['uniacid'],
					':openid' => $openid,
					':period_num' => $period_num
				);
			$indiana_record = pdo_fetch($sql_record,$data_record); // 检索购买记录

			$codes = unserialize($indiana_period['codes']);
			$codes_num = sizeof($codes);		//检测现有code的数量
			$buy_codes = array_slice($codes,0,$codes_number);
			if(!is_array($buy_codes)){	//判断分码正确
				return 'false';
			}
			$left_codes = array_slice($codes,$codes_number,$codes_num-$codes_number);
			if(!is_array($left_codes)){	//判断分码正确
				return 'false';
			}
			$left_codes = serialize($left_codes);
			$shengyu_codes = $indiana_period['shengyu_codes'] - $codes_number;
			$canyurenshu = $indiana_period['canyurenshu'] + $codes_number;
			$create_time = time();
			$microtime	 = rand(100,999);
			pdo_update('sz_yi_indiana_period',array('codes'=>$left_codes,'shengyu_codes'=>$shengyu_codes,'canyurenshu'=>$canyurenshu),array('uniacid'=>$_W['uniacid'],'period_num'=>$order['period_num']));

			if(empty($indiana_record)){
				//未曾有购买记录
				$ordersn =$order['ordersn'];
				$record  = array(
					'openid' 		=> $openid,
					'uniacid' 		=> $_W['uniacid'],
					'ordersn' 		=> $ordersn,
					'create_time'	=> $create_time,
					'period_id' 	=> $indiana_period['id'],
					'period_num' 	=> $period_num,
					'codes' 		=> serialize($buy_codes),//购买码
					'count' 		=> $codes_number, //购买个数
					'microtime' 	=> $microtime
				);
				pdo_insert('sz_yi_indiana_record',$record);
			}else{
				$new_code = array_merge(unserialize($indiana_record['codes']),$buy_codes);	//组合两个数组
				$new_count = $codes_number + $indiana_record['count'];
				$result = pdo_update('sz_yi_indiana_record',array('codes' => serialize($new_code),'count' => $new_count),array('uniacid'=>$_W['uniacid'],'period_num' => $period_num,'openid' => $openid));
			}

			$indiana_goods = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_goods') . ' where uniacid=:uniacid and good_id = :good_id ',array(
			        ':uniacid'  => $_W['uniacid'],
			        ':good_id'  => $indiana_period['goodsid']
			    ));	

			$consumerecord = array(
				'openid' 		=> $openid,
				'uniacid' 		=> $_W['uniacid'],
				'num' 			=> $codes_number,
				'ordersn' 		=> $order['ordersn'],
				'codes' 		=> serialize($buy_codes),//购买码
				'period_num' 	=> $period_num,
				'create_time' 	=> $create_time,
				'microtime' 	=> $microtime,
				'ip' 			=> $_SERVER['REMOTE_ADDR']
			);
			pdo_insert("sz_yi_indiana_consumerecord",$consumerecord);
			$province_id = pdo_insertid();
			if ($province_id) {
				$participate_txt= $set['indiana_participate'];
				$participate_txt = str_replace('[商品]', $indiana_goods['title'], $participate_txt);
				$participate_txt = str_replace('[期数]', $indiana_period['period'], $participate_txt);
				$participate_txt = str_replace('[期号]', $indiana_period['period_num'], $participate_txt);
				$participate_txt = str_replace('[人次]', $codes_number, $participate_txt);

				$default_txt = "您已参与【第".$indiana_period['period']."期】  ".$indiana_goods['title']." \r\n\r\n 期号：".$indiana_period['period_num']."\r\n\r\n参与：".$codes_number."人次";
				$msg = array(
				    'first' => array(
				        'value' => $set['indiana_participatetitle']?$set['indiana_participatetitle']:"参与夺宝通知",
				        "color" => "#4a5077"
				    ),
				    'keyword1' => array(
				        'value' => $participate_txt?$participate_txt:$default_txt,
				        "color" => "#4a5077"
				    )
				);
				$detailurl  = $_W['siteroot'] . "/app/index.php?i=" .$_W['uniacid']."&c=entry&method=order&p=indiana&m=sz_yi&do=plugin";
				m('message')->sendCustomNotice($openid, $msg, $detailurl);
			}
			if ($shengyu_codes <= 0) {
				self::jiexiaotime($period_num);
			}	

		}
		//统计揭晓时间
		public function jiexiaotime ($period_num){
			global $_W, $_GPC;
			$hour	= date('H');
			$minute = date('i');
			//销售时间：10:00~22:00(72期)10分钟一期，22:00~02:00(48期)5分钟一期 
			if ( $hour >= '10' && $hour <= '22') {
				$raise = 10 - substr($minute,-1) + 3;
			} elseif ( $hour > '22' || $hour <= '2'){
				if ( substr($minute,-1) < 5) {
					$raise = 5 - substr($minute,-1) + 3;
				} else {
					$raise = 10 - substr($minute,-1) + 3;
				}
			} else {
				$raise = 5;
			}
			$jiexiao = time() + $raise * 60;	
			pdo_update('sz_yi_indiana_period',array('jiexiao_time'=>$jiexiao, 'status'=>'2'),array('uniacid'=>$_W['uniacid'],'period_num'=>$period_num));

			$period = pdo_fetch("SELECT ip.goodsid, ip.period, ig.max_periods, ig.id FROM " . tablename('sz_yi_indiana_period') . " ip 
			left join " . tablename('sz_yi_indiana_goods') . " ig on (ip.goodsid = ig.good_id) 
			 WHERE ip.uniacid = :uniacid and ip.period_num = :period_num ",array(
			 	'uniacid' => $_W['uniacid'],
			 	'period_num' => $period_num
			 ));
			if ($period['max_periods']-$period['period'] > 0) {
				self::setPeriod($period['id']);
			}
		}

		//开奖
		public function autoexec ($uniacid) {
			global $_W, $_GPC;
			$_W['uniacid'] = $uniacid;
			$set = m('plugin')->getpluginSet('indiana', $_W['uniacid']);
			set_time_limit(0);

			$indiana = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_indiana_period') . " WHERE uniacid = :uniacid AND jiexiao_time <= :jiexiao_time AND status = :status ",array(
					':uniacid' => $_W['uniacid'],
					':jiexiao_time' => time(),
					':status' => 2
				));

			$indiana_message = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_indiana_period') . " WHERE uniacid = :uniacid AND  status = :status ",array(
					':uniacid' => $_W['uniacid'],
					':status' => 2
				));
			foreach ($indiana_message as $key => $value) {
				if ( ( $value['jiexiao_time']-60 >= time() ) && ( $value['jiexiao_time']-120 < time() ) ) {
					
					$indiana_goods = pdo_fetch('SELECT * FROM ' . tablename('sz_yi_indiana_goods') . ' where uniacid=:uniacid and good_id = :good_id ',array(
				        ':uniacid'  => $_W['uniacid'],
				        ':good_id'  => $value['goodsid']
				    ));

					$indiana_record = pdo_fetchall('SELECT * FROM ' . tablename('sz_yi_indiana_record') . ' where uniacid=:uniacid and period_num = :period_num ',array(
				        ':uniacid'  => $_W['uniacid'],
				        ':period_num'  => $value['period_num']
				    ));

					$announced_txt= $set['indiana_announced'];
					$announced_txt = str_replace('[商品]', $indiana_goods['title'], $announced_txt);
					$announced_txt = str_replace('[期数]', $value['period'], $announced_txt);
					$default_txt = "您已参与【第".$value['period']."期】  ".$indiana_goods['title']." 即将揭晓结果";

					$msg = array(
					    'first' => array(
					        'value' => $set['indiana_announcedtitle']?$set['indiana_announcedtitle']:"您参与的夺宝即将揭晓",
					        "color" => "#4a5077"
					    ),
					    'keyword1' => array(
					        'value' => $announced_txt?$announced_txt:$default_txt,
					        "color" => "#4a5077"
					    )
					);
					$detailurl  = "http://" . $_SERVER['SERVER_NAME'] . "/app/index.php?i=" .$_W['uniacid']."&c=entry&p=detail&do=shop&m=sz_yi&id=".$value['goodsid']."&periodnum=".$value['period_num']."&indiana=1";
					foreach ($indiana_record as $k => $v) {
						m('message')->sendCustomNotice($v['openid'], $msg, $detailurl);
					}
				}
			}


			if (!$indiana) {
				return false;
			}
			foreach ($indiana as $key => $value) {
				self::createtime_winer($value['id'],$value['period_num'],$_W['uniacid']);
			}
			

		}
		// /***********开奖计算*********/
		public function createtime_winer($periodid = '',$period_number = '',$uniacid){
			global $_W;
			$_W['uniacid'] = $uniacid;
			$src = 'http://f.apiplus.cn/cqssc.json';
			$src .= '?_='.time();
			$json = file_get_contents(urldecode($src));
			$json = json_decode($json);
			$periods = $json->data[0]->expect;

			//本期最后购买时间pdo_fetchcolumn
			$lasttime = pdo_fetchcolumn("SELECT create_time FROM " . tablename('sz_yi_indiana_consumerecord') . " WHERE uniacid = :uniacid and period_num = :period_num order by create_time desc limit 1", array(
			        ':uniacid'      => $_W['uniacid'],
			        ':period_num'   => $period_number
			    ));

			$s_indiana = pdo_fetchall("SELECT ic.openid, ic.create_time, ic.microtime, m.nickname from " . tablename('sz_yi_indiana_consumerecord') . " ic 
			    left join " . tablename('sz_yi_member') . " m on( ic.openid=m.openid )  
			    where ic.uniacid = :uniacid  and ic.create_time <= :create_time order by ic.create_time desc limit 20 ",
			    array(
			        ':uniacid'      => $_W['uniacid'],
			        ':create_time'   => $lasttime
			    ));
			    $numa = 0;
			    foreach ($s_indiana as &$row) {
			        $row['numa'] = date("His", $row['create_time']).$row['microtime'];
			        $numa += date("His", $row['create_time']).$row['microtime'];
			        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);

			    }
			    unset($row);

				$numb = str_replace(array(","),"",$json ->data[0]->opencode);

				$period = pdo_fetch("SELECT id,goodsid,zong_codes FROM " . tablename('sz_yi_indiana_period') . " WHERE id ='{$periodid}'");

				$endtime = time();				//揭晓时间 announced
				$wincode = fmod(($numa + $numb),$period['zong_codes']) + 1000001;
				$comdata = array(
					'uniacid' => $_W['uniacid'], 
					'numa' => $numa, 
					'numb' => $numb, 
					'periods' => $periods,//开奖期号 
					'pid' => $period['id'], 
					'wincode' => intval($wincode), //开奖号码
					'createtime' => $endtime
				);
				pdo_insert('sz_yi_indiana_comcode',$comdata);				//写入中奖计算记录
				pdo_update('sz_yi_indiana_period',array('code'=>$wincode,'endtime'=>$endtime,'status'=>2),array('uniacid'=>$_W['uniacid'],'period_num'=>$period_number));	//写入中奖信息
				self::get_winner($period_number,$period['id'],$wincode,$_W['uniacid']);
				

		}

		
		/******************获取中奖人信息****************/
		public function get_winner($period_num = '', $periodid = '' , $wincode = '',$uniacid){
			global $_W;
			$_W['uniacid'] = $uniacid;
			$set = m('plugin')->getpluginSet('indiana', $_W['uniacid']);
			//更新完毕，计算获奖信息
			$sql_record_winner = "select * from ".tablename('sz_yi_indiana_record')." where uniacid = :uniacid and period_num = :period_num";
			$data_record_winner = array(
					':uniacid' => $_W['uniacid'],
					':period_num' => $period_num
				);
			$records = pdo_fetchall($sql_record_winner,$data_record_winner);		//查询所有本期商品交易记录
			//计算获奖人
			foreach ($records as$k=> $v) {
				$scodes=unserialize($v['codes']);//转换商品code
				for ($i=0; $i < count($scodes) ; $i++) { 
					if ($scodes[$i]==$wincode) {
						$lack_period['openid']=$v['openid'];
						$lack_period['recordid']=$v['id'];
						break;
					}
				}
			}

			if(empty($lack_period['openid'])){
				pdo_delete('sz_yi_indiana_comcode',array('pid'=>$periodid));
				self::createtime_winer($periodid,$period_num,$uniacid);
			}else{

				//计算中奖订单
				$sql_record_order = "select * from ".tablename('sz_yi_indiana_consumerecord')." where uniacid = :uniacid and openid = :openid and period_num = :period_num";
				$data_record_order = array(
						':uniacid' => $_W['uniacid'],
						':openid' => $lack_period['openid'],
						':period_num' => $period_num
					);
				$order_data = pdo_fetchall($sql_record_order,$data_record_order);
				foreach ($order_data as$k=> $v) {
					$scodes=unserialize($v['codes']);//转换商品code
					for ($i=0; $i < count($scodes) ; $i++) { 
						if ($scodes[$i]==$wincode) {
							$lack_period['ordersn']=$v['ordersn'];
							pdo_update('sz_yi_indiana_record',array('ordersn'=>$v['ordersn']),array('uniacid'=>$_W['uniacid'],'id'=>$lack_period['recordid']));	//写入中奖信息
							break;
						}
					}
				}
		        pdo_update('sz_yi_order', array(
		            'status' => 3,
		            'finishtime' => time(),
		            'refundstate' => 0
		        ), array(
		            'period_num' => $period_num,
		            'uniacid' => $_W['uniacid']
		        ));
		        //执行夺宝订单 分销
				$orders = pdo_fetchall("select id from ".tablename('sz_yi_order')." where uniacid = :uniacid and  period_num = :period_num",array(':uniacid' => $_W['uniacid'],':period_num' => $period_num));
				foreach ($orders as $o) {
					if (p('commission')) {
						p('commission')->checkOrderFinish($o['id']);
					}
				}

				$pro_m = m('member')->getMember($lack_period['openid']);//获奖用户信息
				$lack_record = pdo_fetch("select count from ".tablename('sz_yi_indiana_record')." where uniacid='{$_W['uniacid']}' and openid='{$lack_period['openid']}' and period_num='{$period_num}'");
				$lack_period['code']=$wincode;
				$lack_period['mid']=$pro_m['id'];
				$lack_period['nickname']=$pro_m['nickname'];
				$lack_period['avatar']=$pro_m['avatar'];
				$lack_period['partakes']=$lack_record['count'];
				$lack_period['status']='3';
				//更新中奖信息到这期数据
				pdo_update('sz_yi_indiana_period', $lack_period, array('id' => $periodid));


				$indiana_goods = pdo_fetch('SELECT ig.*,ip.period FROM ' . tablename('sz_yi_indiana_period') . ' ip 
				left join ' . tablename('sz_yi_indiana_goods') . ' ig on (ip.goodsid = ig.good_id)
				where ip.uniacid=:uniacid and ip.period_num = :period_num ',array(
			        ':uniacid'  => $_W['uniacid'],
			        ':period_num'  => $period_num
			    ));
				$winning_txt= $set['indiana_winning'];
				$winning_txt = str_replace('[商品]', $indiana_goods['title'], $winning_txt);
				$winning_txt = str_replace('[期数]', $indiana_goods['period'], $winning_txt);
				$winning_txt = str_replace('[幸运号]', $wincode, $winning_txt);
				$winning_txt = str_replace('[本期参与人次]', $lack_record['count'], $winning_txt);

				$default_txt = "您参与的夺宝商品【第".$indiana_goods['period']."期】  ".$indiana_goods['title']." \r\n\r\n 幸运号码".$wincode."\r\n\r\n本期参与：".$lack_record['count']."人次";
				$msg = array(
				    'first' => array(
				        'value' => $set['indiana_winningtitle']?$set['indiana_winningtitle']:"开奖通知",
				        "color" => "#4a5077"
				    ),
				    'keyword1' => array(
				        'value' => $winning_txt?$winning_txt:$default_txt,
				        "color" => "#4a5077"
				    )
				);
				
				$detailurl  = "http://" . $_SERVER['SERVER_NAME'] . "/app/index.php?i=" .$_W['uniacid']."&c=entry&method=order&p=indiana&op=lucky&m=sz_yi&do=plugin";
				m('message')->sendCustomNotice($lack_period['openid'], $msg, $detailurl);

			}
		}

		public function getorder($period_num) {
			global $_W;
			$sql_record = "select ip.*, ig.title from ".tablename('sz_yi_indiana_period')." ip 
			left join ".tablename('sz_yi_indiana_goods')." ig on (ip.goodsid = ig.good_id) 
			where ip.uniacid = :uniacid and ip.period_num = :period_num ";
			$data_record = array(
					':uniacid' => $_W['uniacid'],
					':period_num' => $period_num
				);
			$indiana_record = pdo_fetch($sql_record,$data_record); // 检索购买记录
			return $indiana_record;
		}
	}
}

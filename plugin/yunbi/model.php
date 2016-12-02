<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('YunbiModel')) {

	class YunbiModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}
		//消费获得虚拟币
		public function GetVirtualCurrency($orderid) {
			global $_W, $_GPC;
			$set = $this->getSet();

			if (empty($orderid)) {
				return false;
			}
			$order_goods = pdo_fetchall("SELECT g.isyunbi,g.yunbi_consumption,g.yunbi_commission,o.openid,o.price,o.dispatchprice,m.id,m.openid as mid ,g.isdeclaration,g.virtual_declaration,og.declaration_mid FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_member') . " m  on o.openid = m.openid left join " . tablename("sz_yi_order_goods") . " og on og.orderid = o.id  left join " . tablename("sz_yi_goods") . " g on g.id = og.goodsid WHERE o.id = :orderid and o.uniacid = :uniacid and m.uniacid = :uniacid",
				array(':orderid' => $orderid,':uniacid' => $_W['uniacid']
			));
			if (empty($order_goods)) {
				return false;
			}

			$virtual_currency = 0;
			$virtual_agent = 0;
			$declaration = array();
			foreach($order_goods as $good){
				if($good['isyunbi'] == 1 && $good['declaration_mid'] != ''){
					if ($good['yunbi_consumption'] > 0) {
						$virtual_currency += ($good['price'] - $good['dispatchprice']) * $good['yunbi_consumption'] / 100;
					} else {
						$virtual_currency += ($good['price'] - $good['dispatchprice']) * $set['consumption'] / 100;
					}
					$is_goods_return = true;
					if ($good['yunbi_commission'] > 0) {
						$virtual_agent += ( $good['price'] - $good['dispatchprice'] ) * $good['yunbi_commission'] / 100;
					}
				}

				if ($good['isdeclaration'] == '1') {
					//$virtual_declaration += $good['virtual_declaration'];
					$declaration[$good['declaration_mid']] += $good['virtual_declaration'];
				}
			}

			if ($declaration) {
				foreach ($declaration as $key => $value) {
					if ($value > 0) {

						$declaration_info = m('member')->getMember($key);
						$this->setVirtualCurrency($declaration_info['openid'],$value);
						$declaration_log[$key] = array(
					        'id' 			=> $declaration_info['id'],
					        'openid' 		=> $declaration_info['openid'],
					        'credittype' 	=> 'virtual_currency',
					        'money' 		=> $value,
							'remark'		=> '报单获得'.$value.$set['yunbi_title']
					    );
						//$this->addYunbiLog($_W['uniacid'],$declaration_log,'13');

						$declaration = array(
							'keyword1' => array(
								'value' => '报单获得'.$set['yunbi_title'].'通知',
								'color' => '#73a68d'),
							'keyword2' =>array(
								'value' => '本次获得'.$value.$set['yunbi_title'],
								'color' => '#73a68d')
							);
						m('message')->sendCustomNotice($declaration_info['openid'], $declaration);
					}
				}
				$this->addYunbiLogs($_W['uniacid'],$declaration_log,'13');
			}



			if ($set['isyunbi'] == 1 && $set['isconsumption'] == 1) {
				//商品 没有返消费币 返回
				if(!$is_goods_return)
				{
					return false;
				}
				if ($set['acquisition'] == 0) {
					//echo "直接获得";
					$this->setVirtualCurrency($order_goods[0]['openid'],$virtual_currency);
					$data_log = array(
				        'id' 			=> $order_goods[0]['id'],
				        'openid' 		=> $order_goods[0]['openid'],
				        'credittype' 	=> 'virtual_currency',
				        'money' 		=> $virtual_currency,
						'remark'		=> '购物获得'.$virtual_currency.$set['yunbi_title']
				    );
					$this->addYunbiLog($_W['uniacid'],$data_log,'1');

					$messages = array(
						'keyword1' => array(
							'value' => '购物获得'.$set['yunbi_title'].'通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次获得'.$virtual_currency.$set['yunbi_title'],
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($order_goods[0]['openid'], $messages);

				} else {		
					//echo "间接获得";
					$this->setVirtualCurrency($order_goods[0]['openid'],$virtual_currency,'virtual_temporary');
					$this->setVirtualCurrency($order_goods[0]['openid'],$virtual_currency,'virtual_temporary_total');
					$data_log = array(
				        'id' 			=> $order_goods[0]['id'],
				        'openid' 		=> $order_goods[0]['openid'],
				        'credittype' 	=> 'virtual_temporary',
				        'money' 		=> $virtual_currency,
						'remark'		=> '购物-间接获得'.$virtual_currency.$set['yunbi_title']
				    );
					$this->addYunbiLog($_W['uniacid'],$data_log,'1');

					$messages = array(
						'keyword1' => array(
							'value' => '购物获得'.$set['yunbi_title'].'通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次获得'.$virtual_currency.'待转'.$set['yunbi_title'].',等待转入'.$set['yunbi_title'],
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($order_goods[0]['openid'], $messages);

					if ( $virtual_agent > 0) {
						$member = m('member')->getMember($order_goods[0]['openid']);
						$agentinfo = m('member')->getMember($member['agentid']);
						if ($agentinfo) {
							$this->setVirtualCurrency($agentinfo['openid'],$virtual_agent);
							//$this->setVirtualCurrency($agentinfo['openid'],$virtual_agent,'virtual_temporary_total');
							$data_log = array(
						        'id' 			=> $agentinfo['id'],
						        'openid' 		=> $agentinfo['openid'],
						        'credittype' 	=> 'virtual_currency',
						        'money' 		=> $virtual_agent,
								'remark'		=> '购物-分销上级-获得'.$virtual_agent.$set['yunbi_title']
						    );
							$this->addYunbiLog($_W['uniacid'],$data_log,'1');
							$messages = array(
								'keyword1' => array(
									'value' => '分销上级获得'.$set['yunbi_title'].'通知',
									'color' => '#73a68d'),
								'keyword2' =>array(
									'value' => '本次获得'.$virtual_agent.$set['yunbi_title'],
									'color' => '#73a68d')
							);
							m('message')->sendCustomNotice($agentinfo['openid'], $messages);
						}
					}
				}
			}
		}

		//分销商获得虚拟币
		public function GetVirtual_Currency($set,$uniacid) {
			global $_W, $_GPC;
			$current_time = time();

			$sql = "update ".tablename('sz_yi_member')." as m join (select sm.agentid, count(1) as agent_count from ".tablename('sz_yi_member')." sm where sm.`uniacid` =  " . $uniacid . " AND sm.status = '1' AND sm.isagent = 1 group by sm.agentid) as ac on m.id = ac.agentid set `virtual_currency` = virtual_currency + ac.agent_count *  " . $set['distribution'] . ", last_money =  ac.agent_count *  " . $set['distribution'] . ",updatetime = " .$current_time. " where m.`uniacid` =  " . $uniacid . " AND status = '1' AND isagent = '1' ";
			pdo_fetchall($sql);

			$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
				array(':updatetime' => $current_time,':uniacid' => $uniacid
			));	
			foreach ($update_member as $key => $value) {
				$data_log['$key'] = array(
	                'id' 			=> $value['id'],
	                'openid' 		=> $value['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $value['last_money'],
					'remark'		=> '分销下线获得'.$value['last_money'].$set['yunbi_title']
                );
				//$this->addYunbiLog($uniacid,$data_log,'2');
				$messages = array(
					'keyword1' => array(
						'value' => '分销下线获得'.$set['yunbi_title'].'通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次获得'.$value['last_money'].$set['yunbi_title'],
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($value['openid'], $messages);
			}
			$this->addYunbiLogs($uniacid,$data_log,'2');
		}
	
		//虚拟币返现到余额
		public function PerformYunbiReturn($set,$uniacid){
			global $_W, $_GPC;
			$current_time = time();
			if ($set['isreturn_or_remove'] == 0 && $set['isreturnremove'] == 1 ) {
				$mc_sql = "update ".tablename('mc_members')." as m join (select sm.virtual_currency, sm.uid from ".tablename('sz_yi_member')." sm where sm.`uniacid` =  " . $uniacid . " and sm.virtual_currency > 0 ) as ac on m.uid = ac.uid set m.credit2 = credit2 + (ac.virtual_currency * " .$set['yunbi_return']. " / 100)  where m.`uniacid` =  " . $uniacid ;
				pdo_fetchall($mc_sql);
				$sz_sql = "update ".tablename('sz_yi_member')."  set credit2 = credit2 + (virtual_currency * " .$set['yunbi_return']. " / 100), last_money =  (virtual_currency * " .$set['yunbi_return']. " / 100) ,updatetime = " .$current_time. ", `virtual_currency` = virtual_currency - (virtual_currency * " .$set['yunbi_return']. " / 100) where `uniacid` =  " . $uniacid ." AND virtual_currency > 0";
				pdo_fetchall($sz_sql);

				$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
					array(':updatetime' => $current_time,':uniacid' => $uniacid
				));	

				foreach ($update_member as $key => $value) {
					$data_log[$key] = array(
		                'id' 			=> $value['id'],
		                'openid' 		=> $value['openid'],
		                'credittype' 	=> 'virtual_currency',
		                'money' 		=> $value['last_money'],
						'remark'		=> $set['yunbi_title']."返现到余额,扣除".$value['last_money']
	                );
	                //$this->addYunbiLog($uniacid,$data_log,'5');
					$messages = array(
						'keyword1' => array(
							'value' => $set['yunbi_title'].'返现通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次返现到余额'.$value['last_money'].$set['yunbi_title'].",余额获得：".$value['last_money']."元",
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
				$this->addYunbiLogs($uniacid,$data_log,'5');
			}
		}

		//临时虚拟币转入到云币
		public function PerformYunbiInto($set,$uniacid){
			global $_W, $_GPC;
			$current_time = time();
			if ($set['isreturn_or_remove'] == 3) {
				//小于等于返现比例
				pdo_fetchall("update ".tablename('sz_yi_member')."  set virtual_currency = virtual_currency + virtual_temporary, last_money =  virtual_temporary ,updatetime = " .$current_time. ", `virtual_temporary` = 0 where `uniacid` =  " . $uniacid ." AND virtual_temporary <= (virtual_temporary_total * " .$set['yunbi_return']. " / 100) AND virtual_temporary > 0;");
				//大于返现比例
				pdo_fetchall("update ".tablename('sz_yi_member')."  set virtual_currency = virtual_currency + (virtual_temporary_total * " .$set['yunbi_return']. " / 100), last_money =  (virtual_temporary_total * " .$set['yunbi_return']. " / 100) ,updatetime = " .$current_time. ", `virtual_temporary` = virtual_temporary - (virtual_temporary_total * " .$set['yunbi_return']. " / 100) where `uniacid` =  " . $uniacid ." AND virtual_temporary > 0;");
				//上级获得相应数量的云币
				$sql = "update ".tablename('sz_yi_member')." as m join (select sm.agentid, sm.id as smid from ".tablename('sz_yi_member')." sm where sm.`uniacid` =  " . $uniacid . " AND sm.updatetime = " .$current_time. " ) as ac on m.id = ac.agentid set `virtual_currency` = virtual_currency + " . $set['the_superior_obtain'] . ", last_money = last_money + " . $set['the_superior_obtain'] . ",updatetime = " .$current_time. " where m.`uniacid` =  " . $uniacid . " AND status = '1' AND isagent = '1' ";
				pdo_fetchall($sql);

				$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
					array(':updatetime' => $current_time,':uniacid' => $uniacid
				));	
				foreach ($update_member as $key => $value) {
					$data_log[$key] = array(
		                'id' 			=> $value['id'],
		                'openid' 		=> $value['openid'],
		                'credittype' 	=> 'virtual_currency',
		                'money' 		=> $value['last_money'],
						'remark'		=> "待转".$set['yunbi_title']."转入".$set['yunbi_title'].",增加".$value['last_money']
	                );
	                //$this->addYunbiLog($uniacid,$data_log,'10');// 10 虚拟币转入云币
					$messages = array(
						'keyword1' => array(
							'value' => $set['yunbi_title'].'转入通知',
							'color' => '#73a68d'),
						'keyword2' =>array(
							'value' => '本次共转入'.$value['last_money'].'到'.$set['yunbi_title'],
							'color' => '#73a68d')
						);
					m('message')->sendCustomNotice($value['openid'], $messages);
				}
				$this->addYunbiLogs($uniacid,$data_log,'10');// 10 虚拟币转入云币
			}
		}
		//虚拟币清除
		public function RemoveYunbi($set,$uniacid){
			global $_W, $_GPC;
			$current_time = time();

			$sql = "update ".tablename('sz_yi_member')."  set last_money = virtual_currency ,updatetime = " .$current_time. ", `virtual_currency` = virtual_currency - virtual_currency where `uniacid` =  " . $uniacid ." AND virtual_currency > 0";
			pdo_fetchall($sql);
			$update_member = pdo_fetchall("SELECT id, uniacid, openid, last_money, updatetime FROM " . tablename('sz_yi_member') . " WHERE updatetime = :updatetime and uniacid = :uniacid ",
				array(':updatetime' => $current_time,':uniacid' => $uniacid
			));	
			foreach ($update_member as $key => $value) {
				$data_log[$key] = array(
	                'id' 			=> $value['id'],
	                'openid' 		=> $value['openid'],
	                'credittype' 	=> 'virtual_currency',
	                'money' 		=> $value['last_money'],
					'remark'		=> "清除".$set['yunbi_title']
                );
				//$this->addYunbiLog($uniacid,$data_log,'6');
				$messages = array(
					'keyword1' => array(
						'value' => $set['yunbi_title'].'清除通知',
						'color' => '#73a68d'),
					'keyword2' =>array(
						'value' => '本次清除'.$value['last_money'].$set['yunbi_title'],
						'color' => '#73a68d')
					);
				m('message')->sendCustomNotice($value['openid'], $messages);
			}
			$this->addYunbiLogs($uniacid,$data_log,'6');
		}
		/*
		 * 添加 虚拟币log
		 * type 1:购物获得 2:分销下线获得 3:购物抵扣 4:返还抵扣 5:返现到余额 6:清除明细
		 * data log数组
		 */	
		public function addYunbiLog ($uniacid,$data=array(),$type){
			$status = isset($data['status'])?$data['status']:'1';
			$data_log = array(
				'uniacid' 		=> $uniacid,
			    'mid' 			=> $data['id'],
			    'openid' 		=> $data['openid'],
			    'credittype' 	=> $data['credittype'],
			    'money' 		=> $data['money'],
			    'status' 		=> $status,
			    'returntype' 	=> $type,
				'create_time'	=> time(),
				'remark'		=> $data['remark']
			);
			pdo_insert('sz_yi_yunbi_log', $data_log);
		}
		/*
		 * 添加 虚拟币log
		 * type 1:购物获得 2:分销下线获得 3:购物抵扣 4:返还抵扣 5:返现到余额 6:清除明细
		 * data log数组
		 */	
		public function addYunbiLogs ($uniacid,$data=array(),$type){
			if(!empty($data)){
				$sql = '';
				foreach ($data as $key => $value) {
					$status = isset($value['status'])?$value['status']:'1';
			        $sql .= "INSERT INTO " . tablename('sz_yi_yunbi_log') . " (`uniacid`, `mid`, `openid`, `credittype`, `money`, `status`, `returntype`, `create_time`, `remark`) VALUES ('".$uniacid."', '".$value['id']."', '".$value['openid']."','".$value['credittype']."','".$value['money']."','".$status."','".$type."','".TIMESTAMP."','".$value['remark']."');";
				}
				pdo_fetch($sql); 
			}
			
		}
		public function PerformRecycling($set,$uniacid) {
			global $_W, $_GPC;
			$recycling = (int)$set['recycling'] * 3600;


		    $trading = pdo_fetchall("select * from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid and returntype = :returntype and money <> 0 and status = :status AND create_time <= :create_time", array(
                ':uniacid' => $uniacid,
                ':returntype' => '11',
                'status' => '0',
                'create_time' => time()-$recycling
            ));
            if ($trading) {
	          	foreach ($trading as $row) {
	          		$price = $row['money'] * $set['trading_money'] / $set['credit'];
    				$poundage = $price * $set['poundage'] / 100;
				    $sql = "update ".tablename('sz_yi_yunbi_log')."  set status = 3 where `uniacid` =  " . $uniacid ." AND status = '0' AND id = ".$row['id'];
			        pdo_fetchall($sql);
			        $result = m('member')->setCredit($row['openid'], 'credit2', $price - $poundage, array(
			            $_W['member']['uid'],
			            '出让'.$yunbi_title.'-公司回购-余额获得:' . $price - $poundage . '手续费:' .$poundage
			        ));
			        // 出售人推送信息
	    		}	
            }

            
		}
		public function MoneySumTotal($conditions='',$mid='') {
			global $_W, $_GPC;
			if (!empty($mid)) {
			    $total = pdo_fetchcolumn("select sum(money) as money from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid ".$conditions." and money <> 0 and mid = :mid ", array(
			        ':uniacid' => $_W['uniacid'],
			        ':mid' => $mid
			    ));
			} 
		    return !empty($total)?$total:'0';
		}
		public function CountTotal($conditions='') {
			global $_W, $_GPC;
			    $total = pdo_fetchcolumn("select count(1) as money from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid ".$conditions." and money <> 0 ", array(
			        ':uniacid' => $_W['uniacid']
			    ));
		    return !empty($total)?$total:'0';
		}
		public function setVirtualCurrency($openid='',$currency,$fieldname='') {
			global $_W, $_GPC;
			if (empty($fieldname)) {
				$fieldname = 'virtual_currency';
			}
			pdo_fetchall("update ".tablename('sz_yi_member')." set ".$fieldname." = ".$fieldname." + ".$currency." where `uniacid` =  " . $_W['uniacid'] . " AND openid = '".$openid."' ");
		}

		public function autoexec ($uniacid) {
			global $_W, $_GPC;
			$_W['uniacid'] = $uniacid;
			set_time_limit(0);
			load()->func('file');
	        $tmpdirs = IA_ROOT . "/addons/sz_yi/tmp/yunbi/".date("Ymd");
			$yunbi_log = $tmpdirs."/yunbi_log.txt";
			$log_content = array();
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."云币LOG开始========================\r\n";
	        if (!is_dir($tmpdirs)) {
	            mkdirs($tmpdirs);
	        }
	        $set = m('plugin')->getpluginSet('yunbi', $_W['uniacid']);
	        $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."虚拟币返现到余额开始--------\r\n";
	       //虚拟币返现到余额
	        if (!empty($set) && $set['isreturn_or_remove'] == 0 && $set['isreturnremove'] == 1 ) {
	            if ($set['acquisition'] == 0) {
	                $return_validation   = $tmpdirs."/return_".date("Ymd").$_W['uniacid'].".txt";
	                if (!file_exists($return_validation)) {
	                    $isexecute = false;
	                    if (date('H') == $set['yunbi_returntime']) {
	                        if (!isset($set['current_d']) || $set['current_d'] != date('d')) {
	                            //$data  = array_merge($set, array('current_d'=>date('d')));
	                            $set['current_d'] = date('d');
	                            $this->updateSet($set);
	                            $isexecute = true;
	                        }
	                    }
	                }else{
	                	$log_content[] = "uniacid:".$_W['uniacid']."时间：".date("Y-m-d")."虚拟币已返现！\r\n";
	                }
	                if ( $isexecute ) {
	                    //虚拟币返现到余额
	                   	$this->PerformYunbiReturn($set, $_W['uniacid']);
	                    touch($return_validation);
	                    $log_content[] = "uniacid:".$_W['uniacid']."时间：".date("Y-m-d")."虚拟币返现到余额成功！\r\n";
	                } else {
	                	$log_content[] = "uniacid:".$_W['uniacid']."时间：".date("Y-m-d")."当前虚拟币不可返现到余额！\r\n";
	                }
	            }
	        }
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."虚拟币返现到余额结束--------\r\n";
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."清除虚拟币开始--------\r\n";
	        if (!empty($set) && $set['isreturn_or_remove'] == 1 && $set['isreturnremove'] == 1) {
	            //清除虚拟币
	            $remove_validation   = $tmpdirs."/remove_".date("Ymd").$_W['uniacid'].".txt";
	            if (!file_exists($remove_validation)) {
	                $remove_times = explode("||",$set['yunbi_remove_times']);
	                $isexecute = false;
	                foreach ($remove_times as $k => $v) {
	                    if (str_replace(array("日","点"),"",$v) == date('dH')) {
	                        if (!isset($set['remove_d']) || $set['remove_d'] != date('d')) {
	                            $set['remove_d'] = date('d');
	                            $this->updateSet($set);
	                            $isexecute = true;
	                            break;
	                        }
	                    }
	                }
	            }else{
	            	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."虚拟币已清除！\r\n";
	            }

	            if ($_GPC['testtype'] == 'remove') {
	                $isexecute = true;
	            }
	            if ( $isexecute ) {
	                //清除虚拟币
	                $this->RemoveYunbi($set, $_W['uniacid']);
	                touch($remove_validation);
	                $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."虚拟币清除成功！\r\n";
	            } else {
	            	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."当前虚拟币不可清除！\r\n";
	            }


	        }
	        $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."清除虚拟币结束--------\r\n";
	        $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."临时虚拟币转入云币开始--------\r\n";
	        if (!empty($set) && $set['isreturn_or_remove'] == 2) { 
	            //临时虚拟币转入云币关闭！
	            $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."临时虚拟币转入云币关闭！\r\n";
	        } elseif (!empty($set) && $set['isreturn_or_remove'] == 3) {
	            //临时虚拟币转入云币开启！
	            if ($set['acquisition'] == 1) {
	                $yunbi_into  = $tmpdirs."/into_".date("Ymd").$_W['uniacid'].".txt";
	                if (!file_exists($yunbi_into)) {
	                    $isexecute = false;
	                    if (date('H') == $set['yunbi_returntime']) {
	                        if (!isset($set['into_d']) || $set['into_d'] != date('d')) {
	                            $set['into_d'] = date('d');
	                            $this->updateSet($set);
	                            $isexecute = true;
	                        }
	                    }
	                }else{
	                	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."临时虚拟币已转入到".$set['yunbi_title']."！\r\n";
	                }
	                if ($_GPC['testtype'] == 'into') {
	                    $isexecute = true;
	                }
	                if ( $isexecute ) {
	                    //虚拟币返现到余额
	                    $this->PerformYunbiInto($set, $_W['uniacid']);
	                    touch($yunbi_into);
	                    $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."临时虚拟币转入到".$set['yunbi_title']."成功！\r\n";
	                } else {
	                	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."当前临时虚拟币不可转入到".$set['yunbi_title']."！\r\n";
	                }

	            }
	        }
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."临时虚拟币转入云币结束--------\r\n";
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."分销下线获得虚拟币开始--------\r\n";
	        //分销下线获得虚拟币
	        if (!empty($set) && $set['isdistribution']) {
	            $d_validation   = $tmpdirs."/d_".date("Ymd").$_W['uniacid'].".txt";
	            if (!file_exists($d_validation)) {
	                        $this->updateSet($set);
	                if (date('H') == $set['distribution_returntime']) {
	                    if (!isset($set['distribution_d']) || $set['distribution_d'] != date('d')) {
	                        $set['distribution_d'] = date('d');
	                        $this->updateSet($set);
	                        $isdistribution = true;
	                    }
	                }
	            }else{
	            	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."分销下线已获得虚拟币！ \r\n";
	            }

	            if ($_GPC['testtype'] == 'distribution') {
	                $isdistribution = true;
	            }
	            if ( $isdistribution) {
	                //分销商获得虚拟币
	                $this->GetVirtual_Currency($set, $_W['uniacid']);
	                touch($d_validation);
	                $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."分销下线获得虚拟币成功！ \r\n";
	            } else {
	            	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."当前分销下线不可获得虚拟币！ \r\n";
	            }
	        }
	        $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."分销下线获得虚拟币结束--------\r\n";
	        $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."公司回购开始--------\r\n";
	        //公司回购
	        if (!empty($set) && $set['recycling'] >= '1') {
	            $this->PerformRecycling($set, $_W['uniacid']);
	            $log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."公司回购成功！ \r\n";
	        }else{
	        	$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."公司回购失败！ \r\n";
	        }
			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."公司回购结束--------\r\n";

			$log_content[] = "uniacid:".$_W['uniacid'].date("Y-m-d H:i:s")."云币LOG结束========================\r\n\r\n\r\n";
        	file_put_contents($yunbi_log,$log_content,FILE_APPEND);
		}

	}
}
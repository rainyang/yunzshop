<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('MerchantModel')) {
	class MerchantModel extends PluginModel
	{
		Private $child_centers = array();
		public function getInfo($openid){
			global $_W;

			$setdata = pdo_fetch("select * from " . tablename('sz_yi_sysset') . ' where uniacid=:uniacid limit 1', array(
			    ':uniacid' => $_W['uniacid']
			));
			$setsyss     = unserialize($setdata['sets']);
			$settrade = $setsyss['trade'];

			$set = $this->getSet();
			$info = array();
			if (empty($openid)) {
				return;
			}
			$center = $this->isCenter($openid);
			if (empty($center)) {
				return;
			}
			$member = m('member')->getInfo($openid);
			if (!empty($set['limit_day'])) {
                $time = time();
                if (!empty($member['id'])) {
                    $last_apply_time = pdo_fetchcolumn("SELECT apply_time FROM " . tablename('sz_yi_merchant_apply') . "WHERE uniacid={$_W['uniacid']} AND member_id={$member['id']} ORDER BY id DESC LIMIT 1");
                    if (!empty($last_apply_time)) {
                        $last_time = $last_apply_time + $set['limit_day']*60*60*24;
                        if ($last_time > $time) {
                            $info['limit_day'] = true;
                            $info['last_time'] = date('Y-m-d H:i:s', $last_time);
                        }
                    }
                }
            }
			$info['levelinfo'] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_level') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $center['level_id']));
			$this->child_centers = array();
			$centers = $this->getChildCenters($openid);
			$supplier_uids = $this->getChildSupplierUids($openid);
			if (empty($supplier_uids)) {
				$supplier_uids = 0;
			}
			$info['supplier_uids'] = $supplier_uids;
			$supplier_cond = " AND o.supplier_uid in ({$supplier_uids}) ";
			if ($info['supplier_uids'] == 0) {
				$supplier_cond = " AND o.supplier_uid < 0 ";
			}
			$info['ordercount'] = pdo_fetchcolumn("SELECT count(o.id) FROM " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id AND ifnull(r.status,-1)<>-1 " . " WHERE o.uniacid=".$_W['uniacid']." {$supplier_cond} AND o.status>=1 ORDER BY o.createtime DESC,o.status DESC ");

			$info['centercount'] = count($centers);
			$info['merchantcount'] = count($this->getCenterMerchants($center['id']));
			$info['commission_total'] = number_format(pdo_fetchcolumn("SELECT sum(money) FROM " . tablename('sz_yi_merchant_apply') . " WHERE uniacid=:uniacid AND member_id=:member_id AND iscenter=1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member['id'])), 2);

			$info['commission_ok'] = 0;

			$apply_cond = "";
			$now_time = time();
			if (!empty($set['apply_day'])) {
				$apply_day = $now_time - $set['apply_day']*60*60*24;
				$apply_cond .= " AND o.finishtime<{$apply_day} ";
			}else if (!empty($settrade['receive']) && !empty($set['apply_day'])) {
				$apply_day = $now_time - $set['apply_day']*60*60*24;
				$sendreceive = $now_time - $settrade['receive']*60*60*24;
				$apply_cond .= " AND (o.finishtime<{$apply_day} or o.sendtime<{$sendreceive})";
			}else if (!empty($settrade['receive'])){
				$sendreceive = $now_time - $settrade['receive']*60*60*24;
				$apply_cond .= " AND o.sendtime<{$sendreceive}";
			}
			$merchant_orders = pdo_fetchall("SELECT so.*,o.id as oid FROM " . tablename('sz_yi_merchant_order') . " so left join " . tablename('sz_yi_order') . " o on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid=".$_W['uniacid']." {$supplier_cond} {$apply_cond} AND o.center_apply_status=0 AND o.status=3 ORDER BY o.createtime DESC,o.status DESC ");
			if (!empty($merchant_orders)) {
                $info['commission_ok'] = 0;
                foreach ($merchant_orders as $o) {
                    $info['commission_ok'] += $o['money'];
                }
            }
			$info['commission_ok'] = $info['commission_ok']*$info['levelinfo']['commission']/100;
			$info['order_total_price'] = number_format(pdo_fetchcolumn("SELECT sum(og.price) FROM " . tablename('sz_yi_order') . " o " . " left join  ".tablename('sz_yi_order_goods')."  og on o.id=og.orderid left join " . tablename('sz_yi_order_refund') . " r on r.orderid=o.id AND ifnull(r.status,-1)<>-1 " . " WHERE o.uniacid=".$_W['uniacid']." {$supplier_cond} {$apply_cond} ORDER BY o.createtime DESC,o.status DESC "), 2);
			$order_ids = pdo_fetchall("SELECT o.id FROM " . tablename('sz_yi_order') . " o left join " . tablename('sz_yi_merchant_order') . " so on o.id=so.orderid left join " . tablename('sz_yi_order_goods') . " og on og.orderid=o.id WHERE o.uniacid=".$_W['uniacid']." {$supplier_cond} {$apply_cond} AND o.center_apply_status=0 AND o.status=3 ORDER BY o.createtime DESC,o.status DESC ");
			$info['order_ids'] = $order_ids;
			return $info;
		}

		public function getOpenid($center_id){
			global $_W;
			if (empty($center_id)) {
				return;
			}
			$center = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_merchant_center') . " WHERE uniacid=:uniacid AND id=:id", array(':uniacid' => $_W['uniacid'], ':id' => $center_id));
			return $center;
		}

		public function getChildCenters($openid){
			global $_W;
			if (empty($openid)) {
				return;
			}
			$center = $this->isCenter($openid);
			if (empty($center)) {
				return;
			}
			$childs = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchant_center') . " WHERE uniacid=:uniacid AND center_id=:center_id", array(':uniacid' => $_W['uniacid'], ':center_id' => $center['id']));
			if (!empty($childs)) {
				$data = array();
				foreach ($childs as $key => $value) {
					$this->child_centers[$value['id']] = $value;
				}
				foreach ($childs as $val) {
					return $this->getChildCenters($val['openid']);
				}
			} else {
				return $this->child_centers;
			}
		}

		public function getChildSupplierUids($openid){
			global $_W;
			if (empty($openid)) {
				return;
			}
			$member = m('member')->getInfo($openid);
			$center = $this->isCenter($openid);
			$child_centers = $this->getChildCenters($openid);
			if (!empty($child_centers)) {
				$ids = array();
				$center_openids = '';
				$ismerchant = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchants') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}'");
				if (!empty($ismerchant)) {
					$center_openids = "'{$openid}'";
				}
				foreach ($child_centers as $val) {
					$ids[] = $val['id'];
					if (!empty($center_openids)) {
						$center_openids .= ",";
					}
					$center_openids .= "'".$val['openid']."'";
				}
				$merchant_cond = '';
				if (!empty($center_openids)) {
					$merchant_cond .= " OR openid in ({$center_openids})";
				}
				$center_ids = implode(',', $ids);
				if (!empty($center)) {
					$center_ids .= ",".$center['id'];
				}
				
				$supplier_uids = pdo_fetchall("SELECT distinct supplier_uid FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND (center_id in ({$center_ids}) {$merchant_cond})", array(':uniacid' => $_W['uniacid']));
			} else {
				if (!empty($center)) {
					$ismerchant = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_merchants') . " WHERE uniacid={$_W['uniacid']} AND openid='{$openid}'");
					$merchant_cond = '';
					if (!empty($ismerchant)) {
						$merchant_cond = " OR openid='{$openid}'";
					}
					$supplier_uids = pdo_fetchall("SELECT distinct supplier_uid FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND center_id=:center_id {$merchant_cond}", array(':uniacid' => $_W['uniacid'], ':center_id' => $center['id']));
				} else {
					$supplier_uids = pdo_fetchall("SELECT distinct supplier_uid FROM " . tablename('sz_yi_merchants') . " WHERE uniacid=:uniacid AND member_id=:member_id", array(':uniacid' => $_W['uniacid'], ':member_id' => $member['id']));
				}
				
			}
			if (!empty($supplier_uids)) {
				$uids = array();
				foreach ($supplier_uids as $val) {
					$uids[] = $val['supplier_uid'];
				}
				$supplier_uids = implode(',', $uids);
			}
			if (empty($supplier_uids)) {
				$supplier_uids = 0;
			}
			return $supplier_uids;
		}

		//会员id下的所有供应商的supplier_uid
		public function getAllSupplierUids($member_id){
			global $_W, $_GPC;
			$supplier_uids = pdo_fetchall("select distinct supplier_uid from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and member_id={$member_id}");
	        $uids = "";
	        foreach ($supplier_uids as $key => $value) {
	            if ($key == 0) {
	                $uids .= $value['supplier_uid'];
	            } else {
	                $uids .= ','.$value['supplier_uid'];
	            }
	        }
	        if (empty($uids)) {
	            $uids = 0;
	        }
	        return $uids;
		}

		public function isCenter($openid){
			global $_W;
			$center = pdo_fetch("SELECT * FROM " . tablename('sz_yi_merchant_center') . " WHERE uniacid=:uniacid AND openid=:openid", array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			return $center;
		}

		public function getCenterMerchants($center_id){
            global $_W, $_GPC;
            if (empty($center_id)) {
                return '';
            }
            //center_id下的所有招商员
            $merchants = pdo_fetchall("select * from " . tablename('sz_yi_merchants') . " where uniacid={$_W['uniacid']} and center_id=:center_id ORDER BY id DESC", array(':center_id' => $center_id));
            //循环赋予头像等信息
            foreach ($merchants as &$value) {
                $merchants_member = m('member')->getMember($value['openid']);
                $value['username'] = pdo_fetchcolumn("SELECT username FROM " . tablename('sz_yi_perm_user') . " WHERE uniacid=:uniacid AND uid=:uid", array(':uniacid' => $_W['uniacid'], ':uid' => $value['supplier_uid']));
                $value['avatar'] = $merchants_member['avatar'];
                $value['nickname'] = $merchants_member['nickname'];
                $value['realname'] = $merchants_member['realname'];
                $value['mobile'] = $merchants_member['mobile'];
            }
            unset($value);
            return $merchants;
        }

		//基础设置
		public function getSet()
		{
			$set = parent::getSet();
			return $set;
		}

		//发送消息
		function sendMessage($_var_20 = '', $_var_150 = array(), $_var_151 = '')
		{
			global $_W, $_GPC;
			$set = $this->getSet();
			$_var_153 = $set['templateid'];
			$member = m('member')->getMember($_var_20);
			$_var_154 = unserialize($member['noticeset']);
			if (!is_array($_var_154)) {
				$_var_154 = array();
			}
			if ($_var_151 == TM_MERCHANT_APPLY) {
				$_var_155 = $set['merchant_applycontent'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['time']), $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($set['merchant_applytitle']) ? $set['merchant_applytitle'] : '提现申请通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			}
			if ($_var_151 == TM_MERCHANT_PAY) {
				$_var_155 = $set['merchant_finishcontent'];
				$_var_155 = str_replace('[昵称]', $_var_150['nickname'], $_var_155);
				$_var_155 = str_replace('[时间]', date('Y-m-d H:i:s', $_var_150['time']), $_var_155);
				$_var_156 = array('keyword1' => array('value' => !empty($set['merchant_finishtitle']) ? $set['merchant_finishtitle'] : '提现申请完成通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $_var_155, 'color' => '#73a68d'));
				if (!empty($_var_153)) {
					m('message')->sendTplNotice($_var_20, $_var_153, $_var_156);
				} else {
					m('message')->sendCustomNotice($_var_20, $_var_156);
				}
			}
		}

		//权限
		function perms()
		{
			return array('merchant' => array('text' => $this->getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'merchants' => array('text' => '招商员', 'view' => '浏览'))));
		}
	}
}
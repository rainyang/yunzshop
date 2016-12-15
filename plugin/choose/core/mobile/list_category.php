<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$pageid = intval($_GPC['pageid']);
$page = pdo_fetch('SELECT * FROM '.tablename('sz_yi_chooseagent') . ' WHERE uniacid=:uniacid AND id=:id ', array(':id' => $pageid, ':uniacid' => $uniacid));
if (!empty($page['isopenchannel'])) {
	$isopenchannel = $page['isopenchannel'];
} elseif (!empty($page['isstore'])) {
	$isstore = $page['isstore'];
} else {
	if ($page['isopen'] == 1) {
		$sup_uid = $page['uid'];	
	} else {
		$sup_uid = '';
		if ($page['pcate'] != '') {
			$pcate = $page['pcate'];	
			if ($page['ccate'] != '') {
				$ccate = $page['ccate'];
			}
			if ($page['tcate'] != '') {
				$tcate = $page['tcate'];
			}
		}
	}
}
if ($operation == 'category') {
	if (!empty($_GPC['level'])) {
		if (!empty($isopenchannel)) {
			$parent_category = pdo_fetchall("SELECT a.id,a.parentid,a.name,a.level FROM " . tablename('sz_yi_category') . " a LEFT JOIN  " .tablename('sz_yi_goods'). " b ON (a.id = b.pcate )  WHERE a.parentid=0 AND a.uniacid=:uniacid AND b.isopenchannel = :isopenchannel GROUP BY a.id ", array(
				':isopenchannel' => $isopenchannel,
			    ':uniacid' => $_W['uniacid'] 
			));
			$ids = 0;
			if (!empty($parent_category)) {
				$ids = array();
				foreach ($parent_category as $v) {
					$ids[] = $v['id'];
				}
				$ids = implode(',',$ids);
			}
			$sql = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a LEFT JOIN  ' .tablename('sz_yi_goods'). ' b ON a.id = b.ccate WHERE a.parentid in('.$ids.') AND a.uniacid=:uniacid AND  b.uniacid=:uniacid AND b.isopenchannel = :isopenchannel GROUP BY a.id ';
			$children_category = pdo_fetchall($sql, array(
			    ':uniacid' => $_W['uniacid'],
			    ':isopenchannel' => $isopenchannel
			));
			$ids1 = 0;
			if (!empty($children_category)) {
				$ids1 = array();
				foreach ($children_category as $v1) {
					$ids1[] = $v1['id'];
				}
				$ids1 = implode(',', $ids1);
			}
			$sql1 = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a LEFT JOIN  ' .tablename('sz_yi_goods'). ' b ON a.id = b.tcate WHERE a.parentid in('.$ids1.') AND a.uniacid=:uniacid AND  b.uniacid=:uniacid AND b.isopenchannel = :isopenchannel GROUP BY a.id ';
			$third_category = pdo_fetchall($sql1, array(
			    ':uniacid' => $_W['uniacid'],
			    ':isopenchannel' => $isopenchannel
			));

		} elseif (!empty($isstore)) {
			$goodsids = pdo_fetchall("SELECT distinct goodsid FROM ".tablename('sz_yi_store_goods')." WHERE storeid=:storeid and uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':storeid' => $page['storeid']));

			$goodsid = array();

			foreach ($goodsids as $row) {
				
				$goodsid[] = $row['goodsid'];
			}
			$goodsid = implode(',', $goodsid);
			if (!empty($goodsid)) {
                $parent_category = pdo_fetchall('SELECT distinct c.id,c.parentid,c.name,c.level FROM ' . tablename('sz_yi_category') . ' c left join ' .tablename('sz_yi_goods'). ' g on c.id = g.pcate '.' WHERE c.uniacid=:uniacid AND c.parentid=0 and g.id in ('.$goodsid.') GROUP BY c.id', array(':uniacid' => $uniacid));
            }


            foreach ($parent_category as $v) {
                $ids[] = $v['id'];
            }
            if (!empty($ids) && !empty($goodsid)) {
                $sql = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a left join ' .tablename('sz_yi_goods'). ' b on a.id=b.ccate WHERE a.uniacid=:uniacid AND a.parentid in ('.implode(',',$ids).') and b.id in ('.$goodsid.') GROUP BY a.id' ;
                $children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid));
                foreach ($children_category as $v1) {
                    $ids1[] = $v1['id'];
                }
                if (!empty($ids1) && !empty($goodsid)) {
                    $sql1 = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a left join ' .tablename('sz_yi_goods'). ' b ON a.id=b.tcate WHERE a.uniacid=:uniacid AND a.parentid in ('.implode(',',$ids1).') AND b.id IN ('.$goodsid.') GROUP BY a.id' ;
                    $third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid));
                }

            }
			
		} elseif ($page['isopen']==1) {//判断是否开启供应商
		    $parent_category = pdo_fetchall("SELECT a.id,a.parentid,a.name,a.level FROM " . tablename('sz_yi_category') . " a LEFT JOIN  " .tablename('sz_yi_goods'). " b ON (a.id = b.pcate )  WHERE a.parentid=0 AND a.uniacid=:uniacid AND b.isverify=1 AND  b.supplier_uid = :sup_uid GROUP BY a.id ", array(
			    ':uniacid' => $_W['uniacid'],
			    ':sup_uid' => $sup_uid
			));
			foreach ($parent_category as $v) {
				$ids[] = $v['id'];
			}
			if (!empty($ids)) {
				$sql = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a LEFT JOIN  ' .tablename('sz_yi_goods'). ' b ON a.id = b.ccate WHERE a.parentid in('.implode(',',$ids).') AND a.uniacid=:uniacid AND b.isverify=1 AND  b.uniacid=:uniacid AND b.supplier_uid = :sup_uid GROUP BY a.id ';
				$children_category = pdo_fetchall($sql, array(
				    ':uniacid' => $_W['uniacid'],
				    ':sup_uid' => $sup_uid
				));
				
				foreach ($children_category as $v1) {
					$ids1[] = $v1['id'];
				}
				if (!empty($ids1)) {
					$sql1 = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a LEFT JOIN  ' .tablename('sz_yi_goods'). ' b ON a.id = b.tcate WHERE a.parentid in('.implode(',',$ids1).') AND b.isverify=1 AND a.uniacid=:uniacid AND  b.uniacid=:uniacid AND b.supplier_uid = :sup_uid GROUP BY a.id ';
					$third_category = pdo_fetchall($sql1, array(
					    ':uniacid' => $_W['uniacid'],
					    ':sup_uid' => $sup_uid
					));	
				}
				
			}
			
		} else {
			if ($page['allgoods'] == 1) {
				$parent_category = pdo_fetchall('SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid=0 ', array(':uniacid' => $uniacid));
				foreach ($parent_category as $v) {
					$ids[] = $v['id'];
				}
				if (!empty($ids)) {
					$sql = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid in ('.implode(',',$ids).') ' ;
					$children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid));	
					foreach ($children_category as $v1) {
						$ids1[] = $v1['id'];
					}
					if (!empty($ids1)) {
						$sql1 = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid in ('.implode(',',$ids1).') ' ;
						$third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid));
					}
					
				}
				
			} elseif (!empty($page['tcate'])) {
			    $parent_category = pdo_fetchall('SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND id=:id AND parentid=0  ', array(':uniacid' => $uniacid, ':id' => $page['pcate']));
				
				$sql = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') .' WHERE uniacid=:uniacid AND id=:id AND parentid=:parentid ' ;
				$children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':id' => $page['ccate'], ':parentid' => $page['pcate']));

				$sql1 = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') .' WHERE uniacid=:uniacid AND id=:id AND parentid=:parentid ' ;
				$third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid, ':id' => $page['tcate'], ':parentid' => $page['ccate']));
			} elseif (!empty($page['ccate'])) {
			    $parent_category = pdo_fetchall('SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND id=:id AND parentid=0  ',array(':uniacid' => $uniacid, ':id' => $page['pcate']));
				$sql = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND id=:id AND parentid = :parentid ';
				$children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':id' => $page['ccate'], ':parentid' => $page['pcate']));

				$sql1 = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') .' WHERE uniacid=:uniacid  AND parentid=:parentid ' ;
				$third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid, ':parentid' => $page['ccate']));					
			} elseif (!empty($page['pcate'])) {
			    $parent_category = pdo_fetchall('SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND id=:id AND parentid=0  ',array(':uniacid' => $uniacid, ':id' => $page['pcate']));
				$sql = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid = :parentid ' ;
				$children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid,':parentid' => $page['pcate']));
				foreach ($children_category as $v){
					$ids[] = $v['id'];
				}
				$sql1 = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') .' WHERE uniacid=:uniacid  AND parentid in('.implode(',',$ids).') ' ;
				$third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid));		
			} else {
				$parent_category = pdo_fetchall('SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid=0 ', array(':uniacid' => $uniacid));
				foreach ($parent_category as $v) {
					$ids[] = $v['id'];
				}
				if (!empty($ids)) {
					$sql = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid in ('.implode(',',$ids).') ' ;
					$children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid));	
					foreach ($children_category as $v1) {
						$ids1[] = $v1['id'];
					}
					if (!empty($ids1)) {
						$sql1 = 'SELECT id,parentid,name,level FROM ' . tablename('sz_yi_category') . ' WHERE uniacid=:uniacid AND parentid in ('.implode(',',$ids1).') ' ;
						$third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid));
					}
					
				}
					
			}
		}
		foreach ($parent_category as $key => $category) {
			if (!empty($children_category)) {
				foreach ($children_category as $k1 => $v1) {
					if ($category['id'] == $v1['parentid']) {
						$parent_category[$key]['sub'][$k1] = $v1;
						if (!empty($third_category)) {
							foreach ($third_category as $k2 => $v2) {
								if ($v1['id'] == $v2['parentid']) {
									$parent_category[$key]['sub'][$k1]['sub1'][$k2] = $v2;
								}
							}	
						}
					}	
				}	
			}
			
			$args = array(           
            'ccate' => $category['id'],
            'supplier_uid' => $sup_uid,
            'isopenchannel' => $isopenchannel,
            'isverify' => 1
	        );
	        $goods    = m('goods')->getList($args);
	        $conut = 0;
	        foreach ($goods as $key => $good) {
	        	$cartcount = pdo_fetchcolumn('SELECT sum(total) FROM ' . tablename('sz_yi_member_cart') . ' WHERE openid=:openid AND deleted=0 AND uniacid=:uniacid AND goodsid = :goodsid limit 1', array(
		            ':uniacid' => $_W['uniacid'],
		            'goodsid' => $good['id'],
		            ':openid' => $openid
		        ));
		        $conut = $cartcount + $conut;
	        }
	        $parent_category[$key]['count'] = $conut;
		}
		return show_json(1, array('category' => $parent_category,'current_category' => $current_category));
	} else {
		$category = m('shop')->getCategory();
		return show_json(1, array('category' => $category));
	}
} 
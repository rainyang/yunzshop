<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$storeid = intval($_GPC['storeid']);
if ($operation == 'category') {
	if (!empty($_GPC['level'])) {
	    $can_goods = pdo_fetchall(" SELECT * FROM " .tablename('sz_yi_goods'). " WHERE uniacid=:uniacid and status=1 and deleted=0 and isverify=2", array(':uniacid' => $_W['uniacid']));
        //遍历所有核销商品，取指定门店为空或者指定门店中有此门店的商品
        foreach ($can_goods as $row) {
            if (!empty($row['storeids'])) {
                $storeids = explode(',', $row['storeids']);
                foreach ($storeids as $r) {
                    if ($r == $storeid) {
                        $goodsids[] = $row['id'];
                    }
                }
            } else {
                $goodsids[] = $row['id'];
            }
        }
        if (!empty($goodsids)) {
            $parent_category = pdo_fetchall("SELECT a.id,a.parentid,a.name,a.level FROM " . tablename('sz_yi_category') . " a LEFT JOIN  " .tablename('sz_yi_goods'). " b ON (a.id = b.pcate)  WHERE a.parentid=0 AND a.uniacid=:uniacid and b.id IN (".implode(',',$goodsids).") GROUP BY a.id", array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        foreach ($parent_category as $v) {
            $ids[] = $v['id'];
        }
        if (!empty($ids) && !empty($goodsids)) {
            $sql = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a left join ' .tablename('sz_yi_goods'). ' b on a.id=b.ccate WHERE a.uniacid=:uniacid AND a.parentid in ('.implode(',',$ids).') and b.id in ('.implode(',',$goodsids).') GROUP BY a.id' ;
            $children_category = pdo_fetchall($sql, array(':uniacid' => $uniacid));
            foreach ($children_category as $v1) {
                $ids1[] = $v1['id'];
            }
            if (!empty($ids1) && !empty($goodsids)) {
                $sql1 = 'SELECT a.id,a.parentid,a.name,a.level FROM ' . tablename('sz_yi_category') . ' a left join ' .tablename('sz_yi_goods'). ' b ON a.id=b.tcate WHERE a.uniacid=:uniacid AND a.parentid in ('.implode(',',$ids1).') AND b.id IN ('.implode(',',$goodsids).') GROUP BY a.id' ;
                $third_category = pdo_fetchall($sql1, array(':uniacid' => $uniacid));
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
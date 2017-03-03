<?php
global $_W, $_GPC;
$openid   = m('user')->getOpenid();
$centers = $this->model->getChildCenters($openid);
$total = count($centers);
if ($_W['isajax']) {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
    $list = $this->model->page_array($psize, $pindex, $centers, 0);
	foreach ($list as &$row) {

	    $row['member'] = pdo_fetch('SELECT nickname,mobile,avatar FROM ' . tablename('sz_yi_member') . ' WHERE uniacid = :uniacid AND openid = :openid', array(
	        ':uniacid'  => $_W['uniacid'],
            ':openid'   => $row['openid']
        ));

        $row['level'] = pdo_fetch('SELECT ml.* FROM ' . tablename('sz_yi_merchant_level') . ' ml LEFT JOIN ' . tablename('sz_yi_merchant_center') . ' mc ON mc.level_id = ml.id WHERE mc.uniacid = :uniacid AND mc.openid = :openid', array(
            ':uniacid'  => $_W['uniacid'],
            ':openid'   => $row['openid']
        ));
	}
	unset($row);
return show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('center_team');

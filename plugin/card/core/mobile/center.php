<?php
//芸众商城 QQ:913768135
global $_W, $_GPC;
$set = $this->model->getSet();
$openid = m('user')->getOpenid();
$used = intval($_GPC['used']);
$past = intval($_GPC['past']);
if ($_W['isajax']) {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $time = time();
    $sql = 'SELECT * FROM ' . tablename('sz_yi_card_data');
    $sql .= ' WHERE openid=:openid AND uniacid=:uniacid ';
    $sql .= ' order by bindtime desc  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall($sql, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));
    foreach ($list as &$row) {
        $isoverdue = $this->model->checkValidity($row['id']);
        if ($isoverdue == 0) {
            if ($row['isday'] == 1) {
                $row['timestr'] = date('Y-m-d H:i:s',$row['bindtime']) . "至" . date('Y-m-d H:i:s', ($row['bindtime']+$row['validity_period']));
            } else if ($row['isday'] == 2) {
                $row['timestr'] = date('Y-m-d H:i:s',$row['timestart']) . "至" . date('Y-m-d H:i:s', $row['timeend']);
            }
        } else {
            $row['timestr'] = "已过期";
        }
        //余额等于0在页面判断
    }
    unset($row);
    return show_json(1, array('list' => $list, 'pagesize' => $psize));
}
include $this->template('center');

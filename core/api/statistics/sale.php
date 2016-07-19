<?php
/**
 * 管理后台APP API销售数据统计接口
 *
 * PHP version 5.6.15
 *
 * @package   统计模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
//$api->validate('username','password');
//$_YZ->ca('statistics.view.sale');
$sale['all'] = getSaleData('sum(price)',array(
    ':uniacid' => $_W['uniacid'])
);
$sale['month'] = getSaleData('sum(price)', array(
    ':uniacid' => $_W['uniacid'],
    ':starttime' => strtotime("-1 month"),
    ':endtime' => time()
));
$count['today_order'] = getSaleData('count(*)', array(
    ':uniacid' => $_W['uniacid'],
    ':starttime' => time()
));
$count['new_member'] = '0';
$count['week_order'] = getSaleData('count(*)', array(
    ':uniacid' => $_W['uniacid'],
    ':starttime' => strtotime("-1 week"),
    ':endtime' => time()
));

function getSaleData($countfield, $map = [])
{
    $condition = '1';
    if(isset($map[':uniacid'])){
        $condition .= ' AND uniacid=:uniacid';
    }
    if(isset($map[':starttime'])){
        $condition .= ' AND createtime >=:starttime';
    }
    if(isset($map[':endtime'])){
        $condition .= ' AND createtime <=:endtime';
    }
    return pdo_fetchcolumn("SELECT ifnull({$countfield},0) as cnt FROM " . tablename('sz_yi_order') . " WHERE {$condition} AND status>=1 ", $map);
}

$rse = compact('sale', 'count');
dump($rse);
$_YZ->returnSuccess($rse);
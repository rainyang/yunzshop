<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$this->model->autogoods();
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
if(empty($openid)){
    $openid = m('user')->isLogin();
}
$member    = m('member')->getMember($openid);
$uniacid    = $_W['uniacid'];
$set = $this->model->getSet();
$commission = p('commission');
$shopset   = m('common')->getSysset('shop');
$plugin_yunbi = p('yunbi');
if ($plugin_yunbi) {
    $yunbi_set = $plugin_yunbi->getSet();
}
if ($commission) {
    $shopid = intval($_GPC['shopid']);
    if (!empty($shopid)) {
        $myshop = set_medias($commission->getShop($shopid), array(
            'img',
            'logo'
        ));
    }
}
$current_category = false;

$args = array(
    'pagesize' => 28,
    'page' => $_GPC['page'],
    'isnew' => $_GPC['isnew'],
    'ishot' => $_GPC['ishot'],
    'isrecommand' => $_GPC['isrecommand'],
    'isdiscount' => $_GPC['isdiscount'],
    'istime' => $_GPC['istime'],
    'keywords' => $_GPC['keywords'],
    'pcate' => intval($_GPC['pcate']),
    'ccate' => intval($_GPC['ccate']),
    'tcate' => intval($_GPC['tcate']),
    'pcate1' => intval($_GPC['pcate1']),
    'ccate1' => intval($_GPC['ccate1']),
    'tcate1' => intval($_GPC['tcate1']),
    'order' => $_GPC['order'],
    'by' => $_GPC['by'],
    'plugin' => 'fund'
);

//$args = icheck_gpc($args);
if (!empty($myshop['selectgoods']) && !empty($myshop['goodsids'])) {
    $args['ids'] = $myshop['goodsids'];
}

//会员权限控制商品显示
$levelid = intval($member['level']);
$groupid = intval($member['groupid']);
$levelCondition = " and ( ifnull(showlevels,'')='' or FIND_IN_SET( {$levelid},showlevels)<>0 ) ";
$levelCondition .= " and ( ifnull(showgroups,'')='' or FIND_IN_SET( {$groupid},showgroups)<>0 ) ";
$condition = ' and `uniacid` = :uniacid AND `deleted` = 0 and status=1' . $levelCondition;
$params    = array(
     ':uniacid' => $_W['uniacid']
);
if (!empty($args['ids'])) {
    $condition .= " and id in ( " .  $args['ids'] . ")";
}
$isnew = !empty($args['isnew']) ? 1 : 0;
if (!empty($isnew)) {
    $condition .= " and isnew=1";
}
$ishot = !empty($args['ishot']) ? 1 : 0;
if (!empty($ishot)) {
    $condition .= " and ishot=1";
}
$isrecommand = !empty($args['isrecommand']) ? 1 : 0;
if (!empty($isrecommand)) {
    $condition .= " and isrecommand=1";
}
$isdiscount = !empty($args['isdiscount']) ? 1 : 0;
if (!empty($isdiscount)) {
    $condition .= " and isdiscount=1";
}
$istime = !empty($args['istime']) ? 1 : 0;
if (!empty($istime)) {
    $condition .= " and istime=1 and " . time() . ">=timestart and " . time() . "<=timeend";
}
$keywords = !empty($args['keywords']) ? $args['keywords'] : '';
if (!empty($keywords)) {
    $condition .= ' AND `title` LIKE :title';
    $params[':title'] = '%' . trim($keywords) . '%';
}
$tcate = intval($args['tcate']);
if (!empty($tcate)) {
    $condition .= " AND ( `tcate` = :tcate or  FIND_IN_SET({$tcate},tcates)<>0 )";
    $params[':tcate'] = intval($tcate);
} else {
    $ccate = intval($args['ccate']);
    if (!empty($ccate)) {
        $condition .= " AND ( `ccate` = :ccate or  FIND_IN_SET({$ccate},ccates)<>0 )";
        $params[':ccate'] = intval($ccate);
    } else {
        $pcate = intval($args['pcate']);
        if (!empty($pcate)) {
            $condition .= " AND ( `pcate` = :pcate or  FIND_IN_SET({$pcate},pcates)<>0 )";
            $params[':pcate'] = intval($pcate);
        }
    }
}

$minprice = intval($_GPC['minprice']);
$maxprice = intval($_GPC['maxprice']);
if (!empty($maxprice)) {
    $condition .= ' AND `marketprice` <= :maxprice';
    $params[':maxprice'] = $maxprice;
}

if (!empty($minprice)) {
    $condition .= ' AND `marketprice` >= :minprice';
    $params[':minprice'] = $minprice;
}
$condition .= " AND `plugin` = 'fund'";

$total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_goods') . " where 1 {$condition}", $params);

$pindex = max(1, intval($_GPC['page']));
$pager = pagination($total, $pindex, $args['pagesize']);

if (!empty($maxprice) || !empty($minprice)) {
    $goods = set_medias(pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods') . " where 1 {$condition}", $params), 'thumb');
} else {
    $goods    = m('goods')->getList($args);
}
foreach ($goods as $key => &$value) {
    $get_fund_data = pdo_fetch("SELECT * FROM " . tablename('sz_yi_fund_goods') . " WHERE goodsid = :id", array(
        ':id' => $value['id']
    ));
    $value['allprice'] = number_format($get_fund_data['allprice'], 2);
    $value['desc'] = $get_fund_data['desc'];
    $yetprice = pdo_fetchcolumn("select sum(og.price) as yetprice from ". tablename('sz_yi_order_goods') ." og left join " . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status > 0 and og.goodsid=".$value['id']);
    //$yetprice += $value['marketprice']*$value['sales'];
    $value['yetprice'] = number_format($yetprice, 2);
    $value['people'] = pdo_fetchcolumn("select count(o.id) from ". tablename('sz_yi_order_goods') ." og left join " . tablename('sz_yi_order') . " o on og.orderid=o.id  where o.status > 0 and og.goodsid=".$value['id']);
    $value['percentage'] = !empty($yetprice) && !empty($get_fund_data['allprice']) ? intval($yetprice/$get_fund_data['allprice']*100) : 0;
    $value['sday'] = $value['timeend'] > time() ? $this->model->check_time($value['timeend']) : "0秒";
}
unset($value);

$category = false;
if ($_W['isajax']) {
    return show_json(1, array(
        'goods' => $goods,
        'pagesize' => $args['pagesize'],
        'category' => $category,
        'current_category' => $current_category
    ));
}

$_W['shopshare'] = array(
    'title' => !empty($set['sharetitle']) ? $set['sharetitle'] : "众筹列表",
    'imgUrl' => !empty($set['shareicon']) ? tomedia($set['shareicon']) : tomedia($shopset['logo']),
    'desc' => !empty($set['sharedesc']) ? $set['sharedesc'] : '',
    'link' => $this->createPluginMobileUrl('fund/index', array(
        'mid' => $mid
    ))
);
$com             = p('commission');
if ($com) {
    $cset = $com->getSet();
    if (!empty($cset)) {
        if ($member['isagent'] == 1 && $member['status'] == 1) {
            $_W['shopshare']['link'] = $this->createPluginMobileUrl('fund/index', array(
                'id' => $goods['id'],
                'mid' => $member['id']
            ));
            if (empty($cset['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) {
                $trigger = true;
            }
        } else if (!empty($_GPC['mid'])) {
            $_W['shopshare']['link'] = $this->createPluginMobileUrl('fund/index', array(
                'id' => $goods['id'],
                'mid' => $_GPC['mid']
            ));
        }
    }
}
include $this->template('list');

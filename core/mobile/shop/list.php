<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$operation  = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid     = m('user')->getOpenid();
if(empty($openid)){
    $openid = m('user')->isLogin();
}
$member    = m('member')->getMember($openid);
$uniacid    = $_W['uniacid'];
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
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
if (!empty($_GPC['tcate'])) {
    $current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id 
        and uniacid=:uniacid order by displayorder DESC', array(
        ':id' => intval($_GPC['tcate']),
        ':uniacid' => $_W['uniacid']
    ));
} elseif (!empty($_GPC['ccate'])) {
    $current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id 
        and uniacid=:uniacid order by displayorder DESC', array(
        ':id' => intval($_GPC['ccate']),
        ':uniacid' => $_W['uniacid']
    ));
} elseif (!empty($_GPC['pcate'])) {
    $current_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id 
        and uniacid=:uniacid order by displayorder DESC', array(
        ':id' => intval($_GPC['pcate']),
        ':uniacid' => $_W['uniacid']
    ));
}

$parent_category = pdo_fetch('select id,parentid,name,level from ' . tablename('sz_yi_category') . ' where id=:id 
    and uniacid=:uniacid limit 1', array(
    ':id' => $current_category['parentid'],
    ':uniacid' => $_W['uniacid']
));
$args = array(
    'pagesize' => $_GPC['pagesize'] ?: 28,
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
    'goodsid' => $_GPC['goodsid']
);
if ($args['pcate']) {
    $pcatename = pdo_fetch(" select id,name from ".tablename('sz_yi_category')." where id =".$args['pcate']." and uniacid=".$uniacid);
}
if ($args['ccate']) {
    $ccatename = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category')." where id =".$args['ccate']." and uniacid=".$uniacid);
    $pcatename = pdo_fetch(" select id,name from ".tablename('sz_yi_category')." where id =".$ccatename['parentid']." and uniacid=".$uniacid);
}
if ($args['tcate']) {
    $tcatename = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category')." where id =".$args['tcate']." and uniacid=".$uniacid);
    $ccatename = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category')." where id =".$tcatename['parentid']." and uniacid=".$uniacid);
    $pcatename = pdo_fetch(" select id,name from ".tablename('sz_yi_category')." where id =".$ccatename['parentid']." and uniacid=".$uniacid);
}

if ($args['pcate1']) {
    $pcate1name = pdo_fetch(" select id,name from ".tablename('sz_yi_category2')." where id =".$args['pcate1']." and uniacid=".$uniacid);
}
if ($args['ccate1']) {
    $ccate1name = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category2')." where id =".$args['ccate1']." and uniacid=".$uniacid);
    $pcate1name = pdo_fetch(" select id,name from ".tablename('sz_yi_category2')." where id =".$ccate1name['parentid']." and uniacid=".$uniacid);
}
if ($args['tcate1']) {
    $tcate1name = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category2')." where id =".$args['tcate1']." and uniacid=".$uniacid);
    $ccate1name = pdo_fetch(" select id,name,parentid from ".tablename('sz_yi_category2')." where id =".$tcate1name['parentid']." and uniacid=".$uniacid);
    $pcate1name = pdo_fetch(" select id,name from ".tablename('sz_yi_category2')." where id =".$ccate1name['parentid']." and uniacid=".$uniacid);
}
$category2 = m('shop')->getCategory2();
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
$goodsid = !empty($args['$goodsid']) ? $args['$goodsid'] : 0;
if (!empty($goodsid)) {
    $condition .= " and id < :goodsid";
    $params[':goodsid'] = intval($goodsid);
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
$tcate1 = intval($args['tcate1']);
if (!empty($tcate1)) {
    $condition .= " AND ( `tcate1` = :tcate1 or  FIND_IN_SET({$tcate1},tcates)<>0 )";
    $params[':tcate1'] = intval($tcate1);
} else {
    $ccate1 = intval($args['ccate1']);
    if (!empty($ccate1)) {
        $condition .= " AND ( `ccate1` = :ccate1 or  FIND_IN_SET({$ccate1},ccates)<>0 )";
        $params[':ccate1'] = intval($ccate1);
    } else {
        $pcate1 = intval($args['pcate1']);
        if (!empty($pcate1)) {
            $condition .= " AND ( `pcate1` = :pcate1 or  FIND_IN_SET({$pcate1},pcates)<>0 )";
            $params[':pcate1'] = intval($pcate1);
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
$condition .= " AND `plugin` = ''";
$total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('sz_yi_goods') . " where 1 {$condition}", $params);

$pindex = max(1, intval($_GPC['page']));
$pager = pagination($total, $pindex, $args['pagesize']);

if (!empty($maxprice) || !empty($minprice)) {
    $goods = set_medias(pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods') . " where 1 {$condition}", $params), 'thumb');
} else {
    $goods    = m('goods')->getList($args);
}

if (intval($shopset['catlevel']) == 3) {
    if ($args['ccate']) {
        $third_category = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_category')." where parentid=:ccate and uniacid=:uniacid", array(':ccate' => $args['ccate'] , ':uniacid' => $_W['uniacid'])), 'advimg,thumb');
    }
}

if ($args['tcate']) {
     $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where tcate=:tcate and pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0 and status = 1 {$levelCondition} order by sales desc limit 7", array(':uniacid' => $uniacid , ':tcate' => $args['tcate'] , ':pcate' => $args['pcate'] , ':ccate' => $args['ccate'])), 'thumb');
} elseif ($args['ccate']) {
    $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where pcate=:pcate and ccate=:ccate and uniacid=:uniacid and deleted = 0 and status = 1 {$levelCondition} order by sales desc limit 7", array(':uniacid' => $uniacid, ':pcate' => $args['pcate'], ':ccate' => $args['ccate'])), 'thumb');
} elseif ($args['pcate']) {
    $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where pcate=:pcate and uniacid=:uniacid and deleted = 0 and status = 1 {$levelCondition} order by sales desc limit 7", array(':uniacid' => $uniacid , ':pcate' => $args['pcate'] )), 'thumb');
} else {
    $ishot = set_medias(pdo_fetchall("select * from ".tablename('sz_yi_goods')." where uniacid=:uniacid and deleted = 0 and status = 1 {$levelCondition} order by sales desc limit 7", array(':uniacid' => $uniacid )), 'thumb');
}

$category = false;
if (intval($_GPC['page']) <= 1) {
    if (!empty($_GPC['tcate'])) {
        $parent_category1 = pdo_fetch('select id,parentid,name,level,thumb from ' . tablename('sz_yi_category') . ' 
            where id=:id and uniacid=:uniacid limit 1', array(
            ':id' => $parent_category['parentid'],
            ':uniacid' => $_W['uniacid']
        ));
        $category         = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' 
            where parentid=:parentid 
            and enabled=1 and uniacid=:uniacid order by level asc, isrecommand desc, displayorder DESC', array(
            ':parentid' => $parent_category['id'],
            ':uniacid' => $_W['uniacid']
        ));
        $category         = array_merge(array(
            array(
                'id' => 0,
                'name' => '全部分类',
                'level' => 0
            ),
            $parent_category1,
            $parent_category
        ), $category);
    } elseif (!empty($_GPC['ccate'])) {
        if (intval($set['catlevel']) == 3) {
            $category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where 
                (parentid=:parentid or id=:parentid) and enabled=1  and uniacid=:uniacid 
                order by level asc, isrecommand desc, displayorder DESC', array(
                ':parentid' => intval($_GPC['ccate']),
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            $category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' where 
                parentid=:parentid and enabled=1 and uniacid=:uniacid order by level asc, 
                isrecommand desc, displayorder DESC', array(
                ':parentid' => $parent_category['id'],
                ':uniacid' => $_W['uniacid']
            ));
        }
        $category = array_merge(array(
            array(
                'id' => 0,
                'name' => '全部分类',
                'level' => 0
            ),
            $parent_category
        ), $category);
    } elseif (!empty($_GPC['pcate'])) {
        $category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' 
            where (parentid=:parentid or id=:parentid) and enabled=1 and uniacid=:uniacid order by level asc, 
            isrecommand desc, displayorder DESC', array(
            ':parentid' => intval($_GPC['pcate']),
            ':uniacid' => $_W['uniacid']
        ));
        $category = array_merge(array(
            array(
                'id' => 0,
                'name' => '全部分类',
                'level' => 0
            )
        ), $category);
    } else {
        $category = pdo_fetchall('select id,name,level,thumb from ' . tablename('sz_yi_category') . ' 
            where parentid=0 and enabled=1 and uniacid=:uniacid order by displayorder DESC', array(
            ':uniacid' => $_W['uniacid']
        ));
    }
    foreach ($category as &$c) {
        $c['thumb'] = tomedia($c['thumb']);
        if ($current_category['id'] == $c['id']) {
            $c['on'] = true;
        }
    }
    unset($c);
}

if ($_W['isajax']) {
    return show_json(1, array(
        'goods' => $goods,
        'pagesize' => $args['pagesize'],
        'category' => $category,
        'current_category' => $current_category,
        'page_total' => ceil($total / $args['pagesize']) ?: 1
    ));
}
include $this->template('shop/list');

<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid = $_W['uniacid'];

$set = m('common')->getSysset(array('trade','shop'));
$shop_set = m('common')->getSysset(array('shop'));
$shopset   = m('common')->getSysset('shop');
$yunbiset = m('plugin')->getpluginSet('yunbi');
$yunbi_title = $yunbiset['yunbi_title']?$yunbiset['yunbi_title']:"云币";
$level = array('levelname' => empty($this->yzShopSet['levelname']) ? '普通会员' : $this->yzShopSet['levelname']);

$referrer = array();
if($shop_set['shop']['isreferrer'] ){
    if($member['agentid']>0){
        $referrer = pdo_fetch("select * from " . tablename("sz_yi_member") . " where uniacid=".$_W['uniacid']." and id = '".$member['agentid']."' ");
        $nickname = $referrer['nickname'] ? $referrer['nickname'] :  $referrer['realname'];
        $nickname = $nickname ? $nickname :  $referrer['mobile'];
        $referrer['realname'] = mb_substr($nickname, 0, 6, 'utf-8');
    }else
    {
        $referrer['realname'] = "总店";
    }
}


if ($operation == 'display') {

    $cumulative_total   = p('yunbi') -> MoneySumTotal(" and returntype in ('1','2','4','7','9','10','12','13') ",$member['id']);
    $deduct_return      = p('yunbi') -> MoneySumTotal(" and returntype in ('3','5') ",$member['id']);

    $remove_total = pdo_fetchcolumn("select count(1) as money from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid and returntype = '6' and money <> 0 and mid = :mid ", array(
        ':uniacid' => $_W['uniacid'],
        ':mid' => $member['id']
    ));

    $shopping      = p('yunbi') -> MoneySumTotal(" and returntype = '1' ",$member['id']);
    $offline       = p('yunbi') -> MoneySumTotal(" and returntype = '2' ",$member['id']);
    $deduct        = p('yunbi') -> MoneySumTotal(" and returntype = '3' ",$member['id']);
    $return_deduct = p('yunbi') -> MoneySumTotal(" and returntype = '4' ",$member['id']);
    $return        = p('yunbi') -> MoneySumTotal(" and returntype = '5' ",$member['id']);
    $remove        = p('yunbi') -> MoneySumTotal(" and returntype = '6' ",$member['id']);

    $into           = p('yunbi') -> MoneySumTotal(" and returntype = '10' ",$member['id']);

    $recharge      = p('yunbi') -> MoneySumTotal(" and returntype = '7' ",$member['id']);
    $presented     = p('yunbi') -> MoneySumTotal(" and returntype = '8' ",$member['id']);
    $acquisition   = p('yunbi') -> MoneySumTotal(" and returntype = '9' ",$member['id']);
    $declaration   = p('yunbi') -> MoneySumTotal(" and returntype = '13' ",$member['id']);


} elseif ( $_W['isajax'] && $operation == 'log') {
   $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $type = $_GPC['type']=='8'?"8,9":$_GPC['type'];
    $total = pdo_fetchcolumn("select count(yl.id) from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid ) 
        where yl.uniacid = :uniacid and yl.returntype in ( ".$type." ) and yl.money <> 0 and m.id = :mid ", array(
        ':uniacid' => $_W['uniacid'],
        ':mid' => $member['id']
    ));

    $list = pdo_fetchall("select yl.*, m.id as mid, m.realname , m.mobile  from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid ) 
        where yl.uniacid = :uniacid and yl.returntype in ( ".$type." ) and yl.money <> 0 and m.id = :mid order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
        array(
            ':uniacid' => $_W['uniacid'],
            ':mid' => $member['id']
        ));
    foreach ($list as &$row) {
        $row['create_time'] = date("Y-m-d H:i:s", $row['create_time']);
    }
    unset($row);
    return show_json(1, array(
        'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
        'type' => $_GPC['type']
    ));
}

    
 

include $this->template('yunbi_log');

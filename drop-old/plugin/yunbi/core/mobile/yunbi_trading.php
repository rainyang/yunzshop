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

    $trading_1 = p('yunbi')->CountTotal(" AND returntype = '11' AND status >= '0' ");
    $trading_2 = p('yunbi')->CountTotal(" AND returntype = '11' AND status >= '0' AND (mid = '".$member['id']."' or buy_mid = '".$member['id']."') ");
    $trading_3 = p('yunbi')->CountTotal(" AND returntype = '11' AND status = '0' ");
    $trading_4 = p('yunbi')->CountTotal(" AND returntype = '11' AND (mid = '".$member['id']."' or buy_mid = '".$member['id']."') AND status > '1' ");
    $_GPC['type'] = 1;

} elseif ( $operation == 'trading') {
    if ($_W['isajax']) {
        $money = floatval($_GPC['money']);
        if ($money > $member['virtual_currency']) {
            return show_json(0,'出让'.$yunbi_title.'不正确');
        }
        p('yunbi')->setVirtualCurrency($member['openid'],-$money);
        $data_log = array(
            'id'            => $member['id'],
            'openid'        => $member['openid'],
            'credittype'    => 'virtual_currency',
            'money'         => $money,
            'status'        => '0',
            'remark'        => "出让".$yunbi_title
        );
        p('yunbi')->addYunbiLog($uniacid,$data_log,'11');

        return show_json(1);
    }
    include $this->template('trading');
    exit;

} elseif ( $_W['isajax'] && $operation == 'log') {
   $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    switch ($_GPC['type']) {
        case '2':
            $conditions = " AND (m.id = '".$member['id']."' or yl.buy_mid = '".$member['id']."') AND yl.status >= '0' ";
            break;
        case '3':
            $conditions = " AND yl.status = '0' ";
            break;
        case '4':
            $conditions = " AND (m.id = '".$member['id']."' or yl.buy_mid = '".$member['id']."') AND yl.status > '1'";
            break;
        default:
            $conditions = " AND yl.status >= '0' ";
            break;
    }
    $total = pdo_fetchcolumn("select count(yl.id) from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid ) 
        where yl.uniacid = :uniacid AND yl.returntype = :returntype and yl.money <> 0 ".$conditions, array(
        ':uniacid' => $_W['uniacid'],
        'returntype' => '11'
    ));

    $list = pdo_fetchall("select yl.*, m.id as mid, m.realname , m.mobile  from" . tablename('sz_yi_yunbi_log') . " yl
        left join " . tablename('sz_yi_member') . " m on( yl.openid=m.openid ) 
        where yl.uniacid = :uniacid AND yl.returntype = :returntype and yl.money <> 0 ".$conditions." order by create_time desc LIMIT " . ($pindex - 1) * $psize . "," . $psize,
        array(
            ':uniacid' => $_W['uniacid'],
            'returntype' => '11'
        ));
    foreach ($list as &$row) {
        $row['create_time'] = date("Y-m-d H:i", $row['create_time']);
        $row['price'] = $row['money'] * $yunbiset['trading_money'] / $yunbiset['credit'];
        if ($row['mid'] == $member['id']) {
            if ($row['status'] == '0') {
                $row['iscancel'] = '1';
            }elseif ($row['status'] == '1') {
                $row['status_text'] = '我购买的';
            }elseif ($row['status'] == '2') {
                $row['status_text'] = '我出售';
            }elseif ($row['status'] == '3') {
                $row['status_text'] = '公司回购';
            }

        }else
        {
            if ($row['status'] == '0') {
                $row['istrading'] = '1';
                $row['buy_link'] = $this->createPluginMobileUrl('yunbi/yunbi_trading',array('op'=>'buy','id'=>$row['id']));
            }
            if ($row['status'] == '2' && $row['buy_mid'] == $member['id']) {
                $row['status_text'] = '我购买的';
            }
        }

    }
    unset($row);
    return show_json(1, array(
        'total' => $total,
        'list' => $list,
        'pagesize' => $psize,
        'type' => $_GPC['type']
    ));
} elseif ( $_W['isajax'] && $operation == 'cancel') {
    //echo "<pre>";print_r('撤回');exit;
    $id = (int)$_GPC['id'];
    $info = pdo_fetch("select * from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid and id = :id and money <> 0 and status = '0'", array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $id
                ));
    if (!$info) {
        return show_json(0,"撤回失败,信息不存在！");
    }
    if ($info['mid'] != $member['id']) {
        return show_json(0,"撤回失败,信息不正确！");
    }
    $sql = "update ".tablename('sz_yi_yunbi_log')."  set status = -1 where `uniacid` =  " . $uniacid ." AND id = ".$id;
    pdo_fetchall($sql);
    p('yunbi')->setVirtualCurrency($member['openid'],$info['money']);
    return show_json(1);

} elseif ( $operation == 'buy') {
    $payset = m('common')->getSysset(array('pay'));
    if ($_W['isajax']) {
        $id = (int)$_GPC['id'];
        $trading = pdo_fetch("select * from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid and id = :id and money <> 0 and status = '0'", array(
                    ':uniacid' => $_W['uniacid'],
                    ':id' => $id
                ));
        if (!$trading) {
            return show_json(0,"购买失败,信息不存在！");
        }
        $trading['create_time'] = date("Y-m-d H:i:s", $trading['create_time']);
        $trading['price'] = $trading['money'] * $yunbiset['trading_money'] / $yunbiset['credit'];
        if ($trading['price'] <= 0) {
            return show_json(0,"购买失败,支付金额不能小于0！");
        }


        $credit        = array(
            'success' => false
        );
        if (isset($payset['pay']) && $payset['pay']['credit'] == 1) {
                $credit = array(
                    'success' => true,
                    'current' => m('member')->getCredit($openid, 'credit2')
                );
        }
        $returnurl = urlencode($this->createPluginMobileUrl('yunbi/yunbi_trading', array(
            'id' => $id,
            'op' => 'buy'
        )));
        return show_json(1, array(
            'credit' => $credit,
            'trading' => $trading,
            'returnurl' => $returnurl
        ));
    }
    include $this->template('pay');
    exit;
} elseif ( $_W['isajax'] && $operation == 'complete' ) {
    $payset = m('common')->getSysset(array('pay'));
    $id = (int)$_GPC['id'];
    $trading = pdo_fetch("select * from" . tablename('sz_yi_yunbi_log') . " where uniacid = :uniacid and id = :id and money <> 0 and status = '0'", array(
                ':uniacid' => $_W['uniacid'],
                ':id' => $id
            ));
    if (!$trading) {
        return show_json(0,"支付失败,信息不存在！");
    }
    $trading['create_time'] = date("Y-m-d H:i:s", $trading['create_time']);
    $trading['price'] = $trading['money'] * $yunbiset['trading_money'] / $yunbiset['credit'];
    $poundage = $trading['price'] * $yunbiset['poundage'] / 100;

    if ($trading['price'] <= 0) {
        return show_json(0,"支付失败,支付金额不能小于0！");
    }
    $type = $_GPC['type'];
    if (!in_array($type, array(
        'weixin',
        'alipay',
        'credit',
        'cash',
        'storecash'
    ))) {
        return show_json(0, '未找到支付方式');
    }
    if ($member['credit2'] < $trading['price']) {
        return show_json(0, '余额不足，请充值后在试！');
    }
    if ($type == 'credit') {
        if (!$payset['pay']['credit']) {
            return show_json(0, '余额支付未开启！');
        }
        $credits = m('member')->getCredit($openid, 'credit2');
        if ($credits < $trading['price']) {
            return show_json(0, "余额不足,请充值");
        }
        $fee    = floatval($trading['price']);

        $result = m('member')->setCredit($openid, 'credit2', -$fee, array(
            $_W['member']['uid'],
            '消费-购买'.$yunbi_title.'-余额支付:' . $fee
        ));
        if (is_error($result)) {
            return show_json(0, $result['message']);
        }
        p('yunbi')->setVirtualCurrency($openid,$trading['money']);
        $data_log = array(
            'id'            => $member['id'],
            'openid'        => $openid,
            'credittype'    => 'virtual_currency',
            'money'         => $trading['money'],
            'status'        => '1',
            'remark'        => "购买".$yunbi_title
        );
        p('yunbi')->addYunbiLog($uniacid,$data_log,'12');
        // 购买人推送信息
        $sql = "update ".tablename('sz_yi_yunbi_log')."  set status = 2 ,buy_mid = ".$member['id']." where `uniacid` =  " . $uniacid ." AND status = '0' AND id = ".$id;
        pdo_fetchall($sql);
        $result = m('member')->setCredit($trading['openid'], 'credit2', $fee - $poundage, array(
            $_W['member']['uid'],
            '出让'.$yunbi_title.'-余额获得:' . $fee - $poundage . '手续费:' .$poundage
        ));
        // 出售人推送信息
        return show_json(1);
    }
    return show_json(0,'支付失败，请重试！');
}

    
 

include $this->template('yunbi_trading');

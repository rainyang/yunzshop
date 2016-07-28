<?php
global $_W, $_GPC;
$operation   = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;

    $params    = array();
    $condition = '';
    if (!empty($_GPC['mid'])) {
        $condition .= ' and id=:mid';
        $condition1 .= ' and r.mid=:mid';
        $params[':mid'] = intval($_GPC['mid']);
    }
    if (!empty($_GPC['realname'])) {
        $_GPC['realname'] = trim($_GPC['realname']);
        $condition .= ' and ( realname like :realname or nickname like :realname or mobile like :realname)';
        $condition1 .= ' and ( m.realname like :realname or m.nickname like :realname or m.mobile like :realname)';
        $params[':realname'] = "%{$_GPC['realname']}%";  
    }

    $total = pdo_fetchall("select * from" . tablename('sz_yi_return') . " r 
        left join " . tablename('sz_yi_member') . " m on (r.mid = m.id ) where r.uniacid =" . $_W['uniacid'] . "    {$condition1} group by mid" ,$params);
    $total = count($total);
    $list_group=pdo_fetchall(" select * from " .tablename('sz_yi_return'). " r 
        left join " . tablename('sz_yi_member') . " m on (r.mid = m.id ) where r.uniacid= " .$_W['uniacid']. "  {$condition1}  group by mid  limit " . ($pindex - 1) * $psize . ',' . $psize ,$params);
    $pager           = pagination($total, $pindex, $psize);
    foreach( $list_group as $row1){
        $infomation=pdo_fetch("select * from " .tablename('sz_yi_member'). "  where uniacid=" .$_W['uniacid']. " and id=" .$row1['mid'] );
       $list_group1[$row1['mid']]= pdo_fetchall(" select * from " .tablename('sz_yi_return'). " where uniacid=" .$_W['uniacid']. " and  mid = ".$row1['mid']);
       foreach($list_group1[$row1['mid']] as  $row2){
           if($row2['delete'] < '1')
           {
            $asd[$row1['mid']]['money1']+=$row2['money'];
           }else
           {
            $asd[$row1['mid']]['money1']+=$row2['return_money'];
           }         
            $asd[$row1['mid']]['return_money1']+=$row2['return_money'];
            $asd[$row1['mid']]['status']=$row2['status'];
            $asd[$row1['mid']]['delete']=$row2['delete'];
       }
       $asd[$row1['mid']]['realname']=$infomation['realname'];
       $asd[$row1['mid']]['avatar']=$infomation['avatar'];
       $asd[$row1['mid']]['mid']=$infomation['id'];

       $asd[$row1['mid']]['unreturnmoney']=$asd[$row1['mid']]['money1'] - $asd[$row1['mid']]['return_money1'];   
    }
    unset($row);

}elseif ($operation == 'detail') {
    $pindex    = max(1, intval($_GPC['page']));
    $psize     = 20;
    $params = " and r.delete = '0' ";
    $total = pdo_fetchall("select * from" . tablename('sz_yi_return') . " r 
        left join " . tablename('sz_yi_member') . " m on (r.mid = m.id ) where r.uniacid =" . $_W['uniacid'] ."  and r.mid = ".$_GPC['mid'].$params);
    $total = count($total);
    $list_group=pdo_fetchall(" select r.id, r.uniacid, r.mid, r.money, r.return_money, r.create_time, r.status, m.realname, m.nickname, m.avatar from " .tablename('sz_yi_return'). " r 
        left join " . tablename('sz_yi_member') . " m on (r.mid = m.id ) where r.uniacid= " .$_W['uniacid'] ."  and r.mid = ".$_GPC['mid'].$params."limit " . ($pindex - 1) * $psize . ',' . $psize );
    foreach ($list_group as $key => $value) {
        $list_group[$key]['unreturnmoney'] = $value['money'] - $value['return_money'];
        $list_group[$key]['create_time'] = date('Y-m-d H:i',$value['create_time']);
    }
    $pager           = pagination($total, $pindex, $psize);

    unset($row);
}elseif ($operation == 'delete') {
    $data = array(
        'delete' => '1'
    );
    pdo_update('sz_yi_return', $data, array(
        'id' => $_GPC['id']
    ));
    message('删除成功！', referer(), 'success');

}

include $this->template('return_tj');


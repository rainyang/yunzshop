<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$openid    = m('user')->getOpenid();
$member = m('member')->getMember($openid);
$uniacid   = $_W['uniacid'];

$sets = $this->getSet();
$set = $sets['ranking'];

$set['rankingname'] = !empty($set['rankingname'])?$set['rankingname']:"排行榜";
$set['integralname'] = !empty($set['integralname'])?$set['integralname']:"积分榜";
$set['expensename'] = !empty($set['expensename'])?$set['expensename']:"消费榜";
$set['commissionname'] = !empty($set['commissionname'])?$set['commissionname']:"佣金榜";
$style_width_type = 0;
if($set['isintegral'] == 1)
{
    $style_width_type+=1;
}
if($set['isexpense'] == 1)
{
    $style_width_type+=1;
}
if($set['iscommission'] == 1)
{
    $style_width_type+=1; 
}
$style_width = 100 / $style_width_type;


if ($set['isintegral']) {
    $type = 0;
} elseif ($set['isexpense']) {
    $type = 1;
}else {
    $type = 2;
}

$_GPC['type'] = $_GPC['type']?$_GPC['type']:$type;

$default_avatar = "../addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg";
if ($_W['isajax']) {
    if ($operation == 'display') {
        if($_GPC['type'] == 0)
        {
            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 10;

            $list      = pdo_fetchall("select * from " . tablename('mc_members') . " where uniacid = '" .$_W['uniacid'] . "' order by credit1 desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total     = pdo_fetchcolumn('select count(*) from ' . tablename('mc_members') . " where  uniacid = '" .$_W['uniacid'] . "'");
            //我的排名
            $m_list      = pdo_fetchall("select * from " . tablename('mc_members') . " where uniacid = '" .$_W['uniacid'] . "' and credit1 > '".$member['credit1']."'");
            $m_num = count($m_list)+1;
            foreach ($list as $k => &$row) {
                $row['number'] = ($k+1) + ($pindex - 1) * $psize;
                $row['avatar'] = !empty($row['avatar'])?$row['avatar']:$default_avatar;
            }
            unset($row);

            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type'=>$_GPC['type'],
                'm_num'=>$m_num,
                'm_credit1'=>$member['credit1'],
                'm_credit_name'=>"总积分",
                'm_avatar'=>!empty($member['avatar'])?$member['avatar']:$default_avatar
            ));

        }elseif($_GPC['type'] == 1)
        {

            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 10;

            $condition = " and o.uniacid={$_W['uniacid']}";
            $condition1 = ' and m.uniacid=:uniacid';
            $params1 = array(':uniacid' => $_W['uniacid']);
            $sql     = "SELECT m.id,m.uniacid,m.realname, m.mobile,m.avatar,m.nickname,l.levelname," . "(select ifnull( count(o.id) ,0) from  " . tablename('sz_yi_order') . " o where o.openid=m.openid and o.status>=1 {$condition})  as ordercount," . "(select ifnull(sum(o.price),0) from  " . tablename('sz_yi_order') . " o where o.openid=m.openid  and o.status>=1 {$condition})  as ordermoney" . " from " . tablename('sz_yi_member') . " m  " . " left join " . tablename('sz_yi_member_level') . " l on l.id = m.level" . " where 1 {$condition1} order by ordermoney desc ";
            $sql .= "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
            
            $list  = pdo_fetchall($sql, $params1);

            $total = pdo_fetchcolumn("select  count(*) from " . tablename('sz_yi_member') . ' m ' . " where 1 {$condition1} ", $params1);

  
            $m_sql     = "SELECT m.id,m.uniacid,m.realname, m.mobile,m.avatar,m.nickname,l.levelname," . "(select ifnull( count(o.id) ,0) from  " . tablename('sz_yi_order') . " o where o.openid=m.openid and o.status>=1 {$condition})  as ordercount," . "(select ifnull(sum(o.price),0) from  " . tablename('sz_yi_order') . " o where o.openid=m.openid  and o.status>=1 {$condition})  as ordermoney" . " from " . tablename('sz_yi_member') . " m  " . " left join " . tablename('sz_yi_member_level') . " l on l.id = m.level" . " where 1 {$condition1} and m.id = '".$member['id']."'";
            $m_list  = pdo_fetch($m_sql, $params1);

            $m_num = 1;

            foreach ($list as $k => &$row) {
                $row['number'] = ($k+1) + ($pindex - 1) * $psize;
                $row['avatar'] = !empty($row['avatar'])?$row['avatar']:$default_avatar;
                if($m_list['ordermoney'] < $row['ordermoney'])
                {
                    $m_num += 1;
                }
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type'=>$_GPC['type'],
                'm_num'=>$m_num,
                'm_credit1'=>$m_list['ordermoney'],
                'm_credit_name'=>"总消费",
                'm_avatar'=>!empty($member['avatar'])?$member['avatar']:$default_avatar
            ));

        }elseif($_GPC['type'] == 2)
        {
            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 10;

            $list = pdo_fetchall("select r.*, m.realname,m.avatar from " . tablename('sz_yi_ranking') . " r left join " . tablename('sz_yi_member') . " m on(r.mid = m.id) where r.uniacid = '" .$_W['uniacid'] . "' and r.mid > 0 order by r.credit desc   LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

            $total     = pdo_fetchcolumn('select count(*) from ' . tablename('sz_yi_ranking') . " where  uniacid = '" .$_W['uniacid'] . "'");
            $m_list = pdo_fetch("select r.*, m.realname,m.avatar from " . tablename('sz_yi_ranking') . " r left join " . tablename('sz_yi_member') . " m on(r.mid = m.id) where r.uniacid = '" .$_W['uniacid'] . "' and r.mid = '".$member['id']."'" );

            $lists = pdo_fetchall("select r.*, m.realname,m.avatar from " . tablename('sz_yi_ranking') . " r left join " . tablename('sz_yi_member') . " m on(r.mid = m.id) where r.uniacid = '" .$_W['uniacid'] . "' and r.credit > '".$m_list['credit']."' and m.id > '0'" );
            $m_num = count($lists)+1;
            foreach ($list as $k => &$row) {
                $row['number'] = ($k+1) + ($pindex - 1) * $psize;
                $row['avatar'] = !empty($row['avatar'])?$row['avatar']:$default_avatar;
            }
            unset($row);
            return show_json(1, array(
                'total' => $total,
                'list' => $list,
                'pagesize' => $psize,
                'type'=>$_GPC['type'],
                'm_num'=>$m_num,
                'm_credit1'=>$m_list['credit']?$m_list['credit']:0,
                'm_credit_name'=>"总佣金",
                'm_avatar'=>!empty($member['avatar'])?$member['avatar']:$default_avatar
            ));

        }

    }
}
include $this->template('ranking');

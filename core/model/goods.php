<?php
/*=============================================================================
#     FileName: goods.php
#         Desc: ÉÌÆ·Àà
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:32:56
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Goods
{
    public function getOption($goodsid = 0, $optionid = 0)
    {
        global $_W;
        return pdo_fetch('select * from ' . tablename('sz_yi_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid', array(
            ':id' => $optionid,
            ':uniacid' => $_W['uniacid'],
            ':goodsid' => $goodsid
        ));
    }
    public function getList($args = array())
    {
        global $_W;
        $page      = !empty($args['page']) ? intval($args['page']) : 1;
        $pagesize  = !empty($args['pagesize']) ? intval($args['pagesize']) : 10;
        $random    = !empty($args['random']) ? $args['random'] : false;
        $choose    = !empty($args['choose']) ? $args['choose'] : false;
        $order     = !empty($args['order']) ? $args['order'] : ' displayorder desc,createtime desc';
        $orderby   = !empty($args['by']) ? $args['by'] : '';
        $ids       = !empty($args['ids']) ? trim($args['ids']) : '';
        $id       = !empty($args['id']) ? trim($args['id']) : '0';
        $sup_uid   = !empty($args['supplier_uid']) ? trim($args['supplier_uid']) : '';
        $isopenchannel   = !empty($args['isopenchannel']) ? trim($args['isopenchannel']) : 0;
        $ischannelpick   = !empty($args['ischannelpick']) ? trim($args['ischannelpick']) : 0;
        $condition = ' and `uniacid` = :uniacid AND `deleted` = 0 and status=1';
        $params    = array(
            ':uniacid' => $_W['uniacid']
        );
        if (!empty($ids)) {
            $condition .= " and id in ( " . $ids . ")";
        }
        if (!empty($id)) {
            $condition .= " and id = :id";
            $params[':id'] = intval($id);
        }
        if (!empty($args['isverify']) && $args['isverify'] == 1) {
            $condition .= " and isverify = '1' ";
        }
        if (!empty($args['isverify']) && $args['isverify'] == 2) {
            $condition .= " and isverify = '2' ";
        }
        if (!empty($sup_uid)) {
            $condition .= " and supplier_uid = :supplier_uid ";
            $params[':supplier_uid'] = intval($sup_uid);
        }
        if (!empty($isopenchannel)) {
            $condition .= " and isopenchannel = :isopenchannel ";
            $params[':isopenchannel'] = intval($isopenchannel);
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

        if($args['plugin'] == 'fund'){
            $condition .= " and plugin='fund'";
        } elseif ($args['plugin'] == 'recharge') {
            $condition .= " and plugin='recharge'";
        } else {
            $condition .= " and plugin=''";
        }
        $openid  = m('user')->getOpenid();
        if(empty($openid)){
            $openid = m('user')->isLogin();
        }
        $member  = m('member')->getMember($openid);
        $levelid = intval($member['level']);
        $groupid = intval($member['groupid']);
        $condition .= " and ( ifnull(showlevels,'')='' or FIND_IN_SET( {$levelid},showlevels)<>0 ) ";
        $condition .= " and ( ifnull(showgroups,'')='' or FIND_IN_SET( {$groupid},showgroups)<>0 ) ";
        if (!empty($ischannelpick)) {
            $list = array();
            $goodsinfo = pdo_fetchall("SELECT distinct goodsid FROM " . tablename('sz_yi_channel_stock') . " WHERE uniacid={$_W['uniacid']} and openid='{$openid}'");
            if (!empty($goodsinfo)) {
                foreach ($goodsinfo as $value) {
                        $channel_goods =  pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE uniacid={$_W['uniacid']} AND id={$value['goodsid']}");
                        $channel_goods['total'] = p('channel')->getMyOptionStock($openid, $value['goodsid'], 0);
                        $list[] = $channel_goods;
                }
            }
        } else {
            if (!$random) {
                if ($choose) {
                    $sql = "SELECT * FROM " . tablename('sz_yi_goods') . " where 1 {$condition} ORDER BY {$order} {$orderby} ";
                } else {
                    $sql = "SELECT * FROM " . tablename('sz_yi_goods') . " where 1 {$condition} ORDER BY {$order} {$orderby} LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
                }    
            } else {
                
                    $sql = "SELECT * FROM " . tablename('sz_yi_goods') . " where 1 {$condition} ORDER BY rand() LIMIT " . $pagesize;
                
                
            }
            $list = pdo_fetchall($sql, $params);
        }
        $list = set_medias($list, 'thumb');
        return $list;
    }
    public function getComments($goodsid = '0', $args = array())
    {
        global $_W;
        $page      = !empty($args['page']) ? intval($args['page']) : 1;
        $pagesize  = !empty($args['pagesize']) ? intval($args['pagesize']) : 10;
        $condition = ' and `uniacid` = :uniacid AND `goodsid` = :goodsid and deleted=0';
        $params    = array(
            ':uniacid' => $_W['uniacid'],
            ':goodsid' => $goodsid
        );
        $sql       = "SELECT id,nickname,headimgurl,content,images FROM " . tablename('sz_yi_goods_comment') . " where 1 {$condition} ORDER BY createtime desc LIMIT " . ($page - 1) * $pagesize . ',' . $pagesize;
        $list      = pdo_fetchall($sql, $params);
        foreach ($list as &$row) {
            $row['images'] = set_medias(unserialize($row['images']));
        }
        unset($row);
        return $list;
    }
    //计算阶梯价格
    public function getLaderMoney($data = array(), $number = '') {
        $money = 0;

        foreach ($data as $key => $value) {
            if ( $value['minimum'] <= $number && $value['maximum'] >= $number) {
                $money = $value['ladderprice'];
                break;
            } elseif ($value['minimum'] == '大于' && $value['maximum'] <= $number) {
                $money = $value['ladderprice'];
                break;
            }
        }
        return $money;
    }
}

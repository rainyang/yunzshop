<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$openid         = m('user')->getOpenid();
$member         = m('member')->getMember($openid);
$uniacid        = $_W['uniacid'];
$goodsid        = intval($_GPC['id']);



if($_GPC['mobile'] && $_GPC['goodsid']){
    $url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$_GPC['mobile']."&t=".time();
    $num = 0;
    do {
        $content = file_get_contents($url);
        $html = iconv("gb2312", "utf-8//IGNORE",$content);
        $data = explode(',', $html);
        $province = explode(':', $data[1]);
        $catname = explode(':', $data[2]);
        $carrier = explode(':', $data[6]);
        $array = array(
            trim($province[0]) => str_replace("'","",$province[1]),
            trim($catname[0]) => str_replace("'","",$catname[1]),
            trim($carrier[0]) => trim(substr(str_replace("'","",$carrier[1]),0,-3))
        );
        $num++;
    } while (empty($array['catName']) && $num < 3);
    //echo json_encode($array);exit;
    if ($array['catName'] == '中国移动') {
        $operator = 1;
    } else if ($array['catName'] == '中国联通') {
        $operator = 2;
    } else if ($array['catName'] == '中国电信') {
        $operator = 3;
    }
    if (!empty($array)) {
        $goods          = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :id limit 1", array(
            ':id' => $_GPC['goodsid']
        ));
        if(empty($goods['province']) && $goods['operator'] == 0){
            //没有地区及运营商限制
            $code = 1;
            //$data = array('code' => 1,'carrier' => $array['carrier'],'catname' => $array['catName']);
        }else{
            if (!empty($goods['province']) && $goods['operator'] == 0) {
                $where = " and province like '%" . $array['province'] . "%'";
            }
            if (empty($goods['province']) && $goods['operator'] != 0) {
                $where = " and operator = " . $operator;
            }
            if (!empty($goods['province']) && $goods['operator'] != 0) {
                $where = " and province like '%" . $array['province'] . "%' and operator = " . $operator;
            }
            $goods_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :goodsid " . $where,array(':goodsid' => $_GPC['goodsid']));
            if(!empty($goods_info)){
                $code = 1;
                //$data = array('code' => 1,'carrier' => $array['carrier'],'catname' => $array['catName']);
            }else{
                $goods_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE id = :goodsid ",array(':goodsid' => $_GPC['goodsid']));
                $code = 0;
                //$data = array('code' => 0,'carrier' => $array['carrier'],'catname' => $array['catName'],'limit_province' => $goods_info['province'],'limit_operator' => $goods_info['operator']);
            }
        }
        if ($code == 1) {
            $spec_provincial_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $_GPC['goodsid'] . " and gsi.title = '省内流量' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//省内

            $spec_domestic_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $_GPC['goodsid'] . " and gsi.title = '国内流量' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//国内

            $spec_operator_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $_GPC['goodsid'] . " and gsi.title = '" . $array['catName'] . "' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//获取用户填写手机号码的商品规格运营商id

            $spec_ids = pdo_fetchall("select gsi.id,gsi.title from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = :id and gs.uniacid = :uniacid
            order by gs.displayorder asc",
                array(
                    ':id' => $_GPC['goodsid'],
                    ':uniacid' => $_W['uniacid']
                ));//获取商品所有规格
            $spec_provincial_data = array();
            $spec_domestic_data = array();
            foreach ($spec_ids as $key => $data_id) {
                $spec_provincial_data = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_option') . " 
                WHERE specs =  '" . $spec_provincial_id . "_" . $spec_operator_id . "_" . $data_id['id'] . "' 
                and stock > 0 and uniacid = :uniacid",
                    array(
                        ':uniacid' => $_W['uniacid']
                    ));
                $spec_domestic_data = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_option') . " 
                WHERE specs =  '" . $spec_domestic_id . "_" . $spec_operator_id . "_" . $data_id['id'] . "' 
                and stock > 0 and uniacid = :uniacid",
                    array(
                        ':uniacid' => $_W['uniacid']
                    ));
                if (!empty($spec_provincial_data)) {
                    $spec_datas['provincial'][$key]['itemid'] = $data_id['id'];
                    $spec_datas['provincial'][$key]['itemtitle'] = $data_id['title'];
                    $spec_datas['provincial'][$key]['optionid'] = $spec_provincial_data['id'];
                    $spec_datas['provincial'][$key]['price'] = $spec_provincial_data['marketprice'];
                }
                if (!empty($spec_domestic_data)) {
                    $spec_datas['domestic'][$key]['itemid'] = $data_id['id'];
                    $spec_datas['domestic'][$key]['itemtitle'] = $data_id['title'];
                    $spec_datas['domestic'][$key]['optionid'] = $spec_domestic_data['id'];
                    $spec_datas['domestic'][$key]['price'] = $spec_domestic_data['marketprice'];
                }
            }

            /*$allspecs = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec') . " where goodsid=:id order by displayorder asc", array(
                ':id' => $_GPC['goodsid']
            ));
            foreach ($allspecs as &$s) {
                $items      = pdo_fetchall("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(
                    ":specid" => $s['id']
                ));
                foreach ($items as $itemid) {
                    if (in_array($itemid['id'],$spec_datas['provincial'])) {
                        $items_new['provincial'][]     = pdo_fetch("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and id=:id order by displayorder asc", array(
                            ":id" => $itemid['id']
                        ));
                    }
                    if (in_array($itemid['id'],$spec_datas['domestic'])) {
                        $items_new['domestic'][]     = pdo_fetch("select * from " . tablename('sz_yi_goods_spec_item') . " where  `show`=1 and id=:id order by displayorder asc", array(
                            ":id" => $itemid['id']
                        ));
                    }
                }
                $s['items'] = set_medias($items_new, 'thumb');
                $s['optionid'] = set_medias($items_new, 'thumb');

            }
            unset($s);*/

        }
        $ret = array('code' => $code,'carrier' => $array['carrier'],'catname' => $array['catName'],'spec_datas' => $spec_datas);
        echo json_encode($ret);

        exit;

    }
//
//    $data = json_encode($spec_mobile_data_ids);
//
//    //$array = json_encode($array);
//    echo $data;exit;
}


<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$openid         = m('user')->getOpenid();
$member         = m('member')->getMember($openid);
$uniacid        = $_W['uniacid'];
//$goodsid        = intval($_GPC['id']);



if($_GPC['mobile']){
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
    //手机号所属运营商
    if ($array['catName'] == '中国移动') {
        $operator = 1;
    } else if ($array['catName'] == '中国联通') {
        $operator = 2;
    } else if ($array['catName'] == '中国电信') {
        $operator = 3;
    }
    $province = $array['province'];//手机号所属省份
    if (!empty($operator) && !empty($province)) {
        $goods          = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE operator = :operator AND province like '%{$province}%' AND uniacid = :uniacid limit 1", 
            array(
            ':operator' => $operator,
            ':uniacid' => $_W['uniacid']
        ));
        if (empty($goods)) {
            $goods          = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE operator = :operator AND uniacid = :uniacid limit 1", 
                array(
                    ':operator' => $operator,
                    ':uniacid' => $_W['uniacid']
                ));
        }
        if (!empty($goods)) {
            $code = 1;
            $goodsid = $goods['id'];
        } else {
            $code = 0;
        }
        if ($code == 1) {
            $spec_provincial_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $goodsid . " and gsi.title = '省内流量' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//省内

            $spec_domestic_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $goodsid . " and gsi.title = '国内流量' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//国内

            $spec_operator_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = " . $goodsid . " and gsi.title = '" . $array['catName'] . "' and gs.uniacid = " . $_W['uniacid'] ." 
            order by gs.displayorder asc");//获取用户填写手机号码的商品规格运营商id

            $spec_ids = pdo_fetchall("select gsi.id,gsi.title from " . tablename('sz_yi_goods_spec') . " gs 
            left join " . tablename('sz_yi_goods_spec_item') . " gsi 
            on gs.id = gsi.specid 
            where gs.goodsid = :id and gs.uniacid = :uniacid
            order by gs.displayorder asc",
                array(
                    ':id' => $goodsid,
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
        }
        $ret = array('code' => $code,'carrier' => $array['carrier'],'catname' => $array['catName'],'spec_datas' => $spec_datas,'goodsid' => $goodsid);
        echo json_encode($ret);
        exit;
    }
}


<?php


if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$openid         = m('user')->getOpenid();
$member         = m('member')->getMember($openid);
$uniacid        = $_W['uniacid'];
/*
 *  1.判断手机号码运营商及所属省份
 *  2.查找对应运营商商品，空则返回，存在则继续筛选
 *  3.数据筛选出该运营商下的省内流量（省内流量）
 *  4.数据筛选出该运营商下的国内流量（国内流量）
 *  5.如果没有对应省份国内流量，则筛选出全国流量作为国内流量
 */
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
    if (!empty($operator)) {//手机号码验证成功
        $goods = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_goods') . " WHERE operator = :operator AND uniacid = :uniacid AND status = 1 ", 
            array(
            ':operator' => $operator,
            ':uniacid' => $_W['uniacid']
        ));
        if (!empty($goods)) {//有数据
            //$code = 1;
            foreach ($goods as $key => $good) {
                $spec_operator_id = pdo_fetchcolumn("select gsi.id from " . tablename('sz_yi_goods_spec') . " gs 
                left join " . tablename('sz_yi_goods_spec_item') . " gsi 
                on gs.id = gsi.specid 
                where gs.goodsid = " . $good['id'] . " and gsi.title = '" . $array['catName'] . "' and gs.uniacid = " . $_W['uniacid'] ." 
                order by gs.displayorder asc");//获取用户填写手机号码的商品规格运营商id

                $spec_ids = pdo_fetchall("select gsi.id,gsi.title from " . tablename('sz_yi_goods_spec') . " gs 
                left join " . tablename('sz_yi_goods_spec_item') . " gsi 
                on gs.id = gsi.specid 
                where gs.goodsid = :id and gs.uniacid = :uniacid
                order by gs.displayorder asc",
                array(
                    ':id' => $good['id'],
                    ':uniacid' => $_W['uniacid']
                ));//获取商品所有规格
                $spec_data = array();
                foreach ($spec_ids as $k => $data_id) {
                    $spec_data[] = pdo_fetch("SELECT * FROM " . tablename('sz_yi_goods_option') . " 
                    WHERE specs =  '" . $spec_operator_id . "_" . $data_id['id'] . "' 
                    and stock > 0 and uniacid = :uniacid",
                    array(
                        ':uniacid' => $_W['uniacid']
                    ));
                }
                //echo json_encode($spec_data);exit;
                foreach ($spec_data as $k => $value) {
                    if ($good['isprovince'] == 1 && strpos($good['province'], $province) !== false && !empty($value)) {//省份省内
                        $spec_datas['provincial'][$k]['itemid'] = $spec_ids[$k]['id'];
                        $spec_datas['provincial'][$k]['itemtitle'] = $spec_ids[$k]['title'];
                        $spec_datas['provincial'][$k]['optionid'] = $value['id'];
                        $spec_datas['provincial'][$k]['price'] = $value['marketprice'];
                        $spec_datas['provincial'][$k]['goodsid'] = $good['id'];
                    }   
                    if (empty($good['isprovince']) && strpos($good['province'], $province) !== false && !empty($value)) {//省份国内
                        $spec_datas['domestic'][$k]['itemid'] = $spec_ids[$k]['id'];
                        $spec_datas['domestic'][$k]['itemtitle'] = $spec_ids[$k]['title'];
                        $spec_datas['domestic'][$k]['optionid'] = $value['id'];
                        $spec_datas['domestic'][$k]['price'] = $value['marketprice'];
                        $spec_datas['domestic'][$k]['goodsid'] = $good['id'];
                    }
                    if (empty($good['isprovince']) && empty($good['province']) && !empty($value)) {//全国国内
                        $spec_datas['alldomestic'][$k]['itemid'] = $spec_ids[$k]['id'];
                        $spec_datas['alldomestic'][$k]['itemtitle'] = $spec_ids[$k]['title'];
                        $spec_datas['alldomestic'][$k]['optionid'] = $value['id'];
                        $spec_datas['alldomestic'][$k]['price'] = $value['marketprice'];
                        $spec_datas['alldomestic'][$k]['goodsid'] = $good['id'];
                    }  
                }   
            }
            if (!empty($spec_datas)) {
                $code = 1;
            } else {
                $code = 0;
            }
            $ret = array(
                'code' => $code,
                'carrier' => $array['carrier'],
                'catname' => $array['catName'],
                'spec_datas' => $spec_datas,
                );
        } else {//无数据
            $code = 0;
            $ret = array(
                'code' => $code,
                'carrier' => $array['carrier'],
                'catname' => $array['catName'],
                'spec_datas' => $spec_datas,
                );
        }

    } else {//手机号码验证失败
       $code = -1; 
       $ret = array(
            'code' => $code
            );
    }
    echo json_encode($ret);
    exit;
}


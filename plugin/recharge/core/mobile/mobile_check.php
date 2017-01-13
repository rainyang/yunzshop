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
if ($_GPC['mobile']) {
    $mobile = intval($_GPC['mobile']);
    $array = $this->model->mobileApi($mobile);
    $catname = !empty(trim($array['catName']))?trim($array['catName']):''; //手机号运营商
    $province = !empty(trim($array['province']))?trim($array['province']):'';//手机号所属省份
    $carrier = !empty(trim($array['carrier']))?trim($array['carrier']):'';//手机号完整信息
    if (empty($catname) || empty($province)) {
        $code = -1;
        $ret = array(
            'code' => $code
        );
        echo json_encode($ret);
        exit;
    }
    if ($catname == '中国移动') {
        $operator = 1;
    } else if ($catname == '中国联通') {
        $operator = 2;
    } else if ($catname == '中国电信') {
        $operator = 3;
    }
    if (!empty($operator)) {//手机号码验证成功
        $code = 0;
        $goods = $this->model->getOperatorGoods($operator); //获取对应运营商下的所有商品
        if (!empty($goods)) {
            $spec_datas = $this->model->getAllOptionsByMobile($goods, $catname, $province);//获取对应运营商及省份信息的规格
            if (!empty($spec_datas)) {
                $code = 1;
            }
        }
        $ret = array(
            'code' => $code,
            'carrier' => $carrier,
            'catname' => $catname,
            'spec_datas' => !empty($spec_datas)?$spec_datas:'',
        );
    } else {//手机号码验证失败
       $code = -1; 
       $ret = array(
           'code' => $code
       );
    }
    echo json_encode($ret);
    exit;
}


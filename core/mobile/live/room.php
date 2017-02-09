<?php 
//之所以把直播放在addons/sz_yi/core/mobile, 是为了在微信播放时能够获取openid, 在addons/sz_yi/plugin文件夹下无法获取

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$room_id = $_GPC['room_id'];
$page = $_GPC['page'];
$page_size = $_GPC['pagesize'];
$openid = m('user')->getOpenid();
$domain = $_SERVER['HTTP_HOST'];

if ($operation == 'display'){

    load()->func('communication');

    //生成分销上下级关系("主播"做为上级, "观看者"如果之前没有分销关系, 则做为"主播"的下级)
    //获取主播信息
    $url_01 = SZ_YI_LIVE_CLOUD_URL . '/shop_live.php?api=room/Get&room_id='.$room_id; //todo
    $result_01 = ihttp_get($url_01);
    $result_array_01 = json_decode($result_01['content'], true);
    $room_info = $result_array_01['data'];
    $anchor_mobile = $room_info['mobile'];

    //获取主播id
    $mid = pdo_fetchcolumn('SELECT id FROM ' . tablename('sz_yi_member') . ' WHERE mobile = :mobile', array(':mobile' => $anchor_mobile));
    $_GPC['mid'] = $mid;

    p('commission')->checkAgent();

    //curl请求"获取直播间关联商品列表"的API
    $url_02 = SZ_YI_LIVE_CLOUD_URL . '/admin_live.php?api=room/Goods&room_id=' . $room_id;
    if(!empty($page)){
        $url_02 .= '&page=' . $page;
    }
    if(!empty($page_size)){
        $url_02 .= '&pagesize=' . $page_size;
    }
    $result_02 = ihttp_get($url_02);
    $result_array_02 = json_decode($result_02['content'], true);
    $goods_list = $result_array_02['data']['list'];

    if(!empty($goods_list)){

        //获取商品ID
        $scope = array();
        foreach($goods_list as $k=>$v){
            $scope[$k] = $v['goods_id'];
        }
        $scope = implode(',', $scope);

        //查询商品详细信息
        $goods_info_list = pdo_fetchall('SELECT id, thumb, title, productprice, marketprice FROM ' . tablename('sz_yi_goods') . ' WHERE id IN (' . $scope . ') ORDER BY FIELD (id, ' . $scope . ')');
        $goods_info_list = set_medias($goods_info_list, "thumb");
      
    }


    //获取sig
    if(empty($_GPC['sig'])){
        $result_02 = ihttp_get(SZ_YI_LIVE_CLOUD_URL.'/shop_live.php?api=IM/Get/sign&openid='.$openid.'&domain='.$domain);
        $result_02_array = json_decode($result_02['content'], true);
        $sig = $result_02_array['data']['sign'];
        $identifier = $result_02_array['data']['identifier'];
    }

    //获取昵称(如果没有nickname,就用mobile做为昵称)
    $userInfo = pdo_fetch('SELECT nickname, mobile FROM '.tablename('sz_yi_member').' WHERE openid = :openid', array(':openid'=>$openid));
    if(!empty($userInfo['nickname'])){
        $nickName = $userInfo['nickname'];
    } else {
        $nickName = $userInfo['mobile'];
    }

}

include $this->template('live/room');
<?php 
//之所以把直播放在addons/sz_yi/core/mobile, 是为了在微信播放时能够获取openid, 在addons/sz_yi/plugin文件夹下无法获取

global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$room_id = $_GPC['room_id']; //必须的参数
$page = $_GPC['page'];
$page_size = $_GPC['pagesize'];
$openid = m('user')->getOpenid();
$domain = $_SERVER['HTTP_HOST'];

if ($operation == 'display'){

    //curl请求"获取直播间关联商品列表"的API
    load()->func('communication');
    $url = 'http://sy.yunzshop.com/admin_live.php?api=room/Goods&room_id=' . $room_id;
    if(!empty($page)){
        $url .= '&page=' . $page;
    }
    if(!empty($page_size)){
        $url .= '&pagesize=' . $page_size;
    }
    $result = ihttp_get($url);
    $result_array = json_decode($result['content'], true);
    $goods_list = $result_array['data']['list'];

    if(!empty($goods_list)){

        //获取商品ID
        $scope = array();
        foreach($goods_list as $k=>$v){
            $scope[$k] = $v['goods_id'];
        }
        $scope = implode(',', $scope);

        //查询商品详细信息
        $goods_info_list = pdo_fetchall('SELECT id, thumb, title, productprice, marketprice FROM ' . tablename('sz_yi_goods') . ' WHERE id IN (' . $scope . ') ORDER BY FIELD (' . $scope . ')');
        $goods_info_list = set_medias($goods_info_list, "thumb");
      
    }


    //获取sig
    if(empty($_GPC['sig'])){
        $result_02 = ihttp_get('http://live.tbw365.cn/shop_live.php?api=IM/Get/sign&openid='.$openid.'&domain='.$domain);
        $result_02_array = json_decode($result_02['content'], true);
        $sig = $result_02_array['data']['sign'];
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
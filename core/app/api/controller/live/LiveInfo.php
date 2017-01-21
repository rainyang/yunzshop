<?php
namespace app\api\controller\live;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 返回小直播需要的所有信息
 * @param num room_id 直播间ID
 * @return 直播间信息和主播信息
 */
class LiveInfo extends YZ
{
    
    public function index()
    {
        global $_W, $_GPC;
        load()->func('communication');
        $room_id = $_GPC['room_id'];

        //获取直播间信息
        $url = 'http://sy.yunzshop.com/shop_live.php?api=room/Get&room_id='.$room_id; //todo
        $result_01 = ihttp_get($url);
        $result_01_array = json_decode($result_01['content'], true);
        $room_info = $result_01_array['data'];
        $anchor_mobile = $room_info['mobile'];

        //获取主播信息
        $anchor_info = pdo_fetch('SELECT nickname, avatar FROM ' . tablename('sz_yi_member') . ' WHERE mobile = :mobile', array(':mobile' => $anchor_mobile));

        if(!empty($room_info)){
            $this->returnSuccess(array_merge(array('userinfo'=>$anchor_info),array('userid'=>$room_info['room_id']), $room_info)); //按照腾讯云文档https://www.qcloud.com/document/product/454/8046#step2.3A-.E7.94.A8.E6.88.B7.E6.89.93.E5.BC.80.E9.93.BE.E6.8E.A5, 主播 id 即为房间号
        } else {
            $this->returnError('获取信息失败');
        }
    }
    
}


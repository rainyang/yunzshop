<?php
namespace app\api\controller\live;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 返回直播间列表
 */
class Group extends YZ
{
    
    public function index()
    {
        global $_W, $_GPC;
        $domain = $_SERVER['HTTP_HOST'];
        $uniacid = $_W['uniacid'];
        // $openid = m('user')->isLogin(); //todo
        $mobile = $_GPC['mobile'];

        //curl请求"获取直播间列表"的API
        load()->func('communication');
        $url = 'http://sy.yunzshop.com/test/shop_live.php?api=room&domain='.$domain.'&uniacid='.$uniacid;
        $result_01 = ihttp_get($url);
        $result_01_array = json_decode($result_01['content'], true);
        $room_list = $result_01_array['data'];

        //获取banner列表
        $banner_list = pdo_fetchall('SELECT advname, link, thumb FROM ' . tablename('sz_yi_live_banner') . ' WHERE enabled = 1 AND uniacid = :uniacid ORDER BY displayorder DESC', array('uniacid' => $uniacid));

        //获取sig
        // $mobile = pdo_fetchcolumn('SELECT mobile FROM ' . tablename('sz_yi_member') . 'WHERE openid = :openid', array(':openid'=>$openid));
        $result_02 = ihttp_get('http://live.tbw365.cn/shop_live.php?api=IM/Get/sign&mobile='.$mobile);
        $result_02_array = json_decode($result_02['content'], true);
        $sig = $result_02_array['data']['sign'];

        $this->returnSuccess(array('room_list'=>$room_list, 'banner_list'=>$banner_list, 'sig'=>$sig));
    }
    
}


<?php
namespace app\api\controller\live;
@session_start();
use app\api\YZ;
use app\api\Request;

/**
 * 返回轮播图列表
 */
class Banner extends YZ
{
    
    public function index()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];

        $banner_list = pdo_fetchall('SELECT advname, link, thumb FROM ' . tablename('sz_yi_live_banner') . ' WHERE enabled = 1 and uniacid = :uniacid ORDER BY displayorder DESC', array('uniacid' => $uniacid));

        if(!empty($banner_list)){
            $this->returnSuccess($banner_list);
        } else {
            $this->returnError('获取失败');
        }
    }
    
}


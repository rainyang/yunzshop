<?php
/*=============================================================================
#     FileName: Upgrade.php
#         Desc: APP升级接口
#   Created by: Sublime Text3.  
#       Author: yitian - http://www.yunzshop.com
#        Email: livsyitian@163.com
#     HomePage: http://shop.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-12-5 11:28:10
#      History:
#         Q Q : 751818588
=============================================================================*/
namespace app\api\controller\cloud;
@session_start();
use app\api\YZ;

class Upgrade extends YZ
{

    public function index()
    {
        $version = $this -> getversion();
        if ($version['apkstatus'] == 1) {
            return $this->returnSuccess($version);
        }
        return $this->returnError($version['msg']);
    }
    //云端检测
    private function detection()
    {
        //define('CLOUD_URL', 'http://cloud.yunzshop.com/web/index.php?c=account&a=appupgrade');
        define('CLOUD_URL', 'http://lyt.yunzshop.com/web/index.php?c=account&a=appupgrade');        //测试链接
        load()->func('communication');

        $resp = ihttp_post(CLOUD_URL, array(
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST']
        ));
        $content = json_decode($resp['content'], 1);
        if ($resp) {
            return array(
                'status'    => $content['status'],
                'apkstatus' => $content['apkstatus'],
                'msg'       => $content['msg']
            );
        }
        return array('status' => 0, 'msg' => "云端请求失败");
    }

    private function getversion()
    {
        $version = pdo_fetch('select * from ' . tablename('sz_yi_appinfo') . 'where version_code = (select max(version_code) from ' . tablename('sz_yi_appinfo') . ')');

        $ret = $this -> detection();
        if ($ret['status'] == 1 && $ret['apkstatus'] == 1) {
            $version['apkstatus'] = 1;
            return $version;
        }
        // status = 0 : 云端请求失败， status = 1 ：云端注册用户/允许升级，status = 2 ：云端注册用户/禁止升级， status = 3 ：非法用户。
        return array(
            'msg' => $ret['msg'],
            'apkstatus' => 0
        );
    }

}

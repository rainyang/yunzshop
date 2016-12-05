<?php
namespace app\api\controller\cloud;
@session_start();
use app\api\YZ;

class Upgrade extends YZ
{

    public function index()
    {
        $op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
        if ($op == 'display') {

        }
        return $this->returnSuccess();
    }

    function detection()
    {
        echo "<pre>"; print_r(456);exit;
    }

}
/*        define('CLOUD_UPGRADE_URL', 'http://lyt.yunzshop.com/web/index.php?c=account&a=upgradetest1');
        load()->func('communication');

        $version = "1.3";
        $resp = ihttp_post(CLOUD_UPGRADE_URL, array(
            'type' => 'download',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version
        ));
        echo "<pre>"; print_r($resp);exit;*/

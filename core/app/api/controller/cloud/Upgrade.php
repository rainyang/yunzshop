<?php
namespace app\api\controller\cloud;
@session_start();
use app\api\YZ;

class Upgrade extends YZ
{

    public function index()
    {
    	define('CLOUD_UPGRADE_URL', 'http://customer.liyitian.com/web/index.php?c=account&a=upgradetest1');
    	load()->func('communication');

    	$version = "1.3";
    	$resp = ihttp_post(CLOUD_UPGRADE_URL, array(
            'type' => 'download',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version
        ));
        echo "<pre>"; print_r($resp);exit;
        return $this->returnSuccess();
    }

}


<?php
namespace Util;
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 16/8/26
 * Time: 上午9:51
 */

class Push
{
    private $action;

    public function __construct()
    {
        require IA_ROOT . '/addons/sz_yi/core/inc/plugin/vendor/leancloud/src/autoload.php';
        $setdata = m("cache")->get("sysset");
        $set = unserialize($setdata['sets']);
        $app = $set['app']['base'];

        //\LeanCloud\LeanClient::initialize($app['leancloud']['id'], $app['leancloud']['key'], $app['leancloud']['master'] . ",master");
        \LeanCloud\LeanClient::initialize('egEtMTe0ky9XbUd57y5rKEAX-gzGzoHsz', 'ca0OTkPQUdrXlPTGrospCY2L', '4HFoIDCAwaeOUSedwOISMUrj' . ",master");

        $this->action = $app["leancloud"]["notify"];
    }

    public function send($alert, $content, $ext)
    {
        $action = $this->action;
        $data = array(
            "alert" => $alert,
            "badge" => "1",
            "content-available" => "0",
            "sound" => "1.wav",
            "action_type" => "1",
            "title" => $content,
            "action" => $action,
            "ext" => $ext// {"id":"'.$id.'","url":"'. $url .'"}
        );
        $lean_push = new \LeanCloud\LeanPush($data);
        $lean_push->send();
    }
}
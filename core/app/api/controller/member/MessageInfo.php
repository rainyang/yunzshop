<?php
namespace app\api\controller\member;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

/**
 * 通知详情API
 */
class MessageInfo extends YZ
{
    public function index(){
        if (!defined('IN_IA')) {
            exit('Access Denied');
        }

        $openid = m('user')->getOpenid();

        $info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_message') . " WHERE `id` = " . $_GET['id']);

        //阅读后, 将该条信息的状态设置为"已读"
        pdo_update('sz_yi_message', array('status'=>'1'), array('id'=>$_GET['id']));

        $result = show_json(1, array('info'=>$info));

        $this->returnSuccess($result);
    }
}

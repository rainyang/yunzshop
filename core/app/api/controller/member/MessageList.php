<?php
namespace app\api\controller\member;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

/**
 * 通知列表API
 */
class MessageList extends YZ
{
    public function index(){
        if (!defined('IN_IA')) {
            exit('Access Denied');
        }

        $openid = m('user')->getOpenid();

        $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_message') . " WHERE `openid` = '" . $openid . "' ORDER BY `id` DESC");
        $result = show_json(1, array('list'=>$list));

        $this->returnSuccess($result);
    }
}

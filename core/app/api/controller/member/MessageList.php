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
        
        $page = max(1, intval($_GPC['page']));
        $psize = !empty($_GPC['psize']) ? $_GPC['psize'] : 20; //每个分页的通知数量, 默认20条
        $ptotal = ceil($page / $psize); //分页总数
        
        $list = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_message') . " WHERE `openid` = '" . $openid . "' ORDER BY `id` DESC LIMIT ".($page - 1)*$psize . ',' . $psize);
        $result = show_json(1, array('list'=>$list,'pagetotal'=>$ptotal));

        $this->returnSuccess($result);
    }
}

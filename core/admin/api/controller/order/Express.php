<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\controller\order;
class Express extends \admin\api\YZ
{
    private $order_info;
    public function __construct()
    {
        //52895 etc
        ///Applications/MAMP/bin/php/php5.5.10/bin/phpize
        //./configure --with-php-config=/Applications/MAMP/bin/php/php5.5.10/bin/php-config
//
        parent::__construct();
        $para = $this->getPara();
        $order_model = new \admin\api\model\order();
        $this->order_info = $order_model->getInfo(array(
            'id' => $para["order_id"],
            'uniacid' => $para["uniacid"]
        ));
    }
    public function index(){
        global $_W;
        $order_info = $this->order_info;
        $order_info['url'] = $_W['siteurl']."/wap/&uniacid={$_W['uniacid']}&id={$order_info['id']}&express={$order_info['express']}&expresssn={$order_info['expresssn']}";
        //dump($order_info);
        $order_info = array_part('expresscom,expresssn,url',$order_info);
        pdo_update('sz_yi_api_log', ['error_info'=> json_encode($order_info)], array('api_log_id' => pdo_insertid()));

        //dump($order_info);
        $this->returnSuccess($order_info);
    }
    public function wap()
    {
        global $_W;
        //echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";exit;
        //todo deceive addons/sz_yi/core/inc/core.php line 44
        $_SERVER['SCRIPT_NAME'] = '/notify';
        $_W["uniacid"] = $_GET['uniacid'];
        $this->callWeb('order/list/deal/express');

    }
    private function getList($company_name, $sn) {
        $url = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$company_name}&fromWeb=null&postid={$sn}";
        load()->func("communication");
        $info = ihttp_request($url);
        $result = $info["content"];
        if (empty($result)) {
            return array();
        }
        preg_match_all("/\<p\>&middot;(.*)\<\/p\>/U", $result, $data);
        if (!isset($data[1])) {
            return false;
        }
        return $data[1];
    }
}


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
//dump($order_info);
        $order_info['url'] = $_W['siteurl']."/wap/&id={$order_info['express']}&express={$order_info['express']}&expresssn={$order_info['expresssn']}";
        dump($order_info);
        $order_info = array_part('expresscom,expresssn,url',$order_info);

        //dump($order_info);
        $this->returnSuccess($order_info);
    }
    public function wap()
    {
        //todo deceive addons/sz_yi/core/inc/core.php line 44
        $_SERVER['SCRIPT_NAME'] = '/notify';
        $this->callWeb('order/list/deal/express');

    }
    private function getList($company_name, $sn) {
        $url = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$company_name}&fromWeb=null&postid={$sn}";
        load()->func("communication");
        $zym_var_13 = ihttp_request($url);
        $zym_var_16 = $zym_var_13["content"];
        if (empty($zym_var_16)) {
            return array();
        }
        preg_match_all("/\<p\>&middot;(.*)\<\/p\>/U", $zym_var_16, $zym_var_5);
        if (!isset($zym_var_5[1])) {
            return false;
        }
        return $zym_var_5[1];
    }
}


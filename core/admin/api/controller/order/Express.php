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
        $order_info['url'] = $_W['siteurl']."/wap/&express={$order_info['express']}&expresssn={$order_info['expresssn']}";
        $order_info = array_part('expresscom,expresssn,url',$order_info);

        dump($order_info);
        $this->returnSuccess($order_info);
    }
    public function wap()
    {
        global $_W;
        $_W['template'] = 'default';

        //$order_info = $this->order_info;
        //dump($order_info);
        $express = 'yuantong';//trim($_GET["express"]);//
        $expresssn = '200380102446';//trim($_GET["expresssn"]);//
        //dump($express);
        //dump($expresssn);

        $arr = $this->getList($express, $expresssn);
        if (!$arr) {
            die("未找到物流信息.");
        }
        $len = count($arr);
        $step1 = explode("<br />", str_replace("&middot;", "", $arr[0]));
        $step2 = explode("<br />", str_replace("&middot;", "", $arr[$len - 1]));
        for ($i = 0; $i < $len; $i++) {
            if (strtotime(trim($step1[0])) > strtotime(trim($step2[0]))) {
                $row = $arr[$i];
            } else {
                $row = $arr[$len - $i - 1];
            }
            $step = explode("<br />", str_replace("&middot;", "", $row));
            $list[] = array(
                "time" => trim($step[0]) ,
                "step" => trim($step[1]) ,
                "ts" => strtotime(trim($step[0]))
            );
        }
        //dump();exit;

        load()->func("tpl");
        $c = new \Core();
        $c->modulename = 'sz_yi';
        require IA_ROOT.'/web/common/template.func.php';
        include $c->template('order/express');
        exit;
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


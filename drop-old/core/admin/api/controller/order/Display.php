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
class Display extends \admin\api\YZ
{
    public function __construct()
    {
        parent::__construct();
        $this->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
        //$api->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
//$api->validate('username','password');
        $order_model = new \admin\api\model\order();
        $order_list = $order_model->getList(
            array(
                'id' => intval($para["order_id"]),
                'status' => $para["status"],
                'paytype' => intval($para["paytype"]),
                'is_supplier_uid' => $this->isSupplier()
            )
        );
        if (count($order_list) == 0) {
            $this->returnSuccess(array(), '暂无数据');
        }

        dump($order_list);
        $this->returnSuccess($order_list);
    }
}
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
namespace controller\api\order;
class Display extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        $this->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
        //$api->validate('username','password');
    }

    public function index()
    {
        global $_GPC;
//$api->validate('username','password');
        $this->ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
        $order_model = new \model\api\order();
        $order_list = $order_model->getList(
            array(
                'id' => intval($_GPC["id"]),
                'status' => $_GPC["status"],
                'paytype' => intval($_GPC["paytype"]),
                'is_supplier_uid' => $this->isSupplier()
            )
        );
        if (count($order_list) == 0) {
            $this->returnSuccess([], '暂无数据');
        }

        dump($order_list);
        $this->returnSuccess($order_list);
    }
}
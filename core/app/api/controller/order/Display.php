<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;


class Display extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        global $_GPC;
        parent::__construct();
        $_GPC['id'] = $_GPC['order_id'];
        $result = $this->callMobile('order/list/display');

        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $order_list = $this->_formatOrderList($this->json['list']);

        return $this->returnSuccess($order_list);
    }

    private function _formatOrderList($order_list)
    {
        foreach ($order_list as &$order) {
            $order = $this->_formatOrder($order);
        }
        return $order_list;
    }

    private function _formatOrder($order)
    {
        $button_list = $this->_getButtonList($order);
        //$order['goods'] =
        $order += [
            'button_list' => $button_list,
        ];
        return $order;
    }

    private function _getButtonList($order)
    {
        $button_list = Order::getButtonList($order);
        return $button_list;
    }

    private function _getStatusStr($order)
    {

        $status_str = Order::getStatusStr($order);
        return $status_str;
    }

}


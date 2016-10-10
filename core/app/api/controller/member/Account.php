<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/9/27
 * Time: 下午7:54
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Account extends YZ
{
    private $_json_datas;

    public function __construct()
    {
        parent::__construct();

        $this->_json_datas = $this->callMobile('member/center');
    }

    public function index()
    { //echo '<pre>';print_r($this->_json_datas);exit;
        if (!empty($this->_json_datas) && !empty($this->_json_datas['json']['member'])) {
              //会员信息
              $res['info'] = array(
                  'id'     => '[会员ID:' . $this->_json_datas['json']['member']['id'] . ']',
                  'avatar'     => $this->_json_datas['json']['member']['avatar'],
                  'nickname'   => $this->_json_datas['json']['member']['nickname'],
                  'levelname'   => $this->_json_datas['json']['level']['levelname'],
                  'levelurl'   => $this->_json_datas['json']['set']['shop']['levelurl'],
                  'referrer_realname'   => !empty($this->_json_datas['json']['shop_set']['shop']['isreferrer'])  ? '[推荐人：' . $this->_json_datas['json']['referrer']['realname'] . ']' : '',
              );

            //操作按钮
            $res['btn'] = array();
            if ($this->_json_datas['json']['set']['trade']['closerecharge']) {
                array_push($res['btn'], array('text'=> '充值', 'url'=> '1'));
            }

            if ($this->_json_datas['json']['member']['credit2'] > 0 && empty($this->_json_datas['json']['set']['trade']['transfer'])) {
                array_push($res['btn'], array('text'=> '转账', 'url'=> '2'));
            }

            if (!empty($this->_json_datas['variable']['yunbiset']['isbot']) && $this->_json_datas['json']['member']['virtual_currency'] > 0 && p('yunbi')) {
                if (!empty($this->_json_datas['json']['shopset']['yunbi_title'])) {
                    $text = $this->_json_datas['json']['shopset']['yunbi_title'] . '转账';
                } else {
                    $text = '云币转账';
                }

                array_push($res['btn'], array('text'=> $text, 'url'=> '3'));
            }

            //货币
            $res['other'] = array();
            if ($this->_json_datas['json']['shopset']['credit']) {
                $text = $this->_json_datas['json']['shopset']['credit'];
            } else {
                $text = '余额';
            }
            array_push($res['other'], array('text'=> $text, 'cost'=> $this->_json_datas['json']['member']['credit2']));
            //积分
            if ($this->_json_datas['json']['shopset']['credit1']) {
                $text = $this->_json_datas['json']['shopset']['credit1'];
            } else {
                $text = '积分';
            }
            array_push($res['other'], array('text'=> $text, 'cost'=> $this->_json_datas['json']['member']['credit1']));
            if ($this->_json_datas['json']['shopset']['isyunbi']) {
                array_push($res['other'], array('text'=> $this->_json_datas['json']['shopset']['yunbi_title'], 'cost'=> $this->_json_datas['json']['member']['virtual_currency']));
            }

            array_push($res['other'],array('text' => '优惠券', 'cost' => $this->_json_datas['json']['counts']['couponcount']));

            $res['order_count'] = $this->_getOrderCount();
            $this->returnSuccess($res);
        } else {
            $this->returnError("请重新登录!");
        }


    }
    private function _getOrderCount(){
        $order_count_arr = $this->_json_datas['json']['order'];
        $res = array(
            'wait_pay'=>$order_count_arr['status0'],
            'wait_sent'=>$order_count_arr['status1'],
            'wait_delivery'=>$order_count_arr['status2'],
            'wait_refund'=>$order_count_arr['status4']
        );
        /*        order.status0 付款
        order.status1 发货
        order.status2 收货
        order.status4*/
        return $res;
    }
}
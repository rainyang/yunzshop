<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;
class Index extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        $result = $this->_getChannelBlockList();

        $commission_ok = $result['json']['channelinfo']['channel']['commission_ok'];
        $cansettle = $commission_ok >= floatval($result['json']['set']['setapplyminmoney']);
        $last_apply = $result['json']['last_apply'];
        if($commission_ok<=0 || empty($cansettle) || !empty($last_apply)){
            $withdrawal = false;    // javascript:;
        }else{
            $withdrawal = ture;    //$this->createPluginMobileUrl('channel/apply')
        }
        $json = array();
        $list = $result['list'];
        $lower_openids = $result['json']['channelinfo']['channel']['lower_openids'];
        if(!empty($lower_openids)){
            $list[] = array(
                'id'        => '7',
                'title'      => '推荐订单',
                'value'     =>'',
                'unit'      =>''
            );
        }
        $json['json'] = array(
            'avatar'            => $result['json']['member']['avatar'],
            'nickname'          => $result['json']['member']['nickname'],
            'level_name'        => $result['json']['channelinfo']['my_level']['level_name'],
            'purchase_discount' => $result['json']['channelinfo']['my_level']['purchase_discount'],
            'min_price'         => $result['json']['channelinfo']['my_level']['min_price'],
            'commission_total'  => $result['json']['channelinfo']['channel']['commission_total'],
            'commission_ok'     => $commission_ok,
            'withdrawal'        => $withdrawal,
            'list'              => $list
        );
        //dump($json);exit();
        $this->returnSuccess($json);
    }
    private function _getChannelBlockList()
    {
        $result = $this->callPlugin('channel/index/');
        $result['list'] = array(
            array(
                'id'         => 1,
                'title'      => '提现记录',
                'value'     =>$result['json']['channelinfo']['channel']['commission_total'],
                'unit'      =>'元'
            ), array(
                'id'        => '2',
                'title'      => '我的订单',
                'value'       =>$result['json']['channelinfo']['channel']['ordercount'],
                'unit'      =>'个订单'
            ), array(
                'id'        => '3',
                'title'      => '我的客户',
                'value'     =>$result['json']['channelcount'],
                'unit'      =>'人'
            )
        );

        return $result;
    }
}

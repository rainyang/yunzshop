<?php
namespace app\api\controller\cart;
@session_start();
use app\api\controller\order\Detail;
use app\api\YZ;

class Confirm extends YZ
{
    private $json;
    private $variable;


    public function __construct()
    {
        //cartids=14,13
        parent::__construct();
        $result = $this->callMobile('order/confirm');

        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        /*
         * isverify 回传
         */
        $json = $this->json;
        $variable = $this->variable;
        $is_show_dispatch_type_block = $this->_isShowDispatchTypeBlock();
        $is_show_address_block = $this->_isShowAddressBlock();



        return $this->returnSuccess($this->json);
    }
    private function _isShowDispatchTypeBlock(){
        $json = $this->json;
        if(!$this->_isVerifySend()){
            return false;
        }
        if($json['carrier_list']['length']>0){
            return true;
        }
        return false;
    }
    private function _isVerifySend(){
        $json = $this->json;
        $variable = $this->variable;
        if($variable['show'] == 1) {
            if ($json['isverify']) {
                return true;
            }
        }
        return false;
    }
    private function _isCarrier(){
        $json = $this->json;
        return $json['carrier'];
    }
    private function _getContactsBlock(){
        $json = $this->json;
        $member = $json['member'];
        if(!$this->_isCarrier()){
            return false;
        }
        $res[] = [
            'title'=>'提货人姓名',
            'text'=>$member['realname'],
        ];
        $res[] = [
            'title'=>'提货人手机',
            'text'=>$member['mobile'],
        ];
        return $res;
    }



    private function _getAddressBlock(){
        $json = $this->json;

        if($this->_isVerifySend()){
            return $this->_getVerifySendAddressBlock();
        }
        if($this->_isCarrier()){
            return $this->_getCarrierAddressBlock();
        }
        return false;
    }
    private function _getVerifySendAddressBlock()
    {
        $json = $this->json;
        $address = $json['address'];
        if($json['isverifysend'] && $json['carrier_list']['length']>0){
            return false;
        }
        $res = [
            'title'=>'收件人',
            'name'=>$address['realname'],
            'mobile'=>$address['mobile'],
            'address'=>$address['address'],
        ];
        //false时显示新建
        return $res;
    }
    private function _getCarrierAddressBlock()
    {
        $json = $this->json;
        $carrier = $json['carrier'];
        $res = [
            'title'=>'自提地点',
            'name'=>$carrier['storename'],
            'mobile'=>$carrier['tel'],
            'address'=>$carrier['address'],
        ];
        //false时显示新建
        return $res;
    }
}


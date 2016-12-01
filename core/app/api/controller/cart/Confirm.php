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
        global $_W,$_GPC;
        //cartids=14,13
        parent::__construct();
        $_GPC['cartids'] = $_GPC['cart_ids'];

        $result = $this->callMobile('order/confirm');
        $_W['ispost'] = true;
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        global $_GPC;
        //$variable = $this->variable;
        $is_show_dispatch_type_block = $this->_isShowDispatchTypeBlock();
        $contacts_block = $this->_getContactsBlock();
        $address_block = $this->_getAddressBlock();
        $this->_setDiscountWayName();
        $this->_setGoodsData();
        //dump(compact('is_show_dispatch_type_block','contacts_block','address_block'));
        $this->json['cartids'] = $_GPC['cart_ids'];
        $this->json += compact('is_show_dispatch_type_block','contacts_block','address_block');
        dump($this->json);
        return $this->returnSuccess($this->json);
    }
    private function _setDiscountWayName(){
        foreach ($this->json['order_all'] as &$order){
            foreach ($order['goods'] as &$good){
                if($good['isnodiscount'] == '0' && $this->json['haslevel']){
                    if($good['discountway']==1){
                        $good['discountwayname'] = '折扣';
                    }else{
                        $good['discountwayname'] = '立减';
                    }
                }
            }
        }
        return ;
    }
    private function _setGoodsData(){
        foreach ($this->json['order_all'] as &$order){
            $order['goods_data'] = '';
            foreach ($order['goods'] as &$good){
                $order['goods_data'].="{$good['goodsid']},{$good['optionid']},{$good['total']}|";
            }
        }
        return ;
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

        if($this->_isCarrier()){
            return $this->_getCarrierContactsBlock();
        }
        if(!$this->_isVirtual()){
            return $this->_getVirtualContactsBlock();
        }

    }
    private function _isVirtual(){
        $json = $this->json;
        $isvirtual =$json['isvirtual'];
        $goods =$json['goods'];
        if($isvirtual || $goods['type']==2){
            return true;
        }
        return false;
    }
    private function _getCarrierContactsBlock(){
        $json = $this->json;
        $member = $json['member'];
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
    private function _getVirtualContactsBlock(){
        $json = $this->json;
        $member = $json['member'];
        $res[] = [
            'title'=>'联系人姓名',
            'text'=>$member['realname'],
        ];
        $res[] = [
            'title'=>'联系人手机',
            'text'=>$member['mobile'],
        ];
        return $res;
    }

    private function _getAddressBlock(){

        if($this->_isVerifySend()){
            return $this->_getRegularAddressBlock();
        }
        if(!$this->_isVirtual()){
            return $this->_getRegularAddressBlock();
        }
        if($this->_isCarrier()){
            return $this->_getCarrierAddressBlock();
        }
        return false;
    }
    private function _getRegularAddressBlock()
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


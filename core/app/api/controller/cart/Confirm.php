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

        $isverify = $this->_isVerify();
        $address_block_list = $this->_getAddressBlockTypeId($json[''], $variable);
        dump($address_block_list);
        $resutl = $this->json;
        dump($this->json);
        return $this->returnSuccess($this->json);
    }

    private function _isVerify()
    {
        $json = $this->json;
        if ($json['isverify'] || $json['isvirtual'] || $json['goods']['type'] == 2) {
            return true;
        }
        return false;
    }

    private function _getAddressBlockTypeId($variable,$json)
    {
        if($variable['show']){
            if($json['isverifysend']){
                //<%if carrier_list.length>0%>
                if($json['carrier_list']['length']>0){
                    //todo
                }else{

                }

            }
        }
    }
}


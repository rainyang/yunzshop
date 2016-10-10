<?php
namespace app\api\controller\cart;
@session_start();
use app\api\YZ;
use app\api\Request;
use yii\helpers\ArrayHelper;

class Display extends YZ
{
    private $json;
    private $variable;
    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/cart/display');

        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }
    public function index()
    {
        //dump($this->json['list'][0]);
        //$result = ArrayHelper::;
        return $this->returnSuccess($this->json);
    }

    private function _validatePara()
    {
        $validate_fields = array(
            'uniacid' => array(
                'type' => 'required',
                'describe' => '',
            ), 'address_id' => array(
                'type' => 'required',
                'describe' => '手机号',
                'required' => false
            ),

        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
}


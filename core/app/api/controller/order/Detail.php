<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use app\api\Request;
use yii\helpers\ArrayHelper;

class Detail extends YZ
{
    private $json;
    private $variable;
    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('order/detail');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }
    public function index()
    {
        //$result = ArrayHelper::;
        $button_list = $this->_getButtonList();
        return $this->returnSuccess($this->json);
    }
    private function _getButtonList(){

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


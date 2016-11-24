<?php
namespace app\api\controller\address;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Show extends YZ
{

    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/address/display');
        $this->json = $result['json'];
    }

    public function index(){
        if(empty($this->json['list'])){
            return $this->returnError('该用户尚未添加收货地址');
        }
        $this->returnSuccess($this->json);
    }
}
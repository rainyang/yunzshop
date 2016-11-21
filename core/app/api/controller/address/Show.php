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
        $this->returnSuccess($this->json);
    }
}
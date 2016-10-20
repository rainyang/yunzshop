<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Send extends YZ
{
    private $json;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callPlugin('supplier/detail/deal/confirmsend');
        dump($result);exit;
        $this->json = $result;
    }
    public function index(){
        return $this->returnSuccess($this->json);
    }
}
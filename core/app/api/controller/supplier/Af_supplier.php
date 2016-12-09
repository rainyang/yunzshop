<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Af_supplier extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('supplier/af_supplier');
        $this->returnSuccess($result);
    }
}
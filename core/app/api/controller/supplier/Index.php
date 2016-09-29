<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Supplier extends YZ
{

    public function __construct()
    {
        parent::__construct();
        echo 1;exit();
    }

    public function index()
    {
        $result = $this->callPlugin('supplier/orderj');
        //$this->variable = $result['variable'];
        $this->returnSuccess($result);
    }

    public function order()
    {
        $result = $this->callPlugin('supplier/orderj/order');
        //$this->variable = $result['variable'];
        $this->returnSuccess($result);
    }
}
<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Index extends YZ
{

    public function __construct()
    {
        parent::__construct();
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

    public function logg()
    {
        $result = $this->callPlugin('supplier/logg');
        //$this->variable = $result['variable'];
        $this->returnSuccess($result);
    }

    public function applyg()
    {
        $result = $this->callPlugin('supplier/applyg');
        //$this->variable = $result['variable'];
        $this->returnSuccess($result);
    }

    public function detail()
    {
        $result = $this->callPlugin('supplier/detail');

        echo '<pre>';print_r($result);exit;
        //$this->variable = $result['variable'];
        $this->returnSuccess($result);
    }
}
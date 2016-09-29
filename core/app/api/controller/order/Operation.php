<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use Illuminate\Support\Arr;

class Operation extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('order/op/display');

        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index(){
        $button_id = '';
        switch ($button_id){
            case 1:
                $route = 'order/op/cancel';
                break;
        }
        $result = $this->callMobile($route);

    }

}


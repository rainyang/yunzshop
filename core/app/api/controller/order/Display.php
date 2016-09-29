<?php
namespace app\api\controller\order;
@session_start();
use app\api\YZ;
use Illuminate\Support\Arr;

class Display extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('order/list/display');

        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        /*$order_list = $this->json['list'];

        $a = Arr::pluck($order_list, 'goods');
        dump($a);
        exit;*/
        return $this->returnSuccess($this->json);
    }

}


<?php
namespace app\api\controller\cart;
@session_start();
use app\api\YZ;

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

}


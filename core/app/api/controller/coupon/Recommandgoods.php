<?php
namespace app\api\controller\coupon;
@session_start();
use app\api\YZ;

class  Recommandgoods extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/util/recommand');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }

    public function index()
    {
        $this->returnSuccess($this->json);
    }
}
//数组list
//循环遍历之后的变量(字段名)
//商品标题；title
//商品id；id
//商品图片：thumnb
//商品现价：marketprice
//商品现价：productprice


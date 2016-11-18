<?php
namespace app\api\controller\category;
@session_start();
use app\api\YZ;
use app\api\Request;
use yii\helpers\ArrayHelper;

class Index extends YZ
{
    private $json;
    private $variable;
    public function __construct()
    {
        parent::__construct();
        $result = $this->callMobile('shop/util/category');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
    }
    
    public function index()
    {  
        return $this->returnSuccess($this->json);
    }
}


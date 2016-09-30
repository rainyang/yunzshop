<?php
namespace app\api\controller\ranking;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Index extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $result = $this->callPlugin('ranking/ranking');
        $this->returnSuccess($result);
    }
 
}
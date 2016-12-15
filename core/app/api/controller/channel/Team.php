<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Team extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('channel/team');
        //echo "<pre>"; print_r($result);exit;
        $this->returnSuccess($result);
    }
}

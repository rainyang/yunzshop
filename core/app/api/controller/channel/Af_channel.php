<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Af_channel extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('channel/af_channel');
        $this->returnSuccess($result);
    }
    public function hasApplied(){
        $result = $this->callPlugin('channel/af_channel');
        $this->returnSuccess(array('is_channel'=>(string)$result['json']['is_channel']));

    }
}
<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Log extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('channel/log');
        $result['nav'] = array(
        	'all' => array(
        		'status' => '',
        		'text' => '全部'
        	),'audit' => array(
        		'status' => '0',
        		'text' => '待审核'
        	),'money' => array(
        		'status' => '1',
        		'text' => '已打款'
        	));
        $this->returnSuccess($result);
    }
}

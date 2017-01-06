<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;

class IndexOrder extends YZ
{
	private $json;
	private $variable;
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    	$this->json = $this->callPlugin('channel/index/order');

    	$nav = $this->_getChannelBlockNav();
    	$this->json['json']['nav'] = $nav;
        $this->returnSuccess($this->json);
    }
    private function _getChannelBlockNav()
    {
        $nav = array(
            array(
                'id'		=> 1,
                'status'	=> '',
                'title'		=>'全部'
            ), array(
                'id'		=> 2,
                'status'	=> 1,
                'title'		=>'待发货'
            ), array(
                'id'		=> 3,
                'status'	=> 2,
                'title'		=>'待收货'
            ), array(
                'id'		=> 4,
                'status'	=> 3,
                'title'		=>'已完成'
            )
        );

        return $nav;
    }
}

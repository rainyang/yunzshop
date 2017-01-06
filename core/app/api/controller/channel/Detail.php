<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;

class Detail extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json = $this->callPlugin('channel/detail');
        $nav = $this->_getChannelBlockNav();
        $this->json['nav'] = $nav;
        $this->returnSuccess($this->json);
    }
    private function _getChannelBlockNav()
    {
        $nav = array(
            array(
                'id'		=> 1,
                'status'	=> '1',
                'value'		=>'采购'
            ), array(
                'id'		=> 2,
                'status'	=> 2,
                'value'		=>'出货'
            ), array(
                'id'		=> 3,
                'status'	=> 3,
                'value'		=>'零售'
            ), array(
                'id'		=> 4,
                'status'	=> 4,
                'value'		=>'自提'
            )
        );

        return $nav;
    }
}

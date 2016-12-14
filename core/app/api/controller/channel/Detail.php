<?php
namespace app\api\controller\channel;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

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
        $nav = [
            [
                'id'		=> 1,
                'status'	=> '1',
                'value'		=>'采购'
            ], [
                'id'		=> 2,
                'status'	=> 2,
                'value'		=>'出货'
            ], [
                'id'		=> 3,
                'status'	=> 3,
                'value'		=>'零售'
            ], [
                'id'		=> 4,
                'status'	=> 4,
                'value'		=>'自提'
            ]
        ];

        return $nav;
    }
}

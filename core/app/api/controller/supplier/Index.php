<?php
namespace app\api\controller\supplier;
@session_start();
use app\api\YZ;
use app\api\controller\order;
use yii\helpers\ArrayHelper;

class Index extends YZ
{
    private $json;

    public function __construct()
    {
        parent::__construct();
        $result = $this->callPlugin('supplier/orderj');
        $this->json = $result;
    }

    public function index()
    {
        $block_list = $this->_getSupplierBlockList();
    }

    private function _getSupplierBlockList()
    {
        $member = $this->json['member'];
        $set = p('commission')->getSet();
        $list = [
            [
                'id' => 1,
                'icon' => '',
                'title' => $set['texts']['commission1'],
                'value' => $member['commission_total'],
                'unit' => '元'
            ], [
                'id' => 2,
                'icon' => '',
                'title' => $set['texts']['order'],
                'value' => $member['ordercount0'],
                'unit' => '个'
            ], [
                'id' => 3,
                'icon' => '',
                'title' => $set['texts']['commission_detail'],
                'value' => '',
                'unit' => $set['texts']['commission_detail']
            ], [
                'id' => 4,
                'icon' => '',
                'title' => $set['texts']['myteam'],
                'value' => $member['agentcount'],
                'unit' => '个'
            ], [
                'id' => 5,
                'icon' => '',
                'title' => $set['texts']['mycustomer'],
                'value' => $member['customercount'],
                'unit' => '人'
            ], [
                'id' => 6,
                'icon' => '',
                'title' => '二维码',
                'value' => '',
                'unit' => '推广二维码'
            ],
        ];

        return $list;
    }
}
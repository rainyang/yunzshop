<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;

class Index extends YZ
{
    private $model;
    private $set;
    private $json;
    public function __construct()
    {
        parent::__construct();
        //$commission_model = new \admin\api\model\commission();
        //$this->model = $commission_model;
        $this->set = p('commission')->getSet();
        $this->json = $this->getJson('commission/index');
    }

    public function index()
    {
        //$json = $this->getJson('commission/index');
        //$set = p('commission')->getSet();
        $member = $this->json['member'];
        $member['can_withdraw'] = true;//todo 假数据
        $block_list = $this->_getBlockList($member);
        $res = ['block_list'=>$block_list];
        $res += array_part('commission_total,agentcount,agenttime,commission_ok,can_withdraw',$member);
        $this->returnSuccess($res);
    }
    private function _getBlockList($member)
    {
        $list = [
            [
                'id' => 1,
                'icon' => '',
                'title' => $this->set['texts']['commission1'],
                'value' => $member['commission_total'],
                'unit' => '元'
            ], [
            'id' => 2,
            'icon' => '',
            'title' => $this->set['texts']['order'],
            'value' => $member['ordercount'],
            'unit' => '个'
        ], [
            'id' => 3,
            'icon' => '',
            'title' => $this->set['texts']['commission_detail'],
            'value' => $this->set['texts']['commission_detail'],
            'unit' => '个'
        ], [
            'id' => 4,
            'icon' => '',
            'title' => $this->set['texts']['myteam'],
            'value' => $member['agentcount'],
            'unit' => '个'
        ]
        ];
        return $list;
    }
}
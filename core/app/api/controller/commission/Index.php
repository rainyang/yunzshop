<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;

class Index extends YZ
{
    private $json;
    private $variable;

    public function __construct()
    {
        parent::__construct();
        //$commission_model = new \admin\api\model\commission();
        //$this->model = $commission_model;
        $result = $this->callPlugin('commission/index/index');
        $this->variable = $result['variable'];
        $this->json = $result['json'];
        //dump($this->variable);
    }

    public function index()
    {
        //$json = $this->getJson('commission/index');
        //$set = p('commission')->getSet();
        $member = $this->json['member'];
        if($this->json['commission_ok']>0 && $this->json['commission_ok']>= $this->json['set']['withdraw']){
            $member['can_withdraw'] = true;
        }
        $block_list = $this->_getBlockList();
        $res = array('block_list' => $block_list);
        $res += array_part('commission_total,agentcount,agenttime,commission_ok,can_withdraw', $member);
        $this->returnSuccess($res);
    }

    private function _getBlockList()
    {
        $list = $this->_getCommissionBlockList();
        /*$block_list_2 = $this->_getBonusBlockList();
        $list = ArrayHelper::merge($block_list_1, $block_list_2);*/
        return $list;
    }

    private function _getBonusBlockList()
    {
        $bonus_set = $this->variable['bonus_set'];
        $member_bonus = $this->variable['member_bonus'];

        $list = array();
        //dump($this->variable['bonus']);
        if ($this->variable['bonus'] != 1) {
            return $list;
        }
        $list = array(
            array(
                'id' => 8,
                'icon' => '',
                'title' => $bonus_set['texts']['commission'],
                'value' => $member_bonus['commission_total'],
                'unit' => '元'
            ), array(
                'id' => 9,
                'icon' => '',
                'title' => $bonus_set['texts']['order'],
                'value' => $member_bonus['ordercount0'],
                'unit' => '个'
            ), array(
                'id' => 10,
                'icon' => '',
                'title' => $bonus_set['texts']['order_area'],
                'value' => $member_bonus['ordercount_area0'],
                'unit' => '个'
            ),
        );
        return $list;
    }

    private function _getCommissionBlockList()
    {
        $member = $this->json['member'];
        $set = p('commission')->getSet();
        $list = array(
            array(
                'id' => 1,
                'icon' => '',
                'title' => $set['texts']['commission1'],
                'value' => $member['commission_total'],
                'unit' => '元'
            ), array(
                'id' => 2,
                'icon' => '',
                'title' => $set['texts']['order'],
                'value' => $member['ordercount0'],
                'unit' => '个'
            ), array(
                'id' => 3,
                'icon' => '',
                'title' => $set['texts']['commission_detail'],
                'value' => '',
                'unit' => $set['texts']['commission_detail']
            ), array(
                'id' => 4,
                'icon' => '',
                'title' => $set['texts']['myteam'],
                'value' => $member['agentcount'],
                'unit' => '个'
            ), array(
                'id' => 5,
                'icon' => '',
                'title' => $set['texts']['mycustomer'],
                'value' => $member['customercount'],
                'unit' => '人'
            ), array(
                'id' => 6,
                'icon' => '',
                'title' => '二维码',
                'value' => '',
                'unit' => '推广二维码'
            ),
        );

        return $list;
    }
}
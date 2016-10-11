<?php
namespace app\api\controller\bonus;
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
        //$commission_model = new \admin\api\model\commission();
        //$this->model = $commission_model;
        $result = $this->callPlugin('bonus/index');
        $this->json = $result['json'];
        //dump($this->variable);
    }

    public function index()
    {
        $navs = $this->_getBonusBlockList();
        $this->json['set']['texts']['levelname'] = "";
        if(!empty($this->json['level'])){
            $this->json['set']['texts']['levelname'] .= "[" . $this->json['level']['levelname'] . "]";
        }
        if(!empty($this->json['member']['bonus_area'])){
            if($this->json['member']['bonus_area'] == 1){
                $this->json['set']['texts']['levelname'] .= "[" . $this->json['set']['texts']['agent_province'] . "]";
            }else if($this->json['member']['bonus_area'] == 2){
                $this->json['set']['texts']['levelname'] .= "[" . $this->json['set']['texts']['agent_city'] . "]";
            }else if($this->json['member']['bonus_area'] == 3){
                $this->json['set']['texts']['levelname'] .= "[" . $this->json['set']['texts']['agent_district'] . "]";
            }
        }
        $this->json['navs'] = $navs;
        $this->returnSuccess($this->json);
    }

    private function _getBonusBlockList()
    {
        $member = $this->json['member'];
        $set = $this->json['set'];
        $list = array(
            array(
                'id' => 1,
                'icon' => '',
                'title' => $set['texts']['commission'],
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
                'title' => $set['texts']['order_area'],
                'value' => $member['ordercount_area'],
                'unit' => '个'
            ), array(
                'id' => 4,
                'icon' => '',
                'title' => $set['texts']['commission_detail'],
                'value' => '',
                'unit' => $set['texts']['commission'].'明细'
            ), array(
                'id' => 4,
                'icon' => '',
                'title' => $set['texts']['mycustomer'],
                'value' => $member['agentcount'],
                'unit' => '个'
            ), array(
                'id' => 5,
                'icon' => '',
                'title' => $set['texts']['mycustomer'],
                'value' => $member['customercount'],
                'unit' => '人'
            )
        );

        return $list;
    }
}
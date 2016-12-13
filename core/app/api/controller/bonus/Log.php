<?php
namespace app\api\controller\bonus;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Log extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('bonus/log');
        $result['json']['set']['texts']['commission_detail'] = $result['json']['set']['texts']['commission_detail'] . "(" . $result['json']['total'] . ")";
        $result['json']['set']['texts']['commission_yj'] = "总".$result['json']['set']['texts']['commission'] . "：+" . $result['json']['commissioncount'] . "元";

        foreach ($result['json']['list'] as $key => &$val) {
            
            if($val['isglobal'] == 1){
                $typename = "全球分红";
            }else{
                if($val['type'] == 2){
                    $typename = "团队分红";
                }else if($val['type'] == 3){
                    $typename = "地区分红";
                }else{
                    $typename = "团队地区分红";
                }
            }
        	$val['applyno'] = "编号：" . $val['applyno'] . $val['applyno'] > 0 ? '（微信钱包）' : '（余额）' . $typename;
            $val['commission_sq'] = $result['json']['set']['texts']['commission'] . "：" . $val['commission'] . " " . "分红时间" . "：" . $val['dealtime'];
        	$val['commission_pay'] = "+" . $val['commission_pay'];
        }
        $result['json']['navs'] = array();
        $result['json']['navs'][] = array("text" => "全部明细", "status" => "1");
        if(!empty($agentLevel)){
            $result['json']['navs'][] = array("text" => "团队明细", "status" => "2");
        }
        if(!empty($member['bonus_area'])){
            $result['json']['navs'][] = array("text" => "地区明细", "status" => "3");
        }
        $this->returnSuccess($result);
    }
}
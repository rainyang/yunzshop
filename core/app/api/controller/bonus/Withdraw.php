<?php
namespace app\api\controller\bonus;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Withdraw extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('bonus/withdraw');
        $result['json']['content']['settledays'] = "买家确认收货后，立即获得{$result['json']['set']['texts']['commission1']}。";
        $result['json']['content']['settledays'] .= $result['json']['set']['settledays'] > 0 ? "结算期（{$result['json']['set']['settledays']}天）后，{$result['json']['set']['texts']['commission']}可{$result['json']['set']['texts']['withdraw']}。结算期内，买家退货，{$result['json']['set']['texts']['commission']}将自动扣除。" : "";
        $result['json']['content']['consume_withdraw'] = $result['json']['set']['consume_withdraw'] > 0 ? "注意： 自己购买的完成订单，共计 <span style='color:red'>{$result['json']['set']['consume_withdraw']}</span> 元后才能申请{$result['json']['set']['texts']['withdraw']}" : "";
        $this->returnSuccess($result);
    }
}
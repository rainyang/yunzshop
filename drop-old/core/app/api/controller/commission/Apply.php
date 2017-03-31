<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;

class Apply extends YZ
{


    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        global $_GPC;
        if(isset($_GPC['type'])){
            $this->submit();
        }
        $result = $this->callPlugin('commission/Apply');
        $result['json']['set']['texts']['commission_apply_title'] = $result['json']['set']['texts']['commission'] . "提现";
        $result['json']['set']['texts']['commission_ok_title'] = "当前" . $result['json']['texts']['commission_ok'] . "(元)";
        $result['json']['set']['texts']['widthdraw_log'] = "提现记录";
        $result['json']['buttons'] = array();
        if (empty($result['json']['set']['closetocredit'])) {
           $result['json']['buttons'][] =  array(
                'id' => 1, 
                'text' => $result['json']['set']['texts']['widthdraw']."到账户余额",
                'type' => 0,
                );
        }

        if (empty($result['json']['set']['closetowechatwallet'])) {
           $result['json']['buttons'][] =  array(
                'id' => 2, 
                'text' => $result['json']['set']['texts']['widthdraw']."到微信钱包",
                'type' => 1,
                );
        }

        if ($result['json']['settingalipay']['pay']['weixin'] == 1 && $result['json']['settingalipay']['pay']['weixin_withdrawals'] == 1) {
            if ($result['json']['commission_ok'] >= 1 && $result['json']['commission_ok'] <= 200){
               $result['json']['buttons'][] =  array(
                    'id' => 3, 
                    'text' => $result['json']['set']['texts']['widthdraw']."到微信红包",
                    'type' => 2,
                    );
           }
        }

        if ($result['json']['settingalipay']['pay']['alipay'] == 1  &&  $settingalipay['pay']['alipay_withdrawals']=='1') {
           $result['json']['buttons'][] =  array(
                'id' => 4, 
                'text' => $result['json']['set']['texts']['widthdraw']."到支付宝",
                'type' => 3,
                );
        }
        $this->returnSuccess($result);
    }
    public function submit(){
        global $_W;
        $_W['ispost'] = true;
        $result = $this->callPlugin('commission/Apply');
        $this->returnSuccess($result['json']);
    }
}
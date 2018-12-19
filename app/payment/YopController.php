<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 9:10
 */

namespace app\payment;

use app\common\components\BaseController;
use app\common\models\AccountWechats;
use Illuminate\Support\Facades\DB;

class YopController extends BaseController
{
    protected $set;

    protected  $parameters;

    public function __construct()
    {
        parent::__construct();

        $this->set = DB::table('yz_yop_setting')->where('app_key', $_REQUEST['customerIdentification'])->first();

        if (empty($this->set)) {
          exit('应用AppKey不存在');
        }
        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }
        $this->init();
    }

    private function init()
    {
        $yop_data = $_REQUEST['response'];
        if ($yop_data) {
            $response = \Yunshop\YopPay\common\Util\YopSignUtils::decrypt($yop_data, $this->set['private_key'], $this->set['yop_public_key']);
            $this->parameters = json_decode($response, true);
        }
    }

    protected function yopLog($desc,$error,$data)
    {
        \Yunshop\YopPay\common\YopLog::yopLog($desc, $error,$data);
    }

    protected function yopResponse($desc,$params, $type = 'unify')
    {
        \Yunshop\YopPay\common\YopLog::yopRequest($desc, $params, $type);
    }
}
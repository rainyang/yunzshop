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

        if (!app('plugins')->isEnabled('yop-pay')) {
            echo 'Not turned on yop pay';
            exit();
        }

        $this->set = $this->getMerchantNo();

        if (empty(\YunShop::app()->uniacid)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->set['uniacid'];
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
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

    protected function getMerchantNo()
    {
        \Log::debug('--------------易宝入网参数--------------', $_REQUEST);

        $app_key = $_REQUEST['customerIdentification'];
        $merchant_no = substr($app_key,  strrpos($app_key, 'OPR:')+4);

        $model = DB::table('yz_yop_setting')->where('parent_merchant_no', $merchant_no)->first();

        if ($model) {
            return [
                'uniacid' =>$model->uniacid,
                'merchant_number' => $model->parent_merchant_no,
                'private_key' => $model->private_key,
                'yop_public_key' => $model->yop_public_key,
            ];
        }

        $model = DB::table('yz_yop_setting')->where('merchant_no', $merchant_no)->first();


        if (empty($model)) {
            exit('商户不存在');
        }

        return [
            'uniacid' =>$model->uniacid,
            'merchant_number' => $model->merchant_no,
            'private_key' => $model->son_private_key,
            'yop_public_key' => $model->yop_public_key,
        ];
    }

    protected function yopLog($desc,$error,$data)
    {
        \Yunshop\YopPay\common\YopLog::yopLog($desc, $error,$data);
    }

    protected function yopResponse($desc,$params, $type = 'unify')
    {
        \Yunshop\YopPay\common\YopLog::yopResponse($desc, $params, $type);
    }

}
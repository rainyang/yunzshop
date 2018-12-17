<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/12
 * Time: 9:09
 */

namespace app\payment\controllers;

use app\payment\YopController;
use app\common\models\AccountWechats;
use Yunshop\YopPay\models\SubMerchant;

class YopmerchantController extends YopController
{

    public function notifyUrl()
    {
        \Log::debug('--------------易宝入网--------------', $this->parameters);
        $son = SubMerchant::withoutGlobalScope('is_son')->where('requestNo',$this->parameters['requestNo'])->first();

        if (empty($son)) {
            exit('Merchant does not exist');
        }
        $status = $this->merNetInStatus();

        $son->status = $status;
        $son->externalId = $this->parameters['externalId'];
        $son->remark = $this->parameters['remark']?:'';
        $bool = $son->save();
        if ($bool) {
            echo 'SUCCESS';
            exit();
        } else {
            echo '保存出错';
            exit();
        }
    }

    protected function merNetInStatus()
    {
        $status = SubMerchant::INVALID;
        if (!empty($this->parameters['merNetInStatus'])) {
            switch ($this->parameters['merNetInStatus']) {
                case 'PROCESS_SUCCESS': //审核通过
                    $status = SubMerchant::PROCESS_SUCCESS;
                    break;
                case 'PROCESS_REJECT': //审核拒绝
                    $status = SubMerchant::PROCESS_REJECT;
                    break;
                case 'PROCESS_BACK': //审核回退
                    $status = SubMerchant::PROCESS_BACK;
                    break;
                case 'PROCESSING_PRODUCT_INFO_SUCCESS': //审核中-产品提前开通
                    $status = SubMerchant::PROCESSING_PRODUCT_INFO_SUCCESS;
                    break;
                default:
                    break;
            }
        }

        return $status;
    }
}
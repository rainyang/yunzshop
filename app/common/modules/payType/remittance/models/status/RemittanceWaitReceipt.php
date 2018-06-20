<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 上午9:50
 */
namespace app\common\modules\payType\remittance\models\status;

use app\common\models\Status;

class RemittanceWaitReceipt extends Status
{
    public function onCreated(){
        dd('todo 继续写已汇款逻辑');
        exit;

    }

}
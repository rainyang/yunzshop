<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;
use app\common\models\order\Express;

class OrderSend extends ChangeStatusOperation
{
    protected $status_before_change = [ORDER::WAIT_SEND];
    protected $status_after_changed = ORDER::WAIT_RECEIVE;
    protected $name = '发货';
    protected $past_tense_class_name = 'OrderSent';
    protected function _updateTable(){
        $db_express_model = new Express();
        $db_express_model->order_id = \YunShop::request()->order_id;
        $db_express_model->express_code = \YunShop::request()->express_code;
        $db_express_model->express_company_name = \YunShop::request()->express_company_name;
        $db_express_model->express_sn = \YunShop::request()->express_sn;
        $db_express_model->save();
        parent::_updateTable();
    }
}
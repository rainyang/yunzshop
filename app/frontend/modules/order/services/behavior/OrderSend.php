<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Order;
use app\common\models\order\Express;
use Illuminate\Support\Facades\Validator;

class OrderSend extends ChangeStatusOperation
{
    protected $statusBeforeChange = [ORDER::WAIT_SEND];
    protected $statusAfterChanged = ORDER::WAIT_RECEIVE;
    protected $name = '发货';
    protected $time_field = 'send_time';

    protected $past_tense_class_name = 'OrderSent';

    /**
     * @return bool|void
     * @throws AppException
     */
    protected function updateTable()
    {

        if ($this->dispatch_type_id == DispatchType::EXPRESS) {
            //实体订单
            $validator = Validator::make(request()->all(), [
                'express_code' => 'required',
                'express_company_name' => 'required',
                'express_sn' => 'required',
            ]);
            if ($validator->fails()) {
                throw new AppException($validator->errors()->first());
            }            $order_id = request()->input('order_id');

            $db_express_model = Express::where('order_id', $order_id)->first();

            !$db_express_model && $db_express_model = new Express();

            $db_express_model->order_id = $order_id;
            $db_express_model->express_code = request()->input('express_code');
            $db_express_model->express_company_name = request()->input('express_company_name');
            $db_express_model->express_sn = request()->input('express_sn');
            $db_express_model->save();
        }
        parent::updateTable();
    }
}
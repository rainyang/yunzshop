<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/2/28
 * Time: 上午11:21
 * comment: 订单删除
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderDelete extends OrderOperation
{
    protected $status_before_change = [ORDER::CLOSE, ORDER::COMPLETE];
    //protected $status_after_changed = -1;
    protected $name = '删除';
    protected $past_tense_class_name = 'OrderDeleted';

    /**
     * 覆盖父类的更新表方法
     * @return int
     */
    protected function _updateTable()
    {
        return $this->order_model->destroy($this->order_model->id);
    }


}
<?php

namespace app\frontend\modules\order\observers;

use app\backend\modules\goods\models\Discount;
use app\backend\modules\goods\models\Share;
use app\backend\modules\goods\services\DiscountService;
use app\backend\modules\goods\services\Privilege;
use app\backend\modules\goods\services\PrivilegeService;
use app\common\models\Goods;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * User: luckystar_D
 * Date: 2017/2/28
 * Time: 上午11:24
 */
class OrderAddressObserver extends \app\common\observers\BaseObserver
{

    private $data = 0;

    public function saving(Model $model)
    {
        dump($model);

        //dd($model);exit;
    }


    public function saved(Model $model)
    {

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:02
 */

namespace app\backend\modules\goods\observers;


class GoodsCategoryObserver extends \app\common\observers\BaseObserver
{
    public function saving(Model $model)
    {

        if (is_null($model->id)) {
            (new \app\common\services\operation\GoodsCategoryLog($model, 'create'));
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/13
 * Time: 5:07 PM
 */

namespace app\frontend\modules\order\models;

use app\common\models\BaseModel;

class Trade extends BaseModel
{
    public function toArray()
    {
        $attributes = array_merge($this->getAttributes(), $this->getPreAttributes());

        $this->setAppends($attributes);
        return parent::toArray();
    }

    public function getPreAttributes()
    {
    }
}
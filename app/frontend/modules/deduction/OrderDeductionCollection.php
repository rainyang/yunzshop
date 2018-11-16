<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/15
 * Time: 4:04 PM
 */

namespace app\frontend\modules\deduction;

use app\framework\Database\Eloquent\Collection;

class OrderDeductionCollection extends Collection
{
    public function push($value)
    {

        parent::push($value);
        asort($this->items);
        return $this;
    }
}
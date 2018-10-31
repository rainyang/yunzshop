<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/31
 * Time: 14:38
 */

namespace app\backend\modules\charts\models;


use app\common\models\BaseModel;

class OrderIncomeEveryday extends BaseModel
{
    public $table = 'yz_order_income_everyday';
    protected $guarded = [''];

}
<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/27
 * Time: 下午1:52
 */

namespace app\common\models;

use app\backend\models\BackendModel;

class Income extends BackendModel
{
    public $table = 'yz_member_income';

    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];

    /**
     * @param $id
     * @return mixed
     */
    public static function getIncomeFindId($id)
    {
        return self::find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getIncomeById($id)
    {
        return self::uniacid()
        ->where('id',$id);
    }

    /**
     * @return mixed
     */
    public static function getIncomes()
    {
       return self::uniacid();
    }
    
    
}
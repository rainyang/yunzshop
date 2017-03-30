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
            ->where('id', $id);
    }

    /**
     * @return mixed
     */
    public static function getIncomes()
    {
        return self::uniacid();
    }

    public static function getIncomeInMonth()
    {
        $model = self::select('create_month');
        $model->uniacid();
        $model->with(['hasManyIncome' => function ($query) {
            $query->select('id', 'create_month', 'type_name', 'amount', 'created_at');
            $query->get();
        }]);
        $model->groupBy('create_month');
        $model->orderBy('create_month', 'desc');
        return $model;
    }

    public static function getDetailById($id)
    {
        $model = self::uniacid();
        $model->select('detail');
        $model->where('id', $id);
        return $model;
    }


    public static function updatedIncomeWithdraw($type, $typeId, $status)
    {
//        var_dump($type);
//        echo "--";
//        var_dump($typeId);exit;
        return self::where('type', 'commission')
            ->where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('type_id', ['2'])
            ->update(['status' => $status]);
//        ->get();
    }

    public function hasManyIncome()
    {
        return $this->hasMany(self::class, "create_month", "create_month");
    }


}
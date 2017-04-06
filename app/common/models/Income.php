<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/27
 * Time: 下午1:52
 */

namespace app\common\models;

use app\backend\models\BackendModel;
use app\backend\modules\finance\services\IncomeService;

class Income extends BackendModel
{
    public $table = 'yz_member_income';

    public $timestamps = true;

    public $widgets = [];

    public $attributes = [];

    protected $guarded = [];

    public $StatusService;

    protected $appends = ['status_name'];

    /**
     * @return mixed
     */
    public function getStatusService()
    {
        if (!isset($this->StatusService)) {

            $this->StatusService = IncomeService::createStatusService($this);
        }
        return $this->StatusService;
    }

    /**
     * @return mixed
     */
    public function getStatusNameAttribute()
    {
        return $this->getStatusService();
    }

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
     * @param $ids
     * @return mixed
     */
    public static function getIncomeByIds($ids)
    {
        return self::uniacid()
            ->whereIn('id', explode(',', $ids));
    }
    

    public function incometable()
    {
        return $this->morphTo();
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

    public static function getWithdraw($type, $typeId, $status)
    {
        return self::where('type', 'commission')
            ->where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['status' => $status]);
    }

    public static function updatedWithdraw($type, $typeId, $status)
    {
        return self::where('member_id', \YunShop::app()->getMemberId())
            ->whereIn('id', explode(',', $typeId))
            ->update(['status' => $status]);
    }

    public function hasManyIncome()
    {
        return $this->hasMany(self::class, "create_month", "create_month");
    }


}
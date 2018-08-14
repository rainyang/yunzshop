<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/11 下午3:30
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\exceptions\AppException;
use app\frontend\modules\withdraw\models\Income;
use app\frontend\modules\withdraw\models\Withdraw;

class DataValidatorService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    /**
     * @var array
     */
    private $income_set;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        $this->income_set = $this->incomeSet();
    }


    /**
     * @throws AppException
     */
    public function validator()
    {
        $type_name = $this->withdrawModel->type_name;

        $amount = $this->withdrawModel->amounts;
        if (bccomp($amount, 1, 2) == -1) {
            throw new AppException("{$type_name}提现金额不能小于1元");
        }

        $real_amount = $this->getIncomeAmount();
        if (bccomp($amount, $real_amount, 2) != 0) {
            throw new AppException("{$type_name}提现金额错误！");
        }

        $roll_out_limit = $this->getRollOutLimit();
        if (bccomp($amount, $roll_out_limit, 2) == -1) {
            throw new AppException("{$type_name}提现金额不能小于{$roll_out_limit}元");
        }

        $outlay = bcadd($this->withdrawModel->poundage, $this->withdrawModel->servicetax, 2);
        $result_amount = bcsub($amount, $outlay, 2);

        if (bccomp($result_amount, 1, 2) == -1) {
            throw new AppException("{$type_name}扣除手续费、劳务税金额不能小于1元");
        }
    }


    /**
     * @return float
     */
    private function getIncomeAmount()
    {
        $type_ids = $this->withdrawModel->type_id;

        //->whereStatus(Income::STATUS_INITIAL)
        return Income::whereIn('id', explode(',', $type_ids))->whereStatus(Income::STATUS_INITIAL)->sum('amount');
    }


    /**
     * @return float
     */
    private function getRollOutLimit()
    {
        return $this->getIncomeSet('roll_out_limit');
    }


    /**
     * @param $key
     * @return float
     */
    private function getIncomeSet($key)
    {
        $result = array_get($this->income_set, $key, '0');
        return empty($result) ? '0' : $result;
    }


    /**
     * @return array
     */
    private function incomeSet()
    {
        return $this->withdrawModel->income_set;
    }

}

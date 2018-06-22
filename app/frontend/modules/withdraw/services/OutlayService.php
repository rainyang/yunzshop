<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/11 下午2:24
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\frontend\modules\withdraw\models\Withdraw;

class OutlayService
{
    /**
     * @var array
     */
    private $income_set;


    /**
     * @var array
     */
    private $withdraw_set;


    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        $this->income_set = $this->incomeSet();
        $this->withdraw_set = $this->withdrawSet();
    }


    /**
     * @return float
     */
    public function getPoundageRate()
    {
        return $this->getIncomeSet('poundage_rate');
    }


    /**
     * @return float
     */
    public function getPoundage()
    {
        $rate = $this->getPoundageRate();
        $amount = $this->getWithdrawAmount();

        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    public function getServiceTaxRate()
    {
        return $this->getWithdrawSet('servicetax_rate');
    }


    /**
     * @return float
     */
    public function getServiceTax()
    {
        $rate = $this->getServiceTaxRate();

        $withdraw_amount = $this->getWithdrawAmount();
        $withdraw_poundage = $this->getPoundage();

        $amount = bcsub($withdraw_amount, $withdraw_poundage, 2);

        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    public function getToBalancePoundageRate()
    {
        return $this->getWithdrawSet('special_poundage');
    }


    /**
     * @return float
     */
    public function getToBalancePoundage()
    {
        $rate = $this->getToBalancePoundageRate();
        $amount = $this->getWithdrawAmount();

        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    public function getToBalanceServiceTaxRate()
    {
        return $this->getWithdrawSet('special_service_tax');
    }


    /**
     * @return float
     */
    public function getToBalanceServiceTax()
    {
        $rate = $this->getToBalanceServiceTaxRate();
        $amount = $this->getWithdrawAmount();

        return $this->calculate($amount, $rate);
    }


    /**
     * @return float
     */
    private function getWithdrawAmount()
    {
        return $this->withdrawModel->amounts;
    }


    /**
     * Calculate
     *
     * @param $amount
     * @param $rate
     * @return float
     */
    private function calculate($amount, $rate)
    {
        return bcdiv(bcmul($amount, $rate, 2), 100, 2);
    }


    private function getIncomeSet($key)
    {
        $result = array_get($this->income_set, $key, '0.00');

        return empty($result) ? '0.00' : $result;
    }


    /**
     * @param $key
     * @return string
     */
    private function getWithdrawSet($key)
    {
        $result = array_get($this->withdraw_set, $key, '0.00');

        return empty($result) ? '0.00' : $result;
    }


    /**
     * @return array
     */
    private function withdrawSet()
    {
        return $this->withdrawModel->withdraw_set;
        //return Setting::get('withdraw.income');
    }


    private function incomeSet()
    {
        return $this->withdrawModel->income_set;
        /*$mark = $this->withdrawModel->mark;
        if (!$mark) {
            throw new AppException('Mark error!');
        }
        if (!empty($this->withdrawModel->income_set)) {
            return $this->withdrawModel->income_set;
        }
        return Setting::get('withdraw.' . $mark);*/
    }

}

<?php
namespace app\frontend\modules\finance\models;
use app\common\models\Income;
use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/30
 * Time: 上午9:40
 */

class Withdraw extends \app\common\models\Withdraw
{
    public $Incomes;
    protected $appends = ['incomes'];

    public static function getWithdrawLog($status)
    {
        $withdrawModel = self::select('id', 'type_name', 'amounts', 'poundage', 'status', 'created_at');

        $withdrawModel->uniacid();

        $withdrawModel->where('member_id', \YunShop::app()->getMemberId());
        if ($status >= '0') {
            $withdrawModel->where('status', $status);
        }
        return $withdrawModel;
    }

    public static function getWithdrawInfoById($id)
    {
        $withdrawModel = self::select('id', 'withdraw_sn', 'pay_way', 'type', 'type_id', 'type_name', 'amounts', 'poundage', 'status', 'created_at', 'actual_amounts', 'actual_poundage');
        $withdrawModel->uniacid();
        $withdrawModel->where('id', $id);


        return $withdrawModel;
    }

    public function getIncomesAttribute()
    {

        if (!isset($this->Incomes)) {
            $configs = Config::get('income');
            foreach ($configs as $key => $config) {
                if ($config['class'] === $this->type) {
                    $incomes = Income::getIncomeByIds($this->type_id)
                        ->select('id', 'incometable_type','incometable_id')
                        ->get();
                    foreach ($incomes as $key => $income) {
                        $this->Incomes[$key] = $income->incometable->toArray();
                    }
                }


            }
        }
        return $this->Incomes;
    }

}
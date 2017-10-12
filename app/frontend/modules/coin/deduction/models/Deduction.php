<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:14
 */

namespace app\frontend\modules\coin\deduction\models;


use app\common\models\BaseModel;
use app\common\models\VirtualCoin;

class Deduction extends BaseModel
{
    protected $table = 'yz_deduction';
    private $setting;

    // todo 初始化setting
    public function coin()
    {
        return $this->hasOne(VirtualCoin::class, 'code', 'code');
    }

    public function valid()
    {
        return app('DeductionManager')->make('GoodsDeductionManager')->bound($this->getCode());
    }

    public function getName()
    {
        return $this->coin->name;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function newCoin()
    {
        return app('CoinManager')->make($this->getCode());
    }

    public function getSetting()
    {
        if (isset($this->setting)) {
            return $this->setting;
        }
        return $this->setting = app('DeductionManager')->make('DeductionSettingManager')->make($this->getCode());
    }

    public function isEnableDeductDispatchPrice()
    {
        return $this->getSetting()->isEnableDeductDispatchPrice();
    }
}
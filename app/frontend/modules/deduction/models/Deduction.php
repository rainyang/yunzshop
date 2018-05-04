<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/6
 * Time: 上午10:14
 */

namespace app\frontend\modules\deduction\models;


use app\common\models\BaseModel;
use app\common\models\VirtualCoin;
use app\frontend\modules\deduction\DeductionSettingCollection;

/**
 * Class Deduction
 * @package app\frontend\modules\deduction\models
 * @property int id
 * @property int code
 */
class Deduction extends BaseModel
{
    protected $table = 'yz_deduction';
    private $settingCollection;
    /**
     * @var VirtualCoin
     */
    private $coin;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function valid()
    {
        return app('DeductionManager')->make('GoodsDeductionManager')->bound($this->getCode());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->getCoin()->getName();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getCoin()
    {
        if (isset($this->coin)) {
            return $this->coin;
        }
        return $this->coin = app('CoinManager')->make($this->getCode());
    }

    /**
     * @return bool | DeductionSettingCollection
     */
    public function getSettingCollection()
    {
        if (isset($this->settingCollection)) {
            return $this->settingCollection;
        }
        if (!app('DeductionManager')->make('DeductionSettingManager')->bound($this->getCode())) {
            return false;
        }
//        dd(app('DeductionManager')->make('DeductionSettingManager')->make($this->getCode()));
//        exit;

        return $this->settingCollection = app('DeductionManager')->make('DeductionSettingManager')->make($this->getCode())->getDeductionSettingCollection();
    }

    public function isEnableDeductDispatchPrice()
    {
        if (!$this->getSettingCollection()) {
            return false;
        }
        return $this->getSettingCollection()->isEnableDeductDispatchPrice();
    }
}
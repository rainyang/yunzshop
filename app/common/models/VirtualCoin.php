<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/10
 * Time: 上午11:30
 */

namespace app\common\models;


abstract class VirtualCoin extends BaseModel
{
    protected $table = 'yz_virtual_coin';
    protected $attributes = [
        'amountOfCoin' => 0,
        'amountOfMoney' => 0,
    ];
    public $name;
    public $code;
    protected $exchange_rate;

    function __construct($attribute = [])
    {
        parent::__construct($attribute);
        $this->exchange_rate = $this->getExchangeRate();
        $this->name = $this->getName();
        $this->code = $this->getCode();
    }

    public function getCode()
    {
        return isset($this->code) ? $this->code : $this->code = $this->_getCode();
    }

    public function getName()
    {
        return isset($this->name) ? $this->name : $this->name = $this->_getName();
    }

    public function getExchangeRate()
    {
        return isset($this->exchange_rate) ? $this->exchange_rate : $this->exchange_rate = $this->_getExchangeRate();
    }

    abstract protected function _getExchangeRate();

    abstract protected function _getName();

    abstract protected function _getCode();

    /**
     * @param VirtualCoin $coin
     * @return VirtualCoin
     */
    public function plus(VirtualCoin $coin)
    {
        return (new static())->setMoney($this->amountOfMoney + $coin->getMoney());
    }

    public function setCoin($amount)
    {
        $this->amountOfMoney = $amount * $this->exchange_rate;
        return $this;
    }

    public function setMoney($amount)
    {

        $this->amountOfMoney = $amount;
        return $this;
    }

    public function toArray()
    {
        $this->amountOfCoin = sprintf('%.2f', $this->getCoin());

        $this->amountOfMoney = sprintf('%.2f', $this->getMoney());

        return parent::toArray();
    }

    public function getCoin()
    {
        return $this->amountOfCoin = $this->amountOfMoney / $this->exchange_rate;
    }

    public function getMoney()
    {
        return $this->amountOfMoney;
    }

    public function save(array $options = [])
    {
        return true;
    }
}
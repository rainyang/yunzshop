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
        'name' => ''
    ];

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
        $this->amountOfMoney += $coin->getMoney();
        return $this;
    }

    public function setCoin($amount)
    {

        $this->amountOfCoin = $amount;
        return $this;
    }

    public function setMoney($amount)
    {

        $this->amountOfMoney = $amount;
        return $this;
    }

    public function toArray()
    {
        $this->amountOfCoin = $this->getCoin();

        $this->amountOfMoney = $this->getMoney();

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
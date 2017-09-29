<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/10
 * Time: 上午11:30
 */

namespace app\common\models;


class VirtualCoin extends BaseModel
{
    protected $table = 'yz_virtual_coin';

    protected $attributes = [
        'amountOfCoin' => 0,
        'amountOfMoney' => 0,
        'name' => ''
    ];
    
    protected $exchange_rate;

    /**
     * @param VirtualCoin $coin
     * @return VirtualCoin
     */
    public function plus(VirtualCoin $coin){
        $this->amountOfMoney += $coin->getMoney();
        return $this;
    }

    public function setCoin($amount){

        $this->amountOfCoin = $amount;
        return $this;
    }
    public function setMoney($amount){

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
//
//        if(isset($this->amountOfMoney)){
//        }
//
//        return $this->amountOfMoney = $this->amountOfCoin * $this->exchange_rate;
    }
}
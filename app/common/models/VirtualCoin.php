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
    private $amountOfCoin;
    private $amountOfMoney;
    public function setAmountOfCoin($amount){
        $this->amountOfCoin = $amount;
    }
    public function setMountOfMoney($amount){
        $this->amountOfMoney = $amount;
    }
    public function getAmountOfCoin()
    {
        if(isset($this->amountOfCoin)){
            return $this->amountOfCoin;
        }
        return $this->amountOfCoin = $this->amountOfMoney / $this->exchange_rate;
    }

    public function getAmountOfMoney()
    {
        if(isset($this->amountOfMoney)){
            return $this->amountOfMoney;
        }
        return $this->amountOfMoney = $this->amountOfCoin * $this->exchange_rate;
    }
}
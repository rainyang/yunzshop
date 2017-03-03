<?php
namespace mobile\order\demo\model;
class MoneyOffPrice extends Price
{
    function getPriceCode(){
        return Goods.MONEY_OFF;
    }
}
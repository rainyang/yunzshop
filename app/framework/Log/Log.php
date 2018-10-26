<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/26
 * Time: 上午11:01
 */
namespace app\framework\Log;

class Log
{
    public function coupon($key,$value){
        file_put_contents($this->getFileName('coupon'),"{$key}:{$value}".PHP_EOL, FILE_APPEND);

    }
    public function deduction($key,$value){
        file_put_contents($this->getFileName('deduction'),"{$key}:{$value}".PHP_EOL, FILE_APPEND);
    }
    private function getFileName($name){
        dd(storage_path("logs/debug/{$name}.log"));
        return storage_path("logs/debug/{$name}.log");
    }
}
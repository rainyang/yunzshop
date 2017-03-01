<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午2:45
 */

namespace app\common\ServiceModel;


class ServiceModel
{
    private $_initial_data;


    public function __construct($db_goods)
    {
        $this->_initial_data = $db_goods;
    }
    public function getInitialData(){
        return $this->_initial_data;
    }

    public function __get($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }
        if(isset($this->_initial_data[$name])){
            return $this->_initial_data[$name];
        }
        return null;
    }
}
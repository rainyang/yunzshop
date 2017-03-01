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

    public function __construct()
    {
    }

    public function __get($name)
    {
        echo '----';
        var_dump($name);

        echo '----';
        if(isset($this->$name)){
            return $this->$name;
        }

        return null;
    }
}
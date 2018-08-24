<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午5:01
 */

namespace app\frontend\modules\order\operations;


interface OrderOperationInterface
{
    public function enable();

    /**
     * @return string
     */
    public function getName();
    /**
     * @return string
     */
    public function getValue();

    /**
     * @return string
     */
    public function getApi();

}
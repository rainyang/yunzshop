<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/18
 * Time: 上午10:00
 */

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\frontend\modules\order\services\OrderService;

class ChangePrice extends BaseController
{
    private $_params;
    public function __construct()
    {
        parent::__construct();
        $this->_params = \YunShop::request()->get();
    }

    public function index()
    {
        list($result,$message) = OrderService::changeOrderPrice($this->_params);
        if($result === false){
            return $this->errorJson($message);
        }
        return $this->successJson($message);
    }
}
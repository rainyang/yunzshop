<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 18/04/2017
 * Time: 15:12
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\services\JsonRpc;
use app\common\services\Session;

class TestController extends BaseController
{
    public function index()
    {

        $result = (new JsonRpc())->client('plus',['user'=>'1','pass'=>2]);
        dd($result);
    }

    public function test()
    {
        $url = 'aHR0cDovL3Rlc3QueXVuenNob3AuY29tL2FkZG9ucy95dW5fc2hvcC8jL2dvb2RzLzE1Mj9pPTImdHlwZT0xJm1pZD0yMDQmc2hhcmVfdGFnPTI';

        $url = base64_decode($url);

        $url = explode('&', $url);
        foreach ($url as $item) {
            $splits[] = explode('=', $item);
        }

        $str = 'http://test.yunzshop.com/addons/yun_shop/#/goods/152?i=2&type=1&mid=204&share_tag=2';

        preg_match('/.+mid=(\d+).+/', $str, $matches);

        echo '<pre>';print_r($matches);exit;

        echo '<pre>';print_r($splits);exit;
    }
}
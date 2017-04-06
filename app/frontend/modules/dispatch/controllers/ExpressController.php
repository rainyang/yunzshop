<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/6
 * Time: 下午4:03
 */
class ExpressController extends \app\common\components\ApiController
{
    public function index()
    {
        Request::get();
        $order_id = 1;
    }
    function getExpress($express, $expresssn)
    {
        $url = sprintf(SZ_YI_EXPRESS_URL, $express, $expresssn, time());
        //$url = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$express}&fromWeb=null&postid={$expresssn}";
        load()->func('communication');
        $resp = ihttp_request($url);
        $content = $resp['content'];

        if (empty($content)) {
            return array();
        }

        $content = json_decode($content);

        return $content->data;
    }
}
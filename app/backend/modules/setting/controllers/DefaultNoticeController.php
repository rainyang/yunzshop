<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/16
 * Time: 15:55
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;

class DefaultNoticeController extends BaseController
{
    public function index() {
        //template_id_short = OPENTM207509450 积分消息通知
        $notice_name = \YunShop::request()->notice_name;
        dd($notice_name);
    }
}
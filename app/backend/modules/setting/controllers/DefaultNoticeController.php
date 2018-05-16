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
        $natice_name = \YunShop::request()->notice_name;
    }
}
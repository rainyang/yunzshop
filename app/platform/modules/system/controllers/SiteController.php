<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/27
 * Time: 10:53
 */
namespace app\platform\models\system\controllers;

use app\platform\controllers\BaseController;

class SiteController extends BaseController
{
    public function index()
    {
        $set = \Setting::get('system.site');
        $set_data = request()->setdata;
        if ($set_data) {
            if (!\Setting::set('system.site', $set_data)) {
                return $this->message('失败', '', 'error');
            }
            return $this->message('成功', '');
        }

        return view('system.site', [
            'set' => $set
        ]);
    }
}
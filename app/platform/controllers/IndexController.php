<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午9:49
 */

namespace app\platform\controllers;

class IndexController extends BaseController
{

    public function index()
    {
        $role = 0;

        $user = \Auth::guard('admin')->user();

        if (1 == $user->uid) {
            $role = 1;
        }

        $data = [
            'username' => $user->username,
            'role' => $role,
            'avatar' => $user->hasOneProfile->avatar
        ];

        return $this->successJson('成功', $data);
    }
}

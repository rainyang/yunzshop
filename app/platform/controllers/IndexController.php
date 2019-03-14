<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午9:49
 */

namespace app\platform\controllers;



use Illuminate\Support\Facades\Cookie;

class IndexController extends BaseController
{

    public function index()
    {
        dd(\YunShop::app()->uniacid);
        $user = \Auth::guard('admin')->user();

        Cookie::queue('user_id', $user->uid);
        Cookie::queue('user_username', $user->username);

        return $this->successJson('', ['user' => $user]);
    }
}
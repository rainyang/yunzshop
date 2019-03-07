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
        $user = \Auth::guard('admin')->user();

        Cookie::queue('user_id', $user->id);
        Cookie::queue('user_name', $user->name);

        return response()->json([
            'message' => 'Successful created',
            'data'    => ['user' => $user, 'token' => 'wwww']
        ]);
    }
}
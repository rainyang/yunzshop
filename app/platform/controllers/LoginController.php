<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/15
 * Time: 下午6:56
 */

namespace app\platform\controllers;


use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';
    protected $username;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
   //     $this->middleware('guest:admin', ['except' => 'logout']);
    }
    /**
     * 重写登录视图页面
     * @return [type]                   [description]
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }
    /**
     * 自定义认证驱动
     * @return [type]                   [description]
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    /**
     * 重写验证字段.
     *
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $this->guard('admin')->logout();

        request()->session()->flush();

        request()->session()->regenerate();

        return $this->successJson('成功', []);
    }

    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return response()
            ->json(['token' => 'yyy']);

    }
}
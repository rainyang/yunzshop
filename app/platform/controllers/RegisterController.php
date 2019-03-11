<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/20
 * Time: 上午11:09
 */

namespace app\platform\controllers;

use app\common\exceptions\AdminException;
use app\platform\modules\user\models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
         register as traitregister;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     //   $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return \Validator::make($data, [
            'name' => 'required|max:255|unique:admin_users',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * 重定义注册页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('admin.auth.register');
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
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return AdminUser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        try {
            $this->traitregister($request);
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }
}
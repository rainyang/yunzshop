<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午12:37
 */

namespace app\platform\modules\user\controllers;


use app\common\events\UserActionEvent;
use app\platform\controllers\BaseController;
use app\platform\modules\user\models\AdminUser as User;
use app\platform\modules\user\models\AdminUser;
use app\platform\modules\user\models\Role;
use app\platform\modules\user\requests\AdminUserCreateRequest;
use app\platform\modules\user\requests\AdminUserUpdateRequest;
use Illuminate\Http\Request;
use app\platform\modules\user\models\YzUserProfile;

class AdminUserController extends BaseController
{
    protected $fields = [
        'name' => '',
        'phone' => '',
        'roles' => [],
    ];

    /**
     * Display a listing of the resource.(显示用户列表.)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parames = \YunShop::request();
        if (strpos($parames['search']['searchtime'], '×') !== FALSE) {
            $search_time = explode('×', $parames['search']['searchtime']);

            if (!empty($search_time)) {
                $parames['search']['searchtime'] = $search_time[0];

                $start_time = explode('=', $search_time[1]);
                $end_time = explode('=', $search_time[2]);

                $parames->times = [
                    'start' => $start_time[1],
                    'end' => $end_time[1]
                ];
            }

            $list = User::searchUsers($parames);

            dd($list);
        }

        $users = User::getList();

        if (!$users->isEmpty()) {
            return $this->successJson('成功', $users);
        } else {
            return $this->errorJson('未获取到用户信息');
        }
    }

    /**
     * Show the form for creating a new resource And Store a newly created resource in storage.(添加用户)
     *
     */
    // @return \Illuminate\Http\Response
    public function create()
    {
        $user = request()->user;
        if ($user) {
            $this->validate($this->rules(), $user, $this->message());
            return User::saveData($user, $user_model = '');
        }
    }

    /**
     * Show the form for editing the specified resource And Update the specified resource in storage.(修改用户)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|mixed
     */
    public function edit()
    {
        $uid = request()->uid;
        if (!$uid) {
            return $this->errorJson('参数错误');
        }
        $user = AdminUser::getData($uid);
        $profile = YzUserProfile::where('uid', $uid)->first();
        $user['mobile'] = $profile['mobile'];
        if (!$user || !$profile) {
            return $this->errorJson('找不到该用户');
        }
        $data = request()->user;

        if($data) {
            $this->validate($this->rules($uid, $profile['id']), $data, $this->message());
            return AdminUser::saveData($data, $user);
        }

        if ($user) {
            return $this->successJson('成功', $user);
        } else {
            return $this->errorJson('失败');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uid)
    {
        $tag = User::find((int)$uid);
        foreach ($tag->roles as $v) {
            $tag->roles()->detach($v);
        }
        if ($tag && $tag->$uid != 1) {
            $tag->delete();
        } else {
            return redirect()->back()
                ->withErrors("删除失败");
        }

        return redirect()->back()
            ->withSuccess("删除成功");
    }

    public function validate(array $rules, \Request $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            echo $this->errorJson($validator->errors()->all()); exit;
        }
    }

    public function rules($u_id, $p_id)
    {
        $rules = [];
        if (request()->path() == "admin/user/create") {
            $rules = [
                'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u|unique:yz_admin_users',
                'mobile' => 'required|regex:/^1[34578]\d{9}$/|unique:yz_users_profile',
            ];
        }

        if (request()->path() == "admin/user/edit") {
            $rules = [
                'username' => 'required|regex:/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]{3,30}$/u|unique:yz_admin_users,username,'.$u_id.',uid',
                'mobile' => 'required|regex:/^1[34578]\d{9}$/|unique:yz_users_profile,mobile,'.$p_id,
            ];
        }

        if (request()->path() != "admin/user/edit") {
            $rules['password'] = 'required';
            $rules['re_password'] = 'same:password';
        }
        return $rules;
    }

    public function message()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.regex' => '用户名格式不正确',
            'username.unique' => '用户名已存在',
            'mobile.required' => '手机号已存在',
            'mobile.regex' => '手机号格式不正确',
            'mobile.unique' => '手机号已存在',
            'password.required' => '密码不能为空',
            're_password.same' => '两次密码不一致',
        ];
    }

    /**
     * 修改状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $uid = request()->uid;
        $status = request()->status;
        if (!$uid || !$status) {
            return $this->errorJson('参数错误');
        }
        $result = AdminUser::where('uid', $uid)->update(['status'=>$status]);
        if ($result) {
            return $this->successJson('成功');
        } else {
            return $this->errorJson('失败');
        }
    }

    /**
     * 修改密码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function change()
    {
        $uid = request()->uid;
        $data = request()->user;
        if (!$uid || !$data) {
            return $this->errorJson('参数错误');
        }
        if ($data){
            $user = AdminUser::getData($uid);
            return AdminUser::saveData($data, $user);
        }

        return view('system.user.change');
    }

    /**
     * 单个用户平台列表
     */
    public function applicationList()
    {
        $uid = request()->uid;
    }
}


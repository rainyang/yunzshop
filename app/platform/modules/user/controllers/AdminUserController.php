<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/8
 * Time: 11:51
 */

namespace app\platform\modules\user\controllers;


use app\platform\controllers\BaseController;
use app\platform\modules\user\models\AdminUser;
use app\common\exceptions\AppException;

class AdminUserController extends BaseController
{
    /**
     * 显示用户列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = AdminUser::getList();

        return view('system.user.index', [
            'users' => $users
        ]);
    }

    /**
     * 添加用户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function add()
    {
        $user = request()->user;
        if ($user) {
            return AdminUser::saveData($user, $user_model = '');
        }

        return view('system.user.add', [
        ]);
    }

    /**
     * 用户修改
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|mixed
     */
    public function edit()
    {
        $id = request()->id;
        if (!$id) {
            return $this->errorJson('参数错误');
        }
        $user = AdminUser::getData($id);
        $data = request()->user;

        if($data) {
            return AdminUser::saveData($data, $user);
        }

        return view('system.user.add', [
            'user' => $user
        ]);
    }

    /**
     * 修改状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $id = request()->id;
        $status = request()->status;
        $result = AdminUser::where('id', $id)->update(['status'=>$status]);
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
        $id = request()->id;
        $data = request()->user;
        if ($data){
            $user = AdminUser::getData($id);
            return AdminUser::saveData($data, $user);
        }

        return view('system.user.change');
    }

    /**
     * 单个用户平台列表
     */
    public function applicationList()
    {
        $id = request()->id;
    }
}
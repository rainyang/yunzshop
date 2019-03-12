<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/3/8
 * Time: 11:51
 */

namespace app\platform\modules\user\controllers;


use app\platform\controllers\BaseController;
use app\platform\modules\user\models\AdminUser2;

class AdminUserController extends BaseController
{
    /**
     * 显示用户列表
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

            $list = AdminUser2::searchUsers($parames);

            dd($list);
        }

        $users = AdminUser2::getList();

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
            return AdminUser2::saveData($user, $user_model = '');
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
        $user = AdminUser2::getData($id);
        $data = request()->user;

        if($data) {
            return AdminUser2::saveData($data, $user);
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
        $result = AdminUser2::where('id', $id)->update(['status'=>$status]);
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
            $user = AdminUser2::getData($id);
            return AdminUser2::saveData($data, $user);
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
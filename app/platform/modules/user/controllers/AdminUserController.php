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
    public function index()
    {
        $users = AdminUser::getList();

        foreach ($users as $item) {
            $item['create_at'] = $item['created_at']->format('Y-m-d');
            if ($item['effective_time'] == 0) {
                $item['effective_time'] = '永久有效';
            }else {
                if (time() < $item['effective_time']) {
                    $item['status'] = 1;
                    AdminUser::where('id', $item['id'])->update(['status'=>1]);
                }
                $item['effective_time'] = date('Y-m-d', $item['effective_time']);
            }
        }

//        dd($users['0']->create_at);

        return view('system.user.index', [
            'users' => $users
        ]);
    }

    public function add()
    {
        $user = request()->user;
        if ($user) {
            return AdminUser::saveData($user);
        }

        return view('system.user.add', [
//            'user' => $user
        ]);
    }

    public function edit()
    {
        $id = request()->id;
        if (!$id) {
            return $this->errorJson('参数错误');
        }
        $this->check($id);
        $user = AdminUser::getData($id);
        $data = request()->user;


        if($data) {
            return AdminUser::saveData($data, $user);
        }

        return view('system.user.add', [
            'user' => $user
        ]);
    }

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
}
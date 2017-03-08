<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;
use app\common\models\user\User;
use app\common\models\user\YzRole;

class RoleController extends BaseController
{
    /**
     * 角色列表
     */
    public function index()
    {
        dd(\YunShop::app());
        return view('user.role.index',[])->render();
    }

    /**
     * 创建角色
     */
    public function add()
    {
        $model = new YzRole();
        $permissions = \Config::get('route');
        $permissions = User::getAllPermissions();

        dd(\Yunshop::app()->getRoutes());
        return view('user.role.form',[
            'model'=>$model,
            'permissions'=>$permissions,
        ])->render();
    }

    /**
     * 修改角色
     */
    public function edit()
    {
        return view('user.role.form',[])->render();
    }

    /**
     * 删除角色
     */
    public function del()
    {

    }
}
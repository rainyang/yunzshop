<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\user\User;
use app\common\models\user\YzRole;

class RoleController extends BaseController
{
    /**
     * 角色列表
     */
    public function index()
    {

        return view('user.role.index',[
            'pager'      => 1,
            'roleList'  => 2
        ])->render();
    }

    /**
     * 创建角色
     */
    public function store()
    {
        $roleModel = new YzRole();
        $permissions = \Config::get('menu');
        $userPermissons = User::getAllPermissions();

        $requestRole = \YunShop::request()->YzRole;
        if ($requestRole) {
            $roleModel->setRawAttributes($requestRole);
            $roleModel->uniacid = \YunShop::app()->uniacid;

            $validator = YzRole::validator($roleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($roleModel->save()) {
                    return $this->message('添加角色成功', Url::absoluteWeb('user.role.index'));
                } else {
                    $this->error('数据写入出错，请重试！');
                }
            }
        }
        //dd($roleModel);

        //dd(\Yunshop::app()->getRoutes());
        return view('user.role.form',[
            'roleModel'=>$roleModel,
            'permissions'=>$permissions,
            'userPermissons'=>$userPermissons,
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
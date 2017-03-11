<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\user\User;
use app\common\models\user\YzPermission;
use app\common\models\user\YzRole;

class RoleController extends BaseController
{
    /**
     * 角色列表
     */
    public function index()
    {
        $pageSize = '3';

        $roleList = YzRole::getRoleList($pageSize);
        $pager = PaginationHelper::show($roleList->total(), $roleList->currentPage(), $roleList->perPage());
        //dd($roleList);
        return view('user.role.index',[
            'pager'     => $pager,
            'roleList'  => $roleList
        ])->render();
    }

    /**
     * 搜索
     */
    public function search()
    {
        dd(111);
    }

    /**
     * 创建角色
     */
    public function store()
    {
        $permissions = \Config::get('menu');
        $userPermissons = User::getAllPermissions();

        $roleModel = new YzRole();
        $permissionsModel = new YzPermission();

        $requestRole = \YunShop::request()->YzRole;
        //dd($requestRole);
        if ($requestRole) {
            //将数据赋值到model
            $roleModel->setRawAttributes($requestRole);
            //其他字段赋值
            $roleModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = YzRole::validator($roleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages()); 
            }else{
                //$roleId = $roleModel->createRole($roleModel->toArray());
                if ($roleModel->save()) {
                    $requestPermission = \YunShop::request()->perms;
                    $requestPermission = is_array($permissionsModel) ? $requestPermission : array();
                    var_dump($requestPermission);
                    //数据处理
                    $data = [];
                    foreach ($requestPermission as $key => $value) {
                        $data[$key] = array(
                            'type' => YzPermission::TYPE_ROLE,
                            'item_id' => $roleModel->id,
                            'permission' => $value,
                            'created_at' => $roleModel->created_at,
                            'updated_at' => $roleModel->updated_at
                        );
                    }
                    //缺少数据验证
                    //dd($data);
                    $result = YzPermission::createPermission($data);
                    if ($result) {
                        return $this->message('添加角色成功', Url::absoluteWeb('user.role.index'));
                    } else {
                        //删除刚刚添加的角色
                        YzRole::deleteRole($roleModel->id);
                        $this->error('权限数据写入出错，请重试！');
                    }
                } else {
                    $this->error('角色数据写入出错，请重试！');
                }
            }


        }

        return view('user.role.form',[
            'roleModel'=>$roleModel,
            'permissions'=>$permissions,
            'userPermissons'=>$userPermissons,
        ])->render();
    }

    /**
     * 修改角色
     */
    public function update()
    {
        $requestRole = YzRole::getRoleById(\YunShop::request()->id);
        dd($requestRole);
        return view('user.role.form',[])->render();
    }

    /**
     * 删除角色
     */
    public function destory()
    {
        $requestRole = YzRole::getRoleById(\YunShop::request()->id);
        //dd($requestRole);
        if (!$requestRole) {
            $this->error('未找到数据或已删除');
        }
        $resultRole = YzRole::deleteRole(\YunShop::request()->id);
        if ($resultRole) {
            $resultPermission = YzPermission::deleteRole(\YunShop::request()->id);
            if ($resultPermission) {
                return $this->message('删除角色成功。', Url::absoluteWeb('user.role.index'));
            }
            //是否需要怎么增加角色权限删除失败提示
        } else {
            $this->error('数据写入出错，请重试！');
        }
    }
}
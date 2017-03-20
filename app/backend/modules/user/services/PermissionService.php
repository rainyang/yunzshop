<?php

/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/17
 * Time: 下午7:03
 */
namespace app\backend\modules\user\services;
class PermissionService
{
    /*
     *  添加权限所属类型及所属人ID到权限数组$data
     *
     *  @parms array $data
     *
     *  @return array
     **/
    public function addedToPermission($data =[], $type, $itemId)
    {
        foreach ($data as $key => $value) {
            $permissions[] = array(
                'type' => $type,
                'item_id' => $itemId,
                'permission' => $value
            );
        }
        return $permissions;
    }

    /*
     *  处理权限数组：读取权限数组，返回一维权限数组$permissions
     *
     *  @parms array $data
     *
     *  @return array
     **/
    public function handlePermission($data)
    {
        if (!is_array($data)) {
            return $permissions = [];
        }
        $permissions = [];
        foreach ($data as $key => $value) {
            if ($key['permission']) {
                $permissions[] = $key['permission'];
            }
            if ($value['permission']) {
                $permissions[] = $value['permission'];
            }
        }
        return $permissions;
    }
}

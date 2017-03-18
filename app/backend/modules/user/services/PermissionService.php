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
            $permission[] = array(
                'type' => $type,
                'item_id' => $itemId,
                'permission' => $value
            );
        }
        return $permission;
    }
}

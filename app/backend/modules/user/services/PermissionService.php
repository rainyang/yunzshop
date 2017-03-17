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
    public function addedToPermission($data =[], $type, $itemId)
    {
        //dd($data);
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

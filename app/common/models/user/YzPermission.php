<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:28
 */

namespace app\common\models\user;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class YzPermission extends BaseModel
{
    //use SoftDeletes;

    const TYPE_USER = 1;
    const TYPE_ROLE = 2;
    const TYPE_ACCOUNT = 3;

    public $table = 'yz_permission';

    public static function addYzPermission(array $data = [])
    {
        return static::insert($data);
    }

    public function relationValidator($data)
    {
        $this->fill($data);
        return $this->validator();
    }

    /**
     * Delete role permissions by roleId
     *
     * @param int $roleId
     * @return \mysqli_result
     */
    public static function deleteRolePermission($roleId)
    {
        return static::where('type', '=', static::TYPE_ROLE)->where('item_id', $roleId)->delete();
    }


}

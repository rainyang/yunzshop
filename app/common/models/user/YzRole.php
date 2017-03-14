<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 07/03/2017
 * Time: 10:40
 */

namespace app\common\models\user;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class YzRole extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_role';

    /**
     *  定义字段名
     * 可使
     * @return array */
    public static function atributeNames() {
        return [
            'name'=> '角色名称',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public static function rules() {
        return [
            'name' => 'required',
        ];
    }

    //这个好像没有用，应该可以删除，待确认
    public function UserPermistions()
    {
        return $this->hasMany('app\common\models\user\YzPermission');
    }

    public function roleUser()
    {
        return $this->hasMany('app\common\models\user\YzUserRole', 'role_id');
    }

    public function rolePermission()
    {
        return $this->hasMany('app\common\models\user\YzPermission', 'item_id');
    }

    /**
     * @param int $pageSize
     * @return object
     */
    public static function getRoleList($pageSize)
    {
        return static::uniacid()
            ->with(['roleUser'])
            ->paginate($pageSize);
    }

    /**
     * Get full role information and role permissions By roleId
     *
     * @param int $roleId
     * @return object
     */
    public static function getRoleById($roleId)
    {
        return static::where('id', $roleId)
            ->with(['rolePermission' => function($query) {
                return $query->select('id', 'item_id','permission')
                    ->where('type', '=', YzPermission::TYPE_ROLE);
            }])
            ->first();
    }

    /**
     * @param int $roleId
     * @return \mysqli_result
     */
    public static function deleteRole($roleId)
    {
        return static::where('id', $roleId)->delete();
    }


}
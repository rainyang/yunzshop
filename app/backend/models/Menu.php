<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 09/03/2017
 * Time: 10:52
 */

namespace app\backend\models;


use app\common\models\BaseModel;

class Menu extends BaseModel
{
    public $table = 'yz_menu';

    /**
     * 父菜单与子菜单栏目1:n关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 子菜单与父菜单1:1关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 获取菜单栏目
     *
     * @param $parent_id
     * @param int $child_switch
     * @return mixed
     */
    public static function getMenuAllInfo($parent_id = 0, $child_switch = 1)
    {
        $result = self::select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id', 'sort', 'status'])
                   ->where('parent_id', $parent_id)
                   ->where('status', 1)
                   ->orderBy('sort', 'asc');

        if ($child_switch) {
            $result = $result->with(['childs'=>function ($query) {
                return $query->select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id', 'sort', 'status'])->where('status', 1)->orderBy('sort', 'asc');
            }]);
        }

        return $result;
    }

    /**
     * 通过ID获取菜单栏目
     *
     * @param $id
     * @return mixed
     */
    public static function getMenuInfoById($id)
    {
        return self::select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id', 'sort', 'status'])
            ->where('id', $id)
            ->with(['parent'=>function ($query) {
                return $query->select(['id', 'name']);
            }]);
    }
}
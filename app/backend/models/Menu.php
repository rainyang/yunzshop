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

    public function childs()
    {
        return $this->hasMany('app\backend\models\Menu','parent_id','id');
    }

    public static function getMenuAllInfo()
    {
        return self::select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id'])
                   ->where('prarent_id', 0)
                   ->where('status', 1)
                   ->with(['childs'=>function ($query) {
                       return $query->select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id'])
                           ->with(['childs'=>function ($query2) {
                            return $query2->select(['id', 'name', 'item', 'url', 'url_params', 'permit', 'menu', 'icon', 'parent_id']);
                           }]);
                   }]);
    }
}
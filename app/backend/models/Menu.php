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
}
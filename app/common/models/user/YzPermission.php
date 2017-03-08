<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 02/03/2017
 * Time: 18:28
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class YzPermission extends BaseModel
{
    const TYPE_USER = 1;
    const TYPE_ROLE = 2;
    const TYPE_ACCOUNT = 3;

    public $table = 'yz_permission';



}
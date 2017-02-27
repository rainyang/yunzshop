<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 24/02/2017
 * Time: 16:36
 */

namespace app\common\models;


use app\common\traits\ValidatorTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use ValidatorTrait;
}
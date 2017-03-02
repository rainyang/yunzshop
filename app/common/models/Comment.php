<?php
namespace app\common\models;

use app\backend\models\BackendModel;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:07
 */
class Comment extends BackendModel
{
    public $table = 'yz_comment';

    protected $guarded = [''];

    protected $fillable = [''];
}
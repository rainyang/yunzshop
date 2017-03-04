<?php
namespace app\common\models;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:07
 */
class Comment extends BaseModel
{
    public $table = 'yz_comment';
    
    protected $guarded = [''];
    protected $fillable = [''];

    public function subComments()
    {
        return $this->hasMany('app\common\models\Comment','comment_id','id');
    }

    public function getImagesAttribute($value)
    {
        return $value ? unserialize($value) : $value;
    }


}
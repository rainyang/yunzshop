<?php
namespace app\common\models;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:07
 */
class Comment extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_comment';
    
    protected $guarded = [''];
    protected $fillable = [''];

    public function subComments()
    {
        return $this->hasMany('app\common\models\Comment','comment_id','id');
    }

//    public function getImagesAttribute($value)
//    {
//        return is_array($value) ? iserializer($value) : $value;
//    }


}
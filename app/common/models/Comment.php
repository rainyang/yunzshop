<?php
namespace app\common\models;

use app\backend\modules\goods\services\CommentService;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/27
 * Time: 下午5:07
 */

use Illuminate\Support\Facades\DB;

class Comment extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_comment';

    public $Reply;
    public $Append;
    public $TypeName;

    protected $appends = ['type_name', 'reply', 'append'];

    protected $guarded = [''];
    protected $fillable = [''];

    public function hasManyReply()
    {
        return $this->hasMany('app\common\models\Comment', 'comment_id', 'id');
    }


    public static function getOrderGoodsComment()
    {
        return self::uniacid();
    }


    public function getReplyAttribute()
    {
        if (!isset($this->Reply)) {

            $this->Reply = static::getReplyById($this->id);
        }
        return $this->Reply;
    }

    public static function getReplyById($id)
    {
        return self::uniacid()
            ->where('comment_id', $id)
            ->where('uid', '<>', DB::raw('reply_id'))
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getAppendAttribute()
    {
        if (!isset($this->Append)) {

            $this->Append = static::getAppendById($this->id);
        }
        return $this->Append;
    }

    public static function getAppendById($id)
    {
        return self::uniacid()
            ->where('comment_id', $id)
            ->where('uid', DB::raw('reply_id'))
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTypeNameAttribute()
    {
        if (!isset($this->TypeName)) {

            $this->TypeName = CommentService::getTypeName($this->type);
        }
        return $this->TypeName;
    }

}
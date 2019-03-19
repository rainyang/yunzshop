<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/17/017
 * Time: 15:05
 */

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;

class CoreAttach extends BaseModel
{
    protected $table = 'yz_core_attachment';
    protected $guarded = [''];
    protected $hidden  = ['deleted_at', 'updated_at', 'created_at'];
    public $timestamps = true;

    public function scopeSearch($query, $keyword)
    {
        if ($keyword['year']) {
//            $query = $query->where(explode('/', 'attachment'), )
        }
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号id',
            'uid' => '用户id',
            'filename' => '原文件名',
            'attachment' => '新文件名',
        ];
    }
    public function rules()
    {
        return [
            'uniacid' => 'integer',
            'uid' => 'integer',
            'filename' => 'string|max:50',
            'attachment' => '',
        ];
    }
}
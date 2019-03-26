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
    protected $hidden  = ['deleted_at', 'updated_at'];
    public $timestamps = true;

    public function scopeSearch($query, $keyword)
    {
        if ($keyword['year']) {

            $query = $query->whereBetween(
                'created_at', 
                [
                    mktime(0,0,0, $keyword['month'] ? : 1, 1, $keyword['year']),
                    mktime(23,59,59, $keyword['month']+1 ? : 12, 0, $keyword['year'])
                ]
            );
        }

        if ($keyword['month']) {

            $query = $query->whereBetween(
                'created_at', 
                [
                    mktime(0,0,0, $keyword['month'], 1, $keyword['year'] ? : date('Y') ), 
                    mktime(23,59,59, $keyword['month']+1, 0, $keyword['year'] ? : date('Y') ) 
                ] 
            );
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
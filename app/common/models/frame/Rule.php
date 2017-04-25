<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/23
 * Time: 上午10:41
 */

namespace app\common\models\frame;


use app\common\models\BaseModel;

class Rule extends BaseModel
{
    public $table = 'rule';

    public $timestamps = false;

    public $attributes = [
        'module'        => 'yun_shop',
        'displayorder'  => 0,
        'status'        => 1
    ];

    protected $guarded = [''];

    /*
     * 通过rid 关键字主键id获取关键字规则详情
     *
     * @param varchar $name [ 模块标识：插件标识：主键ID 】如：sz_yi:designer:7
     *
     *
     * @return object */
    public static function getRuleByName($name)
    {
        return self::uniacid()->where('name', $name)->first();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function attributeNames() {
        return [
            'module'        => 'module字段不能为空\'',
            'displayorder'  => 'displayorder字段不能为空',
            'status'        => 'status字段不能为空',
            'uniacid'       => 'uniacid字段不能为空',
            'name'          => 'name字段不能为空'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'module'        => 'required',
            'displayorder'  => 'required',
            'status'        => 'required',
            'uniacid'       => 'required',
            'name'          => 'required'
        ];
    }
}

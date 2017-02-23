<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 21:33
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;
use Validator;

class TestMember extends  Model
{
    public $table = 'mc_members';

    /**
     * 自定义显示错误信息
     * @return array
     */
    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'min' => ' :attribute不能少于:min!',
        ];
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public static function atributeNames()
    {
        return [
            'title'=> trans('member.title'),
            'body'=>'内容'
        ];
    }

    /**
     * 校验表单数据
     *
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validator($data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required|min:3',
        ],self::validationMessages());

        //自定义字段名
        $validator->setAttributeNames(self::atributeNames());

        return $validator;
    }

}
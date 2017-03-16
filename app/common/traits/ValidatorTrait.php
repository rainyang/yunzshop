<?php

/**
 * 验证Trait类.
 *
 * User: jan
 * Date: 26/02/2017
 * Time: 18:55
 */
namespace app\common\traits;

use Validator;

trait ValidatorTrait
{
    /**
     * 自定义显示错误信息
     * @return array
     */
    public  function validationMessages()
    {
        return trans('validation');
    }

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public  function atributeNames()
    {
        return [];
    }

    /**
     * 字段规则
     * @return array
     */
    public  function rules()
    {
        return [];
    }

    /**
     * 校验表单数据
     *
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public  function validator($data =[])
    {
        $validator = Validator::make($data?:$this->getAttributes(), $this->rules(), $this->validationMessages());

        //自定义字段名
        $validator->setAttributeNames($this->atributeNames());

        return $validator;
    }
}
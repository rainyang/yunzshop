<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 10:11
 */

namespace app\common\services\operation;

class GoodsLog extends OperationBase
{

    public $modules = 'goods';

    public $type = 'edit';


    public $modify_fields;

    public function __construct($model, $type = null)
    {
        parent::__construct($model, $type);
    }


    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
     protected function recordField()
     {
         return [
             'title'        => '商品标题',
             'price'        => '现价',
             'market_price' => '原价',
             'cost_price'   => '成本',
             'type'         => [1=> '实体', 2=>' 虚拟'],
             'is_recommand' => [0=>'取消推荐', 1=>'推荐'],
             'is_new'       => [0=>'取消新品', 1=>'新品'],
             'is_hot'       => [0=>'取消热卖', 1=>'热卖'],
             'is_discount'  => [0=>'取消促销', 1=>'促销'],
         ];
     }

    /**
     * 获取模型修改了哪些字段
     * @param object array
     * @return array
     */
    protected function modifyField($model)
    {

        foreach ($this->recordField() as $key => $item) {

            if ($model->isDirty($key)) {

                 $this->modify_fields[$key]['old_content'] = $model->getOriginal($key);
                 $this->modify_fields[$key]['new_content'] = $model->{$key};
            }
        }
    }

}
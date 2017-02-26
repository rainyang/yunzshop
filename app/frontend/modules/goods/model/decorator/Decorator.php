<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 下午3:35
 */

namespace app\frontend\modules\goods\model;

class Decorator
{
    private $_enable_class_list;
    private $_goods_model;

    public function __construct(GoodsModel $goods_model)
    {
        $this->_goods_model = $goods_model;
        //todo
        $class_list = $this->_getClassList();
        foreach ($class_list as $class) {
            $class::enable($goods_model);
            $this->_enable_class_list[] = $class;
        }
    }

    private static function _getClassList()
    {
        return [
            '\app\frontend\modules\goods\model\type\Verify'
        ];
    }

    public function getData()
    {
        $result = [];
        foreach ($this->_enable_class_list as $class){
            $result += (new $class($this->_goods_model))->getData();
        }
        return $result;
    }
}
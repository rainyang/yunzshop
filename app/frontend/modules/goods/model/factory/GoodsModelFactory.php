<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/23
 * Time: 上午11:16
 */
namespace app\frontend\modules\goods\model\factory;

use app\frontend\modules\goods\model\GoodsModel;

class GoodsModelFactory
{
    private $_processing_model;
    protected $source;
    public function getGoodsModel($total){
        $this->source = $this->_getSourceByORM();
        if(is_array($this->source)){
            foreach ($this->source as $source_item){
                $this->_processing_model = (new GoodsModel($source_item,$total));
                $result[] = $this->_processing_model;
            }
            return $result;
        }
        return (new GoodsModel($this->source));
    }

    public function getProcessingModel(){
        return $this->_processing_model;
    }
}
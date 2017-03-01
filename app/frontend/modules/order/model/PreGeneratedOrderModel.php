<?php
namespace app\frontend\modules\order\model;
use app\frontend\modules\goods\model\GoodsGroupModel;
use app\frontend\modules\member\model\MemberModel;
use app\frontend\modules\order\model\behavior\CreateByGoods;

class PreCreateOrderModel extends OrderModel
{
    protected $source;
    protected $order_model;
    protected $total;
    protected $_goods_group_model;
    protected $member_model;


    public function __construct(GoodsGroupModel $goods_group_model=null)
    {
        if(isset($goods_group_model)){
            $this->addGoodsGroup($goods_group_model);

        }
    }
    public function addGoodsGroup(GoodsGroupModel $goods_group_model){
        $this->source['goods'] = $goods_group_model->getData();
        $this->_goods_group_model = $goods_group_model;
    }
    public function addMember(MemberModel $member_model){
        $this->member_model = $member_model;
    }
    public function getData(){
        //todo 需要确保calculateStatistics 运行过

        $this->calculateStatistics();
        return $this->source;
    }
    public function getGoodsGroup(){

    }
    public function create(){
        $order_makers = (new CreateByGoods($this));
        return $order_makers->create();
    }
    public function calculateStatistics(){
        $this->source['discountprice'] = $this->_goods_group_model->getDiscountPrice();
        $this->source['realprice'] = $this->_goods_group_model->getFinalPrice();
        $this->source['dispatch_price'] = $this->_goods_group_model->getDispatchPrice();
        $this->source['goodsprice'] = $this->_goods_group_model->getMarketPrice();
        $this->source['total'] = $this->_goods_group_model->getTotal();

    }
}
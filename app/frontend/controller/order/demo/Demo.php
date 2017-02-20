<?php
namespace app\frontend\controller\order\confirm;
use app\modules\goods\model\frontend\Goods;

class Display extends Base
{
    //
    public function index(){
         new Goods('');
         //调用模板
    }
    //
    public function ajax(){
        new Goods('');
        //返回json
    }
    //预期重构后效果
    public function demo(){
        //获取商品model

        $db_goods = DB_Goods::find(1);
        $goods = new ModelGoods($db_goods);
        //获取订单model
        $order_model = new ModelOrder();
        $order_model->addGoods($goods);
        $order = $order_model->get();
        //获取分组订单model
        $order_group_model = new ModelOrderGroup($order);
        $order_group = $order_group_model->get();

        return $order_group;
    }
}
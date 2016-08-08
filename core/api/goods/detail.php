<?php
/**
 * 管理后台APP API订单接口
 *
 * PHP version 5.6.15
 *
 * @package   订单模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\goods;
class Detail extends \api\YZ
{
    //private $order_info;
    public function __construct()
    {
        parent::__construct();
        //$api->validate('username','password');
    }

    public function index()
    {
        $para= $this->getPara();
        $goodsid = $para['goods_id'];
        $params = array(':uniacid' => $para['uniacid'], ':goodsid' => $goodsid);
//缩略图,名,价格,原价,库存,销量,描述,编号,条码,减库存方式,红包价格,赠送积分,单次最多购买量,最多购买量,库存,销量
        $fields = 'thumb,title,marketprice,productprice,id,goodssn,content,totalcnf,redprice,credit,maxbuy,usermaxbuy,total,sales';
        $goods = pdo_fetch("SELECT {$fields} FROM " . tablename('sz_yi_goods') . " WHERE id = :id limit 1", array(
            ':id' => $goodsid
        ));
        dump($goods);
        $this->returnSuccess($goods);
    }
}
<?php
/**
 * 管理后台APP API商品状态设置接口
 *
 * PHP version 5.6.15
 *
 * @package   商品模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\controller\goods;
class SetProperty extends \admin\api\YZ
{
    //private $order_info;
    public function __construct()
    {
        parent::__construct();
        //$api->validate('username','password');
    }
    public function setStatus(){
        $this->ca('shop.goods.edit');
        $para = $this->para;
        pdo_update('sz_yi_goods', array(
            'status' => $para['status']
        ), array(
            "id" => $para['goods_id'],
            "uniacid" => $para['uniacid']
        ));
        plog('shop.goods.edit', "修改商品上下架状态   ID: {$para['goods_id']}");
        $this->returnSuccess();
    }
    public function setDelete(){
        $this->ca('shop.goods.delete');
        $para = $this->para;
        $id  = intval($para['goods_id']);
        $row = pdo_fetch("SELECT id, title, thumb FROM " . tablename('sz_yi_goods') . " WHERE id = :id", array(
            ':id' => $id
        ));
        if (empty($row)) {
            $this->returnError('抱歉，商品不存在或是已经被删除！');
        }
        pdo_update('sz_yi_goods', array(
            'deleted' => 1
        ), array(
            'id' => $id
        ));
        plog('shop.goods.delete', "删除商品 ID: {$id} 标题: {$row['title']} ");
        $this->returnSuccess(array(),'删除成功！');
    }
    public function index()
    {
        exit;
        $para = $this->para;
        ca('shop.goods.edit');
        $id = intval($para['id']);
        $type = $para['type'];
        $data = intval($para['data']);
        if (in_array($type, array(
            'new',
            'hot',
            'recommand',
            'discount',
            'time',
            'sendfree',
            'nodiscount'
        ))) {
            $data = ($data == 1 ? '0' : '1');
            pdo_update('sz_yi_goods', array(
                'is' . $type => $data
            ), array(
                "id" => $id,
                "uniacid" => $para['uniacid']
            ));
            if ($type == 'new') {
                $typestr = "新品";
            } else if ($type == 'hot') {
                $typestr = "热卖";
            } else if ($type == 'recommand') {
                $typestr = "推荐";
            } else if ($type == 'discount') {
                $typestr = "促销";
            } else if ($type == 'time') {
                $typestr = "限时卖";
            } else if ($type == 'sendfree') {
                $typestr = "包邮";
            } else if ($type == 'nodiscount') {
                $typestr = "不参与折扣状态";
            }
            plog('shop.goods.edit', "修改商品{$typestr}状态   ID: {$id}");
            $this->returnSuccess($data);
        }

        if (in_array($type, array(
            'type'
        ))) {
            $data = ($data == 1 ? '2' : '1');
            pdo_update('sz_yi_goods', array(
                $type => $data
            ), array(
                "id" => $id,
                "uniacid" => $para['uniacid']
            ));
            plog('shop.goods.edit', "修改商品类型   ID: {$id}");
            $this->returnSuccess($data);
        }
        $this->returnError();
    }
}
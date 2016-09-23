<?php
/**
 * 消费者model
 *
 * 管理后台 APP API 订单model
 *
 * @package   订单模块
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class member
{
    public function __construct()
    {

    }

    /**
     * 获取订单详情
     *
     * 详细描述（略）
     * @param string $para 查询条件数组
     * @return array 订单详情数组
     */
    public function getCount($para){
        
        $count = pdo_fetch("select count(*) from " . tablename('sz_yi_member') . " where  createtime>:createtime and uniacid=:uniacid limit 1", array(
            ':uniacid' => $para['uniacid'],
            ':createtime' => $para['createtime']
        ));
        return current($count);
    }

}

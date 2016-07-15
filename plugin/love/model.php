<?php
/*=============================================================================
#     FileName: model.php
#         Desc: 基金model类
#       Author: ym
#      Version: 0.0.1
#   LastChange: 2016-07-14
=============================================================================*/
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('LoveModel')) {
	class LoveModel extends PluginModel
	{
		public function getSet()
		{
			$set = parent::getSet();
			$set['texts'] = array();
			return $set;
		}

		/**
	     * 处理爱心基金购物捐赠
	     * @param goods_where 处理的订单条件
	     * @param openid 用户标示
	     * @param become_order 统计方式 0为支付 1为完成
	     * modify ym 2016.7.14
	     */
        public function checkOrder($goods_where, $openid, $become_order){
            global $_W;
            $set = $this->getSet();
            if(!empty($set['start']) && intval($set['become_order']) == $become_order){
                $goods = pdo_fetchall('SELECT g.id, g.love_money FROM ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_goods') . ' g on og.goodsid = g.id ' . ' where ' . $goods_where);
                foreach ($goods as $key => $val) {
                    if($val['love_money'] > 0){
                        $memberid = pdo_fetchcolumn("select id from " .tablename('sz_yi_member'). "  where openid=:openid and uniacid=:uniacid",array(':openid'=>$openid, ':uniacid'=>$_W['uniacid']));
                        $love_data = array(
                            'uniacid' => $_W['uniacid'],
                            'mid' => $memberid,
                            'openid' => $openid,
                            'money' => $val['love_money'],
                            'goodsid' => $val['id'],
                            'paymonth' => 2,
                            'type' => 1,
                            'createtime' => time()
                        );
                        pdo_insert('sz_yi_love_log', $love_data);
                    }
                }  
            }
        }

	}
}

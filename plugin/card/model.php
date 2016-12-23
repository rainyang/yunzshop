<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
if (!class_exists('CardModel')) {
	class CardModel extends PluginModel
	{
        public function getSet()
        {
            $set = parent::getSet();
            $set['gift_title'] = empty($set['gift_title'])?'代金卡':$set['gift_title'];
            return $set;
        }

		public function getCdkey($len=16){
            $chars = array(
                "A", "B", "C", "D", "E", "F", "G",
                "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
                "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
                "3", "4", "5", "6", "7", "8", "9"
            );
            $charsLen = count($chars) - 1;
            shuffle($chars);
            $output = "";
            for ($i=0; $i<$len; $i++)
            {
                $output .= $chars[mt_rand(0, $charsLen)];
            }
            return $output;
        }

        public function verifyCDkey($cdkey)
        {
            global $_W;
            if (empty($cdkey)) {
                return array();
            }
            $result = pdo_fetch("SELECT * FROM " . tablename('sz_yi_card_data') . " WHERE uniacid=:uniacid AND cdkey=:cdkey", array(
                ':uniacid'  => $_W['uniacid'],
                ':cdkey'    => $cdkey
            ));
            if (empty($result)) {
                return array();
            } else {
                return $result;
            }
        }

        /**
         * @name 所有代金卡
         * @author yangyang
         * @param $openid
         */
        public function getAllCard($openid)
        {
            global $_W;
            $all_card = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_card_data') . " WHERE uniacid=:uniacid AND openid=:openid", array(
                ':uniacid'  => $_W['uniacid'],
                ':openid'   => $openid
            ));
            if (!empty($all_card)) {
                foreach ($all_card as $c) {
                    $this->checkValidity($c['id']);
                }
            }
        }

        /**
         * @name 检查代金卡信息,验证是否过期,并更改数据库
         * @author yangyang
         * @param $id 代金卡id
         * @return bool
         */
        public function checkValidity($id)
        {
            global $_W;
            $time = time();
            if (empty($id)) {
                return;
            }
            $card_info = $this->getCradInfo($id);
            if (empty($card_info)) {
                return;
            }
            if ($card_info['isoverdue'] == 1) {
                return $card_info['isoverdue'];
            }
            if ($card_info['isday'] == 1) {
                $isoverdue = (($card_info['validity_period'] + $card_info['bindtime'])<$time)?0:1;
            } else if ($card_info['isday'] == 2) {
                $isoverdue = ($card_info['timeend']>$time)?0:1;
            }
            if ($isoverdue == 1) {
                pdo_update('sz_yi_card_data',
                    array(
                        'isoverdue' => $isoverdue
                    ),
                    array(
                        'uniacid'   => $_W['uniacid'],
                        'id'        => $id
                    )
                );
            }
            return $isoverdue;
        }

        /**
         * @name 获取代金卡详情
         * @author yangyang
         * @param $id
         * @return array()
         */
        public function getCradInfo($id)
        {
            global $_W;
            if (empty($id)) {
                return;
            }
            $card_info = pdo_fetch("SELECT * FROM " . tablename('sz_yi_card_data') . " WHERE uniacid=:uniacid AND id=:id", array(
                ':uniacid'  => $_W['uniacid'],
                ':id'       => $id
            ));
            return $card_info;
        }

        /**
         * @name 获取满足条件的代金卡数量
         * @author yangyang
         * @param $openid
         * @return int
         */
        public function consumeCardCount($openid)
        {
            global $_W;

            $sqlcount = "SELECT count(1) FROM " . tablename('sz_yi_card_data') . " WHERE openid=:openid AND uniacid=:uniacid AND isoverdue=0 AND balance>0";
            $total = pdo_fetchcolumn($sqlcount, array(':openid' => $openid, ':uniacid' => $_W['uniacid']));

            return $total;
        }
	}
}

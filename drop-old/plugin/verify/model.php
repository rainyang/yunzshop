<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('VerifyModel')) {
    class VerifyModel extends PluginModel
    {
        public function createQrcode($orderid = 0)
        {
            global $_W, $_GPC;
            $path = IA_ROOT . "/addons/sz_yi/data/qrcode/" . $_W['uniacid'];
            if (!is_dir($path)) {
                load()->func('file');
                mkdirs($path);
            }
            $url         = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=verify&method=detail&id=' . $orderid;
            $file        = 'order_verify_qrcode_' . $orderid . '.png';
            $qrcode_file = $path . '/' . $file;
            if (!is_file($qrcode_file)) {
                require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
                QRcode::png($url, $qrcode_file, QR_ECLEVEL_H, 4);
            }
            return $_W['siteroot'] . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'] . '/' . $file;
        }
        public function perms()
        {
            return array(
                'verify' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'keyword' => array(
                            'text' => '关键词设置-log'
                        ),
                        'store' => array(
                            'text' => '门店',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        ),
                        'saler' => array(
                            'text' => '核销员',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        ) ,
                        'withdraw' => array(
                            'text' => '提现',
                            'view' => '浏览',
                            'add' => '添加-log',
                            'edit' => '修改-log',
                            'delete' => '删除-log'
                        )
                    )
                )
            );
        }
        public function getInfo($storeid = 0)
        {
            global $_W, $_GPC;
            $store_info = pdo_fetch("SELECT * FROM ".tablename('sz_yi_store')." WHERE id=:storeid and uniacid=:uniacid", array(':storeid' => $storeid, ':uniacid' => $_W['uniacid']));
            return $store_info;
        }
        //获取门店成交总额
        public function getTotalPrice($storeid = 0)
        {
            global $_W, $_GPC;
            $store_price = pdo_fetchcolumn("SELECT sum(price)  FROM ".tablename('sz_yi_order')." WHERE storeid=:storeid and uniacid=:uniacid and status = 3 ", array(':storeid' => $storeid, ':uniacid' => $_W['uniacid']));
            return $store_price;
        }
        public function getTotal($storeid = 0)
        {
            global $_W, $_GPC;
            $order = pdo_fetchall(" SELECT * FROM ".tablename('sz_yi_order')." WHERE storeid=:id and uniacid=:uniacid ", array(':uniacid' => $_W['uniacid'], ':id' => $storeid));
            $ordercount = count($order);
            return $ordercount;
        }
        public function getWithdrawed($storeid = 0)
        {
            global $_W, $_GPC;
            $totalwithdraw = pdo_fetchall('SELECT money FROM ' . tablename('sz_yi_store_withdraw') . ' WHERE uniacid = :uniacid AND store_id = :id AND status >= 0 and status < 2', array(':uniacid' => $_W['uniacid'], ':id' => $storeid));
            $totalwithdraws = 0;
            foreach ($totalwithdraw as  $value) {
                $totalwithdraws += $value['money'];
            }
            return $totalwithdraws;
        }
        //门店比例计算之后的总金额
        public function getRealPrice($storeid = 0)
        {
            global $_W, $_GPC;
            $store_info = $this->getInfo($storeid);
            $realprice = 0;
            $store_price = pdo_fetchcolumn("SELECT sum(realprice) FROM ".tablename('sz_yi_order')." WHERE storeid=:storeid and uniacid=:uniacid and status = 3 and paytype <> 4 ", array(':storeid' => $storeid, ':uniacid' => $_W['uniacid']));


            return $store_price;
        }

    }
}

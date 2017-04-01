<?php
if (!pdo_fieldexists('sz_yi_order', 'trade_no')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_order')." ADD  `trade_no` VARCHAR( 255 ) DEFAULT '' COMMENT '支付宝交易号';");
}
if (!pdo_fieldexists('sz_yi_order_refund', 'batch_no')) {
    pdo_fetchall("ALTER TABLE  ".tablename('sz_yi_order_refund')." ADD  `batch_no` VARCHAR( 255 ) DEFAULT '' COMMENT '支付宝批次号';");
}
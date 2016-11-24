<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/11/23
 * Time: 上午4:27
 */

if (!pdo_fieldexists('sz_yi_goods', 'opt_switch')) {
    pdo_fetchall("ALTER TABLE ".tablename('sz_yi_goods')." ADD `opt_switch` tinyint DEFAULT 0;");
}

message('商品规格属性更新成功', $this->createWebUrl('shop/goods'), 'success');
<?php
namespace mobile\order\confirm;
class Create extends Base {
    public function index(){
        global $_GPC;

        $id = intval($_GPC["order"][0]["id"]);
        $telephone = intval($_GPC['telephone']) ? intval($_GPC['telephone']) : '';

    }
}
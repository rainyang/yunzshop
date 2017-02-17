<?php
namespace mobile\order;
class Step extends Base{
    public function index(){
        global $_GPC;
        $express = trim($_GPC["express"]);
        $expresssn = trim($_GPC["expresssn"]);
        $content = getExpress($express, $expresssn);
        if (!$content) {
            $content = getExpress($express, $expresssn);
            if (!$content) {
                return show_json(1, array('list' => array()));
            }
        }
        foreach ($content as $data) {
            $list[] = array('time' => $data->time, 'step' => $data->context, 'ts' => $data->time);
        }
        return show_json(1, array('list' => $list));
    }
}
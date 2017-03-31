<?php
namespace \addoons\sz_yi\core\mobile\order\express;
require __DIR__.'/base.php';

class Step extends Base {
    public function run() {
        $express = trim($_GPC['express']);
        $expresssn = trim($_GPC['expresssn']);
        $content = getExpress($express, $expresssn);
        if (!$content) {
            $content = getExpress($express, $expresssn); //todo 和前面代码重复了,为什么
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

$class = new Step();
$class->run();
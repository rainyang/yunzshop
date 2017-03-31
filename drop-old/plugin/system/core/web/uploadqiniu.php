<?php
set_time_limit(0);
global $_W, $_GPC;
if(!empty($_GPC["submit"])){
    $page = intval($_GPC["page"]);
    $startpage = 1 * $page;
    $endpage = $startpage + 100;
    $SQL = "SELECT id, thumb FROM " . tablename("sz_yi_goods") . " WHERE thumb like :thumb AND uniacid = :uniacid order by id desc limit {$startpage}, {$endpage}";
    $goods = pdo_fetchall($SQL, array(":uniacid" => $_W["uniacid"], ":thumb" => "images%"));
    foreach ($goods as $good) {

        $data['thumb'] = save_media($good['thumb']);
        pdo_update('sz_yi_goods', $data, array(
            'id' => $good['id']
        ));
    }
    message('上传七牛完成!', $this->createPluginWebUrl('system/uploadqiniu'), 'success');
}
load()->func('tpl');
include $this->template('uploadqiniu');
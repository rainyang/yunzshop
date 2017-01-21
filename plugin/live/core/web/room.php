<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/12/19
 * Time: 下午5:45
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$operation   = !empty($_GPC['op']) ? $_GPC['op'] : 'display' ;

$domain = 'http://sy.yunzshop.com';

load()->func('communication');
if ($operation == 'display') {
    $keyword = !empty($_GPC['keyword']) ? $_GPC['keyword'] : '';
    $room_id = !empty($_GPC['room_id']) ? $_GPC['room_id'] : '';

    $remote_url = $domain . '/admin_live.php?api=room/Group';
    $params = array('domain'=>$_SERVER['HTTP_HOST'], 'uniacid'=>$_W['uniacid'], 'keyword'=>$keyword, 'room_id'=>$room_id);

    $res = ihttp_request($remote_url, $params);

    if ($res['code'] == 200 && !empty($res['content'])) {
        $list = json_decode($res['content'],1);
    } else {
        $list = array();
    }

} elseif ($operation == 'detail') {
    if ($_W['ispost']) {
        $remote_url = $domain . '/admin_live.php?api=room/Property';

        $params = array('room_id'=>$_GPC['room_id'], 'is_hot'=>$_GPC['is_hot'], 'is_recommand'=>$_GPC['is_recommand'], 'status'=>$_GPC['status']);

        $res = ihttp_request($remote_url, $params);

        if ($res['code'] == 200 && !empty($res['content'])) {
            $data = json_decode($res['content'],1);

            if ($data['result'] == 1) {
                message('房间更新成功!','', 'success');
            } else {
                message('房间更新失败!','', 'error');
            }
        } else {
            message('房间更新失败!','', 'error');
        }
    } else {
        $remote_url = $domain . '/admin_live.php?api=room/Get';

        $params = array('room_id'=>$_GPC['id']);

        $res = ihttp_request($remote_url, $params);

        if ($res['code'] == 200 && !empty($res['content'])) {
            $info = json_decode($res['content'],1);
        } else {
            $info = array();
        }
    }
} elseif ($operation == 'goods') {
    $remote_url = $domain . '/admin_live.php?api=room/Get';

    $params = array('room_id'=>$_GPC['id']);

    $res = ihttp_request($remote_url, $params);

    if ($res['code'] == 200 && !empty($res['content'])) {
        $info = json_decode($res['content'],1);
    } else {
        $info = array();
    }

    $lang = array(
        "putaway"   => "上架",
        "soldout"   => "下架",
        "good"      => "商品",
        "price"     => "价格",
        "repertory" => "库存",
    );
    $remote_url = $domain . '/admin_live.php?api=room/Goods';

    $params = array('room_id'=>$_GPC['id']);
    $res = ihttp_request($remote_url, $params);

    if ($res['code'] == 200 && !empty($res['content'])) {
        $goods = json_decode($res['content'],1);
            if(!empty($goods['data']['list'])){
                foreach ($goods['data']['list'] as $val) {
                $item[] = $val['goods_id'];
            }

            $str_gid = implode(',', $item);
            $sql = 'SELECT * FROM ' . tablename('sz_yi_goods')  . ' WHERE id in (' . $str_gid . ') ORDER BY `status` DESC, `displayorder` DESC,`id` DESC';
            $sqls = 'SELECT COUNT(id) FROM ' . tablename('sz_yi_goods') . ' WHERE id in (' . $str_gid . ')';
            $total = pdo_fetchcolumn($sqls, $params);

            $list = pdo_fetchall($sql, $params);
        } 
    }
}

load()->func('tpl');
include $this->template('room');
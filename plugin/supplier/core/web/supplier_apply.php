<?php
global $_W, $_GPC;
$operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($operation == 'display') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $where = '';
    if (!empty($_GPC['uid'])) {
        $where .= ' and p.uid=' . $_GPC['uid'];
    }
    if (!empty($_GPC['applysn'])) {
        $where .= " and a.applysn='" . $_GPC['applysn'] . "'";
    }
    //提现列表
    $list = pdo_fetchall('select a.*,p.accountname, mobile as telephone, accountbank, banknumber   from ' . tablename('sz_yi_supplier_apply') . ' a left join ' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid where a.status=0 and p.uniacid=' . $_W['uniacid'] . $where . '  limit ' . ($pindex - 1) * $psize . ',' . $psize);
    //总数
    $total = pdo_fetchcolumn('select count(a.id) from ' . tablename('sz_yi_supplier_apply') . ' a left join ' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid where a.status=0 and p.uniacid=' . $_W['uniacid'] . $where);
    //分页
    $pager = pagination($total, $pindex, $psize);
} else {
    if ($operation == 'detail') {
        $id = intval($_GPC['applyid']);
        if (!empty($id)) {
            $set = m('common')->getSysset('shop');
            $apply = pdo_fetch('select * from ' . tablename('sz_yi_supplier_apply') . ' where id = ' . $id);
            $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',
                array(':uid' => $apply['uid'], ':uniacid' => $_W['uniacid']));
            if ($apply['type'] == 2) {
                $result = m('finance')->pay($openid, 1, $apply['apply_money'] * 100, $apply['applysn'],
                    $set['name'] . '供应商提现');
                if (is_error($result)) {
                    message('微信钱包提现失败: ' . $result['message'], '', 'error');
                }
                m('notice')->sendMemberLogMessage($apply['id']);
            }
            $data = array(
                'status' => 1,
                'finish_time' => time()
            );
            pdo_update('sz_yi_supplier_apply', $data, array(
                'id' => $id
            ));
            pdo_query('update ' . tablename('sz_yi_order_goods') . " set supplier_apply_status=1 where id in ({$apply['apply_ordergoods_ids']})");
            $msg = $apply['type'] == 1 ? '手动打款成功' : '提现到微信钱包成功!';
            p('supplier')->sendMessage($openid, array('money' => $apply['apply_money'], 'type' => $msg),
                TM_SUPPLIER_PAY);
            message($msg, $this->createPluginWebUrl('supplier/supplier_apply'), 'success');
        }
    }
}
load()->func('tpl');
include $this->template('supplier_apply');

<?php
/*=============================================================================
#     FileName: category.php
#         Desc:  
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:39:24
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_GPC, $_W;

$shopset   = m('common')->getSysset('shop');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$children  = array();
$category  = pdo_fetchall("SELECT * FROM " . tablename('sz_yi_store_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
foreach ($category as $index => $row) {
    if (!empty($row['parentid'])) {
        $children[$row['parentid']][] = $row;
        unset($category[$index]);
    }
}
if ($operation == 'display') {
    ca('verify.store.view');
    if (!empty($_GPC['datas'])) {
        ca('verify.store.edit');
        $datas = json_decode(html_entity_decode($_GPC['datas']), true);
        if (!is_array($datas)) {
            message('分类保存失败，请重试!', '', 'error');
        }
        $cateids      = array();
        $displayorder = count($datas);
        foreach ($datas as $row) {
            $cateids[] = $row['id'];
            pdo_update('sz_yi_store_category', array(
                'parentid' => 0,
                'displayorder' => $displayorder,
                'level' => 1
            ), array(
                'id' => $row['id']
            ));
            if ($row['children'] && is_array($row['children'])) {
                $displayorder_child = count($row['children']);
                foreach ($row['children'] as $child) {
                    $cateids[] = $child['id'];
                    pdo_query('update ' . tablename('sz_yi_store_category') . ' set  parentid=:parentid,displayorder=:displayorder,level=2 where id=:id', array(
                        ':displayorder' => $displayorder_child,
                        ":parentid" => $row['id'],
                        ":id" => $child['id']
                    ));
                    $displayorder_child--;
                    if ($child['children'] && is_array($child['children'])) {
                        $displayorder_third = count($child['children']);
                        foreach ($child['children'] as $third) {
                            $cateids[] = $third['id'];
                            pdo_query('update ' . tablename('sz_yi_store_category') . ' set  parentid=:parentid,displayorder=:displayorder,level=3 where id=:id', array(
                                ':displayorder' => $displayorder_third,
                                ":parentid" => $child['id'],
                                ":id" => $third['id']
                            ));
                            $displayorder_third--;
                        }
                    }
                }
            }
            $displayorder--;
        }
        if (!empty($cateids)) {
            pdo_query('delete from ' . tablename('sz_yi_store_category') . ' where id not in (' . implode(',', $cateids) . ') and uniacid=:uniacid', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        plog('verify.store.edit', '批量修改分类的层级及排序');
        message('分类更新成功！', $this->createPluginWebUrl('verify/category', array(
            'op' => 'display'
        )), 'success');
    }
} elseif ($operation == 'post') {
    $parentid = intval($_GPC['parentid']);
    $id       = intval($_GPC['id']);
    if (!empty($id)) {
        ca('verify.store.edit|verify.store.view');
        $item     = pdo_fetch("SELECT * FROM " . tablename('sz_yi_store_category') . " WHERE id = '$id' limit 1");
        $parentid = $item['parentid'];
    } else {
        ca('verify.store.add');
        $item = array(
            'displayorder' => 0
        );
    }
    if (!empty($parentid)) {
        $parent = pdo_fetch("SELECT id, parentid, name FROM " . tablename('sz_yi_store_category') . " WHERE id = '$parentid' limit 1");
        if (empty($parent)) {
            message('抱歉，上级分类不存在或是已经被删除！', $this->createPluginWebUrl('post'), 'error');
        }
        if (!empty($parent['parentid'])) {
            $parent1 = pdo_fetch("SELECT id, name FROM " . tablename('sz_yi_store_category') . " WHERE id = '{$parent['parentid']}' limit 1");
        }
    }
    if (empty($parent)) {
        $level = 1;
    } else {
        if (empty($parent['parentid'])) {
            $level = 2;
        } else {
            $level = 3;
        }
    }
    if (checksubmit('submit')) {
        if (empty($_GPC['catename'])) {
            message('抱歉，请输入分类名称！');
        }
        $data = array(
            'uniacid' => $_W['uniacid'],
            'name' => trim($_GPC['catename']),
            'enabled' => intval($_GPC['enabled']),
            'displayorder' => intval($_GPC['displayorder']),
            'isrecommand' => intval($_GPC['isrecommand']),
            'ishome' => intval($_GPC['ishome']),
            'description' => $_GPC['description'],
            'parentid' => intval($parentid),
            'thumb' => save_media($_GPC['thumb']),
            'advimg' => save_media($_GPC['advimg']),
            'advimg_pc' => save_media($_GPC['advimg_pc']),
            'advurl' => trim($_GPC['advurl']),
            'advurl_pc' => trim($_GPC['advurl_pc']),
            'level' => $level
        );
        if (!empty($_GPC['advtitle1'])) {
            $data['advtitle1'] = trim($_GPC['advtitle1']);
        }
        if (!empty($_GPC['advtitle2'])) {
            $data['advtitle2'] = trim($_GPC['advtitle2']);
        }
        if (!empty($_GPC['advtitle3'])) {
            $data['advtitle3'] = trim($_GPC['advtitle3']);
        }
        if (!empty($_GPC['advtitle4'])) {
            $data['advtitle4'] = trim($_GPC['advtitle4']);
        }

        if (!empty($id)) {
            unset($data['parentid']);
            pdo_update('sz_yi_store_category', $data, array(
                'id' => $id
            ));
            load()->func('file');
            file_delete($_GPC['thumb_old']);
            plog('verify.store.edit', "修改分类 ID: {$id}");
        } else {
            pdo_insert('sz_yi_store_category', $data);
            $id = pdo_insertid();
            plog('verify.store.add', "添加分类 ID: {$id}");
        }
        message('更新分类成功！', $this->createPluginWebUrl('verify/category', array(
            'op' => 'display'
        )), 'success');
    }
} elseif ($operation == 'delete') {
    ca('verify.store.delete');
    $id   = intval($_GPC['id']);
    $item = pdo_fetch("SELECT id, name, parentid FROM " . tablename('sz_yi_store_category') . " WHERE id = '$id'");
    if (empty($item)) {
        message('抱歉，分类不存在或是已经被删除！', $this->createPluginWebUrl('verify/category', array(
            'op' => 'display'
        )), 'error');
    }
    pdo_delete('sz_yi_store_category', array(
        'id' => $id,
        'parentid' => $id
    ), 'OR');
    plog('verify.store.delete', "删除分类 ID: {$id} 分类名称: {$item['name']}");
    message('分类删除成功！', $this->createPluginWebUrl('verify/category', array(
        'op' => 'display'
    )), 'success');
}
load()->func('tpl');
include $this->template('category');

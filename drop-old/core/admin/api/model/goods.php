<?php
/**
 * 商品model
 *
 * 管理后台 APP API 商品model
 *
 * @package   订单模块
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace admin\api\model;
if (!defined('IN_IA')) {
    exit('Access Denied');
}

class goods
{
    public function __construct()
    {
        
    }

    public function getList($para, $fields)
    {
        $condition[] = 'WHERE 1';
        $params = array();
        if (isset($para['keyword']) && $para['keyword'] !== '') {
            $para['keyword'] = trim($para['keyword']);
            $condition['title'] = ' AND `title` LIKE :title';
            $params[':title'] = '%' . trim($para['keyword']) . '%';
        }
        if (!empty($para['id'])) {
            $condition['id'] = " AND id < {$para['id']}";
        }
        if (isset($para['status']) && $para["status"] != '') {
            $condition['status'] = ' AND `status` = :status';
            $params[':status'] = intval($para['status']);
        }
        if (isset($para['ccate']) && $para["ccate"] != '') {
            $condition['ccate'] = ' AND (`ccate` = :ccate or ccates = :ccate)';
            $params[':ccate'] = intval($para['ccate']);
        }
        if (isset($para['pcate']) && $para["pcate"] != '') {
            $condition['pcate'] = ' AND (`pcate` = :pcate or pcates = :pcate)';
            $params[':pcate'] = intval($para['pcate']);
        }
        $condition['other'] = ' AND`uniacid` = :uniacid AND `deleted` = :deleted';
        $params += array(
            ':uniacid' => $para['uniacid'],
            ':deleted' => '0'
        );
        $condition['supplier_uid'] = $this->getSupplierCondition($para['uid'], $para['uniacid']);
        $condition_str = implode(' ', $condition);

        $sql = "SELECT {$fields} FROM " . tablename('sz_yi_goods') . $condition_str;
        $sql .= 'ORDER BY `id` DESC,`status` DESC, `displayorder` DESC  ';

        $list = pdo_fetchall($sql, $params);
        foreach ($list as &$goods_item) {
            $goods_item = $this->formatGoodsInfo($goods_item);
        }
        return $list;
    }

    private function formatGoodsInfo($goods_info)
    {
        $goods_info = set_medias($goods_info, "thumb");
        return $goods_info;
    }

    public function getCateTree($uniacid)
    {
        $tree = pdo_fetchall("SELECT p.id AS pcate,p.name,CONCAT(\"[\",GROUP_CONCAT(item),\"]\") AS ccate_list FROM " . tablename('sz_yi_category') . " p
                      LEFT JOIN (SELECT parentid,CONCAT('{\"ccate\":\"',id,'\",\"name\":\"',name,'\"}') item 
                            FROM " . tablename('sz_yi_category') . " WHERE level=2) c ON p.id=c.parentid WHERE uniacid = '{$uniacid}'
                      GROUP BY pcate
                ");
        foreach ($tree as &$item) {
            $ccate_list = json_decode($item['ccate_list'], true);
            $item['ccate_list'] = $ccate_list ? $ccate_list : array();
            array_unshift($item['ccate_list'], array(
                    'ccate' => '',
                    'name' => '全部',
                )
            );
        }
        array_unshift($tree, array(
                'pcate' => '',
                'name' => '全部',
                'ccate_list' => array(
                    array(
                        'ccate' => '',
                        'name' => '全部'
                    )
                )
            )
        );
        return $tree;
    }

    private function getSupplierCondition($uid, $uniacid)
    {
        $condition = '';
        if (p('supplier')) {
            $suproleid = pdo_fetchcolumn('select id from' . tablename('sz_yi_perm_role') . ' where status1 = 1');
            $userroleid = pdo_fetchcolumn('select roleid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid', array(':uid' => $uid, ':uniacid' => $uniacid));

            //Author:RainYang Date:2016-04-09 Content:修改供应商判断条件,有可能上面两个id都是空的情况,照成商品不显示
            if ((!empty($userroleid)) && ($userroleid == $suproleid)) {
                $condition = 'AND supplier_uid=' . $uid;
            }
        }
        return $condition;
    }
}
